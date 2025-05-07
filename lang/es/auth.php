<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Líneas de lenguaje para autenticación
    |--------------------------------------------------------------------------
    |
    | Las siguientes líneas de lenguaje son usadas durante la autenticación para
    | varios mensajes que necesitamos mostrar al usuario. Eres libre de
    | modificar estas líneas de acuerdo a los requerimientos de tu aplicación.
    |
    */

    'failed' => 'Estas credenciales no coinciden con nuestros registros.',
    'throttle' => 'Demasiados intentos de inicio de sesión. Por favor intente nuevamente en :seconds segundos.',
    'unauthenticated' => 'No autenticado',
    'unauthorized' => 'No autorizado',
    'logout_success' => 'Sesión cerrada exitosamente',
    'password' => [
        'mismatch' => 'La contraseña anterior no coincide con nuestros registros.',
        'update_error' => 'Ocurrió un error al actualizar la contraseña.',
        'update_success' => 'Contraseña actualizada exitosamente.',
        'same_password' => 'La nueva contraseña debe ser diferente a la anterior.'
    ],
    'recovery' => [
        'email_sent' => 'Correo de recuperación enviado exitosamente.',
        'invalid_code' => 'Código de verificación inválido.',
        'code_validated' => 'Código validado exitosamente.'
    ],
    'validation_error' => 'Error de validación.',
    'email_not_verified' => 'Tu dirección de correo no está verificada.',
    'email_already_verified' => 'El correo electrónico ya está verificado.',
    'verification_email_sent' => 'Correo de verificación enviado.',
    'verify_email_success' => 'Correo electrónico verificado exitosamente.',
    'invalid_user_id' => 'ID de usuario inválido.',
    'invalid_verification_link' => 'Enlace de verificación inválido.',
];
