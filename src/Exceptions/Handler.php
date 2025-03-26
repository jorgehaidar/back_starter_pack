<?php

namespace App\Exceptions;

use App\Models\Security\ErrorLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use Throwable;
use Illuminate\Support\Facades\Mail;
use App\Mail\ErrorOccurredMail;
use Illuminate\Http\Request;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * Reporta la excepción.
     *
     * @param Throwable $e
     * @return void
     * @throws Throwable
     */
    public function report(Throwable $e): void
    {
        if (!property_exists($e, 'error_id')) {
            $status = $e->getCode() ?: 500;

            $errorLog = $this->logError(request(), $e, $status);
            if ($errorLog) {
                $e->error_id = $errorLog->id;
            }

            $this->sendErrorEmail($e);
        }

        parent::report($e);
    }

    /**
     * Renderiza la respuesta de la excepción.
     *
     * @param Request $request
     * @param Throwable $e
     * @return JsonResponse
     */
    public function render($request, Throwable $e): JsonResponse
    {
        $status = 500;
        $response = [
            'success' => false,
            'message' => 'An error occurred. Please contact support with the error ID.',
            'error_id' => $e->error_id ?? null,
        ];

        if ($e instanceof ValidationException) {
            return Response::json([
                'success' => false,
                'message' => $e->getMessage(),
                'errors' => $e->errors(),
            ], 422);
        }

        if ($e instanceof NotFoundHttpException) {
            $status = 404;
            $response['message'] = 'Resource not found.';
        }

        if ($e instanceof QueryException) {
            $status = 500;
            $response['message'] = 'A database error occurred. Please try again later.';
        }

        return Response::json($response, $status);
    }

    /**
     * Loguea el error en la base de datos.
     *
     * @param Request $request
     * @param Throwable $e
     * @param int $status
     * @return ErrorLog|null
     */
    protected function logError(Request $request, Throwable $e, int $status): ?ErrorLog
    {
        return ErrorLog::create([
            "description" => $e->getMessage(),
            "error" => json_encode([
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]),
            "parameters" => json_encode($request->json()->all()),
            "path" => $request->path(),
            "headers" => json_encode($request->header()),
            "ip" => $request->getClientIp(),
            "user_id" => optional(auth()->user())->id ?? 'Not Logged',
            "username" => optional(auth()->user())->name ?? 'Not Logged',
            "status_code" => $status,
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }

    /**
     * Determina si el error debe ser registrado.
     *
     * @param Throwable $e
     * @return bool
     */
    protected function shouldLogError(Throwable $e): bool
    {
        return !in_array($e->getCode(), explode(',', env('NO_LOG_ERROR', '*')));
    }

    /**
     * Envía un correo de error a los desarrolladores.
     *
     * @param Throwable $e
     * @return void
     */
    protected function sendErrorEmail(Throwable $e): void
    {
        $emailDev = env('EMAIL_DEV');

        if ($emailDev) {
            $request = request();
            $user = auth()->user();

            $errorData = [
                'error_id' => $e->error_id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'path' => $request->path(),
                'method' => $request->method(),
                'ip' => $request->getClientIp(),
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ] : 'Guest',
                'payload' => $request->all(),
            ];

            Mail::to($emailDev)->send(new ErrorOccurredMail($errorData));
        }
    }
}
