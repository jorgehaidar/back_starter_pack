<?php

namespace Mbox\BackCore\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mbox\BackCore\Services\CoreService;
use Illuminate\Support\Facades\Response;
use Throwable;

abstract class CoreController
{
    protected CoreService $service;

    /**
     * @throws Throwable
     */
    public function index(Request $request): JsonResponse
    {
        $params = $request->all();

        $result = $this->service->getAll($params);

        return Response::json([
            'success' => true,
            'message' => 'Resources retrieved successfully.',
            'data' => $result,
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            $result = $this->service->create($params);

            if ($result['success']) {
                DB::commit();
                return Response::json($result, 201);
            }

            DB::rollBack();
            return Response::json($result, 400);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function show(string $id, Request $request): JsonResponse
    {
        $params = $request->all();
        $result = $this->service->getById($id, $params);
        return Response::json($result, $result['status']);
    }

    /**
     * @throws Throwable
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $params = $request->all();
            $result = $this->service->update($id, $params);

            if ($result['success']) {
                DB::commit();
                return Response::json($result, 201);
            }

            DB::rollBack();
            return Response::json($result, 400);

        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();
            $result = $this->service->deleteById($id);

            if ($result['success']) {
                DB::commit();
                return Response::json($result, 201);
            }

            DB::rollBack();
            return Response::json($result, 400);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function deleteMultiple(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $result = $this->service->deleteMultiple($request->all());

            if ($result['success']) {
                DB::commit();
                return Response::json($result, 201);
            }

            DB::rollBack();
            return Response::json($result, 400);
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
