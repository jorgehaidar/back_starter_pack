<?php

return [
    'models' => [
        'users' => 'users',
        'password_reset_tokens' => 'password reset tokens',
        'api_keys' => 'API keys',
        'routes' => 'routes',
        'roles' => 'roles',
        'role_user' => 'role assignments',
        'permissions' => 'permissions',
        'logs' => 'logs',
        'error_logs' => 'error logs',
        'rate_limit_blocks' => 'rate limit blocks',
        'wallets' => 'wallets',
        'clients' => 'clients',
        'vehicle_types' => 'vehicle types',
        'vehicles' => 'vehicles',
        'drivers' => 'drivers',
        'notifications' => 'notifications',
        'commissions' => 'commissions',
        'addresses' => 'addresses',
        'trips' => 'trips',
        'legs' => 'route legs',
        'driver_membership' => 'driver memberships',
        'transactions' => 'transactions',
        'settings' => 'settings',
        'memberships' => 'memberships',
        'jobs' => 'jobs',
        'failed_jobs' => 'failed jobs'
    ],

    'attributes' => [
        'common' => [
            'id' => 'ID',
            'created_at' => 'creation date',
            'updated_at' => 'update date',
            'active' => 'active',
            'status' => 'status',
            'amount' => 'amount',
            'description' => 'description',
            'coordinates' => 'coordinates'
        ],

        'users' => [
            'name' => 'name',
            'lastname' => 'last name',
            'id_card' => 'ID card',
            'phone' => 'phone',
            'email' => 'email',
            'email_verified_at' => 'verification date',
            'password' => 'password',
            'profile_photo' => 'profile photo',
            'remember_token' => 'remember token'
        ],

        'password_reset_tokens' => [
            'email' => 'email',
            'token' => 'token',
            'code' => 'code'
        ],

        'drivers' => [
            'trusted' => 'trusted',
            'evaluation' => 'evaluation',
            'id_front' => 'ID front',
            'id_rear' => 'ID back',
            'circulation_front' => 'circulation front',
            'circulation_rear' => 'circulation back',
            'license' => 'license',
            'taxi_license' => 'taxi license',
            'circulation_plate' => 'plate photo',
            'wallet_id' => 'wallet',
            'vehicle_id' => 'assigned vehicle'
        ],

        'vehicles' => [
            'brand' => 'brand',
            'plate' => 'license plate',
            'color' => 'color',
            'max_passengers' => 'max passengers',
            'comfort' => 'comfort',
            'vehicle_type_id' => 'vehicle type'
        ],

        'trips' => [
            'driver_id' => 'driver',
            'client_id' => 'client',
            'passenger_count' => 'passenger count',
            'is_comfort' => 'is comfort',
            'reason' => 'reason',
            'waiting_time' => 'waiting time',
            'fare' => 'fare'
        ],

        'legs' => [
            'trip_id' => 'trip',
            'origin' => 'origin',
            'destination' => 'destination',
            'fee' => 'fee',
            'distance' => 'distance'
        ],

        'transactions' => [
            'wallet_id' => 'wallet',
            'operation' => 'operation',
            'origin_account' => 'origin account',
            'destination_account' => 'destination account'
        ],

        'memberships' => [
            'name' => 'name',
            'percentage' => 'percentage',
            'km' => 'included kilometers'
        ],

        'driver_membership' => [
            'driver_id' => 'driver',
            'membership_id' => 'membership',
            'until_date' => 'expiration date'
        ],

        'settings' => [
            'radius_km' => 'radius in km',
            'time_waiting' => 'waiting time',
            'membership_amount' => 'membership cost',
            'basic_discount' => 'basic discount'
        ],

        'jobs' => [
            'queue' => 'queue',
            'payload' => 'payload',
            'attempts' => 'attempts',
            'reserved_at' => 'reserved at',
            'available_at' => 'available at'
        ],

        'failed_jobs' => [
            'uuid' => 'UUID',
            'connection' => 'connection',
            'queue' => 'queue',
            'payload' => 'payload',
            'exception' => 'exception',
            'failed_at' => 'failed at'
        ],

        'clients' => [
            'user_id' => 'user',
            'evaluation' => 'evaluation'
        ],

        'addresses' => [
            'alias_name' => 'alias name'
        ],

        'commissions' => [
            'discount' => 'discount'
        ],

        'notifications' => [
            'title' => 'title'
        ],

        'vehicle_types' => [
            'km_rate' => 'rate per km'
        ]
    ]
];
