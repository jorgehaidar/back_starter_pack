<?php

return [
    'models' => [
        'users' => 'usuarios',
        'password_reset_tokens' => 'tokens de restablecimiento',
        'api_keys' => 'claves API',
        'routes' => 'rutas',
        'roles' => 'roles',
        'role_user' => 'asignación de roles',
        'permissions' => 'permisos',
        'logs' => 'registros',
        'error_logs' => 'logs de errores',
        'rate_limit_blocks' => 'bloqueos por límite',
        'wallets' => 'monederos',
        'clients' => 'clientes',
        'vehicle_types' => 'tipos de vehículo',
        'vehicles' => 'vehículos',
        'drivers' => 'conductores',
        'notifications' => 'notificaciones',
        'commissions' => 'comisiones',
        'addresses' => 'direcciones',
        'trips' => 'viajes',
        'legs' => 'tramos',
        'driver_membership' => 'membresías de conductores',
        'transactions' => 'transacciones',
        'settings' => 'configuraciones',
        'memberships' => 'membresías',
        'jobs' => 'trabajos en cola',
        'failed_jobs' => 'trabajos fallidos'
    ],

    'attributes' => [
        'common' => [
            'id' => 'ID',
            'created_at' => 'fecha de creación',
            'updated_at' => 'fecha de actualización',
            'active' => 'activo',
            'status' => 'estado',
            'amount' => 'monto',
            'description' => 'descripción',
            'coordinates' => 'coordenadas'
        ],

        'users' => [
            'name' => 'nombre',
            'lastname' => 'apellido',
            'id_card' => 'cédula',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'email_verified_at' => 'fecha de verificación',
            'password' => 'contraseña',
            'profile_photo' => 'foto de perfil',
            'remember_token' => 'token de sesión'
        ],

        'password_reset_tokens' => [
            'email' => 'correo electrónico',
            'token' => 'token',
            'code' => 'código'
        ],

        'drivers' => [
            'trusted' => 'de confianza',
            'evaluation' => 'evaluación',
            'id_front' => 'frente de cédula',
            'id_rear' => 'reverso de cédula',
            'circulation_front' => 'frente de circulación',
            'circulation_rear' => 'reverso de circulación',
            'license' => 'licencia',
            'taxi_license' => 'licencia de taxi',
            'circulation_plate' => 'foto de placa',
            'wallet_id' => 'monedero',
            'vehicle_id' => 'vehículo asignado'
        ],

        'vehicles' => [
            'brand' => 'marca',
            'plate' => 'placa',
            'color' => 'color',
            'max_passengers' => 'pasajeros máximos',
            'comfort' => 'confort',
            'vehicle_type_id' => 'tipo de vehículo'
        ],

        'trips' => [
            'driver_id' => 'conductor',
            'client_id' => 'cliente',
            'passenger_count' => 'número de pasajeros',
            'is_comfort' => 'es confort',
            'reason' => 'motivo',
            'waiting_time' => 'tiempo de espera',
            'fare' => 'tarifa'
        ],

        'legs' => [
            'trip_id' => 'viaje',
            'origin' => 'origen',
            'destination' => 'destino',
            'fee' => 'tarifa',
            'distance' => 'distancia'
        ],

        'transactions' => [
            'wallet_id' => 'monedero',
            'operation' => 'operación',
            'origin_account' => 'cuenta origen',
            'destination_account' => 'cuenta destino'
        ],

        'memberships' => [
            'name' => 'nombre',
            'percentage' => 'porcentaje',
            'km' => 'kilómetros incluidos'
        ],

        'driver_membership' => [
            'driver_id' => 'conductor',
            'membership_id' => 'membresía',
            'until_date' => 'fecha de expiración'
        ],

        'settings' => [
            'radius_km' => 'radio en km',
            'time_waiting' => 'tiempo de espera',
            'membership_amount' => 'costo de membresía',
            'basic_discount' => 'descuento básico'
        ],

        'jobs' => [
            'queue' => 'cola',
            'payload' => 'carga útil',
            'attempts' => 'intentos',
            'reserved_at' => 'reservado en',
            'available_at' => 'disponible en'
        ],

        'failed_jobs' => [
            'uuid' => 'UUID',
            'connection' => 'conexión',
            'queue' => 'cola',
            'payload' => 'carga útil',
            'exception' => 'excepción',
            'failed_at' => 'falló en'
        ],

        'clients' => [
            'user_id' => 'usuario',
            'evaluation' => 'evaluación'
        ],

        'addresses' => [
            'alias_name' => 'nombre descriptivo'
        ],

        'commissions' => [
            'discount' => 'descuento'
        ],

        'notifications' => [
            'title' => 'título'
        ],

        'vehicle_types' => [
            'km_rate' => 'tarifa por km'
        ]
    ]
];
