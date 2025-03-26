<?php

namespace Mbox\BackCore\Models\Security;

use Mbox\BackCore\Models\CoreModel;

class Route extends CoreModel
{

    protected $fillable = [
        'menu_module',
        'module',
        'route',
        'method',
        'action',
        'active'
    ];

    public function rules($scenario = 'create'): array
    {
        $rules = [
            'create' => [
                'menu_module' => 'required|max:255',
                'module' => 'required|max:255',
                'action' => 'required|max:255',
                'method' => 'required|max:255',
                'route' => 'required|max:255',
                'active' => 'required|boolean'
            ],
            'update' => [
                'menu_module' => 'required|max:255',
                'module' => 'required|max:255',
                'action' => 'required|max:255',
                'method' => 'required|max:255',
                'route' => 'required|max:255',
                'active' => 'required|boolean'
            ],
        ];
        return $rules[$scenario] ?? [];
    }
}
