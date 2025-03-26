<?php

namespace Mbox\BackCore\Models\Security;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mbox\BackCore\Models\CoreModel;

class Log extends CoreModel
{

    protected $relations = ['users'];

    //TODO: poner aqui la relacion con tu clase de usuario
//    public function users(): BelongsTo
//    {
//        return $this->belongsTo(User::class);
//    }

    protected $fillable = [
        'user_id',
        'date_time',
        'action_name',
        'ip',
        'record',
        'table_name'
    ];

    public function rules($scenario = 'create'): array
    {
        $rules = [
            'create' => [
                'user_id' => 'nullable|integer|exists:users,id',
                'date_time' => 'required|date',
                'action_name' => 'required|max:255',
                'ip' => 'required|max:16',
                'record' => 'required|max:255',
                'table_name' => 'required|max:255'
            ],
            'update' => [
                'user_id' => 'nullable|integer|exists:users,id',
                'date_time' => 'required|date',
                'action_name' => 'required|max:255',
                'ip' => 'required|max:16',
                'record' => 'required|max:255',
                'table_name' => 'required|max:255'
            ],
        ];
        return $rules[$scenario] ?? [];
    }
}
