<?php

namespace Mbox\BackCore\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mbox\BackCore\Contracts\HasValidationRules;
use Mbox\BackCore\Services\Security\LogService;

/**
 * @method static create(array $params)
 * @method static where(string $string, array|string|null $apiKey)
 * @method static find(string $id)
 * @method static updateOrCreate(array $array, mixed $modelData)
 */
class CoreModel extends Model implements HasValidationRules
{
    //define si el modelo utiliza id o uuid
//    use HasUuids;

    //define si el modelo utiliza id o ulid
//    use HasUlids;

//define si el modelo aplica la eliminacion booleana
//use SoftDeletes;

//define el nombre de la tabla
//protected $table = '';

//define el nombre de la llave primaria
//protected $primaryKey = '';

//define si esa llave primaria es autoincremental
//public $incrementing = false;

//define el tipo de la llave
//protected $keyType = 'string';

//Define si el modelo utiliza las atributos created_at y updated_at
//public $timestamps = false;

//Define la conexion que utiliza el modelo
//protected $connection = 'mariadb';

//Define los atributos por default del modelo
//protected $attributes = [];

//Define los atributos que pueden ser asignados de forma masiva
//protected $fillable = [];

//Define los atributos que no pueden ser asignados de manera masiva
//protected $guarded = [];

//Define los atributos que no van a ser recuperados
    protected $hidden = ['pivot'];

//Define las relaciones que tiene el modelo
//protected $relations = [];

    /**
     * Define base validation rules for scenarios.
     * Classes inheriting this should override.
     */
    public function rules(string $scenario = 'create'): array
    {
        return [
            'create' => [],
            'update' => [],
        ][$scenario] ?? [];
    }

    public function processedRules(string $scenario = 'create'): array
    {
        $baseRules = $this->rules($scenario);

        if ($scenario === 'update') {
            return $this->processUpdateRules($baseRules);
        }

        return $baseRules;
    }

    /**
     * Add `sometimes` to all rules for the `update` scenario.
     */
    protected function processUpdateRules(array $rules): array
    {
        return array_map(function ($rule) {
            if (is_string($rule)) {
                return 'sometimes|' . $rule;
            }
            return $rule;
        }, $rules);
    }

    protected static function boot(): void
    {
        parent::boot();
        static::updated(function (CoreModel $model) {
            if ($model->getTable() != 'logs') {
                LogService::createAny('Actualizado', $model->getTable(), $model->getKey());
            }
        });
        static::created(function (CoreModel $model) {
            if ($model->getTable() != 'logs') {
                LogService::createAny('Creado', $model->getTable(), $model->getKey());
            }
        });
        static::deleted(function (CoreModel $model) {
            if ($model->getTable() != 'logs') {
                LogService::createAny('Eliminado', $model->getTable(), $model->getKey());
            }
        });
    }
}