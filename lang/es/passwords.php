<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de lenguaje para restablecimiento de contraseña
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas son los mensajes predeterminados que coinciden con
    | las razones dadas por el gestor de contraseñas cuando falla un intento
    | de actualización debido a una contraseña inválida o token incorrecto.
    |
    */

    'reset' => 'Tu contraseña ha sido restablecida correctamente.',
    'sent' => 'Hemos enviado por email el enlace para restablecer tu contraseña.',
    'throttled' => 'Por favor espera antes de intentarlo nuevamente.',
    'token' => 'El token para restablecer contraseña no es válido.',
    'user' => "No encontramos ningún usuario con esa dirección de correo electrónico.",

    /*
    |--------------------------------------------------------------------------
    | Mensajes adicionales recomendados (opcionales)
    |--------------------------------------------------------------------------
    */
    'subject' => 'Notificación de restablecimiento de contraseña',
    'greeting' => '¡Hola!',
    'action_text' => 'Restablecer contraseña',
    'expire_notice' => 'Este enlace expirará en :count minutos.',
    'no_action_required' => 'Si no solicitaste un restablecimiento, ignora este mensaje.',
];
