<?php

namespace Mbox\BackCore\Services;

use Mbox\BackCore\Models\CoreModel;
use Mbox\BackCore\Traits\HandlesPagination;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class CoreService
{
    use HandlesPagination;

    protected CoreModel $modelClass;

    public function processRequest(array $params): Builder
    {
        $query = $this->modelClass->query();

        if (isset($params['relations'])) {
            $query = $this->setRelations($query, $params['relations']);
        }

        if (isset($params['orderBy'])) {
            $query = $this->setOrderBy($query, $params['orderBy']);
        }

        if (isset($params['select'])) {
            $query = $this->setSelect($query, $params['select']);
        }

        if (isset($params['attr'])) {
            $query = $this->setAttr($query, $params['attr']);
        }

        return $query;
    }

    /**
     * Definicion de relaciones
     *
     * "relations": "all"
     * "relations": "relation1"
     * "relations": "relation1.subrelation"
     * "relations": ["relation1", "relation2.subrelation", "..."]
     *
     * @param Builder $query
     * @param array|string $relation
     * @return Builder
     */
    protected function setRelations(Builder $query, array|string $relation): Builder
    {
        if (is_array($relation)) {
            foreach ($relation as $relationItem) {
                $query->with($relationItem);
            }
        }
        elseif ($relation === 'all'){
            $query->with($this->modelClass->getRelations());
        }
        else
        {
            $query->with($relation);
        }

        return $query;
    }

    /**
     * Definicion de orden de columna
     *
     * "orderBy": {
     *      "column1": "asc"
     *      "column2": "desc"
     * }
     *
     * @param Builder $query
     * @param array $orderBy
     * @return Builder
     */
    protected function setOrderBy(Builder $query, array $orderBy): Builder
    {
        foreach ($orderBy as $key => $value) {
            $query->orderBy($key, $value);
        }

        return $query;
    }

    /**
     * Definicion de seleccion de columna
     *
     * "select": [column1, column2]
     *
     * @param Builder $query
     * @param array $select
     * @return Builder
     */
    protected function setSelect(Builder $query, array $select): Builder
    {
        $query->select($select);

        return $query;
    }

    /**
     *
     * Definicion de criterios de busqueda
     *
     * Busqueda con operadores
     * "attr": {
     *   "or": [
     *     ["column", "operator", value]
     *   ],
     *   "and": [
     *     ["column", "operator", value]
     *   ]
     * }
     *
     * Busqueda simple
     * "attr": [
     *   ["column", "operator", value]
     * ]
     *
     * Busqueda con relaciones
     *  "attr": [
     *    ["relation.column", "operator", value]
     *  ]
     *
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    protected function setAttr(Builder $query, array $filters): Builder
    {
        if (!isset($filters['and']) && !isset($filters['or'])) {
            $filters = ['and' => $filters];
        }

        if (isset($filters['and']) && is_array($filters['and'])) {
            foreach ($filters['and'] as $condition) {
                $this->applyCondition($query, $condition, 'and');
            }
        }

        if (isset($filters['or']) && is_array($filters['or'])) {
            foreach ($filters['or'] as $condition) {
                $this->applyCondition($query, $condition, 'or');
            }
        }

        return $query;
    }

    protected function applyCondition(Builder $query, array $condition, string $type): void
    {
        if (count($condition) < 2) {
            return;
        }

        [$key, $operator, $value] = array_pad($condition, 3, null);

        if (!$operator) {
            $operator = 'LIKE';
            $value = '%' . $value . '%';
        }

        if (str_contains($key, '.')) {
            $relations = explode('.', $key);
            $field = array_pop($relations);
            $relationPath = implode('.', $relations);

            $method = $type === 'or' ? 'orWhereHas' : 'whereHas';
            $query->{$method}($relationPath, function ($relationQuery) use ($field, $operator, $value) {
                $relationQuery->where($field, $operator, $value);
            });
        } else {
            $method = $type === 'or' ? 'orWhere' : 'where';
            $query->{$method}($key, $operator, $value);
        }
    }

    public function getById(string $id, array $params = []): array
    {
        try {
            $query = $this->processRequest($params);
            $model = $query->find($id);
            if (!$model) {
                return [
                    'success' => false,
                    'message' => 'Resource not found.',
                    'status' => Response::HTTP_NOT_FOUND
                ];
            }

            return [
                'success' => true,
                'message' => $this->modelClass->getTable().' retrieved successfully.',
                'data' => $model,
                'status' => Response::HTTP_OK
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'success' => false,
                'message' => $this->modelClass->getTable().' not found.',
                'errors' => [$e->getMessage()],
                'status' => Response::HTTP_NOT_FOUND,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while retrieving the resource.',
                'errors' => [$e->getMessage()],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }

    /**
     * @throws Exception
     */
    public function getAll(array $params): Collection|array
    {
        try {
            $query = $this->processRequest($params);

            if (isset($params['pagination']))
                return $this->applyPagination($query, $params['pagination']);

            return $query->get();
        } catch (Throwable $e) {
            throw new Exception('Error processing query: ' . $e->getMessage());
        }
    }

    public function create(array $params): array
    {
        $result = $this->modelValidator($params);
        if (!$result['success']) {
            return $result;
        }

        try {
            $model = $this->modelClass::create($params);

            return [
                'success' => true,
                'message' => 'Resource created successfully.',
                'data' => $model,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to create resource.',
                'errors' => [$e->getMessage()],
            ];
        }
    }

    public function update(string $id, array $params): array
    {
        /** @var CoreModel $model */
        $model = $this->modelClass::find($id);
        if (!$model) {
            return [
                'success' => false,
                'message' => 'Resource not found.',
            ];
        }

        $validationResult = $this->modelValidator($params, 'update');
        if (!$validationResult['success']) {
            return $validationResult;
        }

        try {
            $updated = $model->update($params);

            if (!$updated) {
                return [
                    'success' => false,
                    'message' => 'Failed to update resource.',
                ];
            }

            return [
                'success' => true,
                'message' => 'Resource updated successfully.',
                'data' => $model,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Failed to update resource.',
                'errors' => [$e->getMessage()],
            ];
        }
    }

    public function deleteById(string $id): array
    {
        try {
            $model = $this->modelClass::find($id);
            if (!$model) {
                return [
                    'success' => false,
                    'message' => $this->modelClass->getTable() . ' not found.',
                    'status' => Response::HTTP_NOT_FOUND
                ];
            }

            $deleted = $model->delete();

            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'Error while deleting ' . $this->modelClass->getTable() . '.',
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR
                ];
            }

            return [
                'success' => true,
                'message' => $this->modelClass->getTable() . ' deleted successfully.',
                'data' => ['id' => $model->id],
                'status' => Response::HTTP_OK
            ];
        } catch (QueryException $e) {
            return [
                'success' => false,
                'message' => 'Error while deleting ' . $this->modelClass->getTable() . '.',
                'errors' => [$e->getMessage()],
                'status' => Response::HTTP_BAD_REQUEST
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while deleting the resource.',
                'errors' => [$e->getMessage()],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }

    public function deleteMultiple(array $params): array
    {
        $validator = Validator::make($params, [
            'ids' => 'required|array|min:1',
            'ids.*' => 'required|integer|exists:' . $this->modelClass->getTable() . ',id',
        ]);

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Data validation error.',
                'errors' => $validator->errors(),
                'status' => Response::HTTP_UNPROCESSABLE_ENTITY
            ];
        }

        try {
            $deleted = $this->modelClass::destroy($params['ids']);

            if (!$deleted) {
                return [
                    'success' => false,
                    'message' => 'Error while deleting records from ' . $this->modelClass->getTable() . '.',
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR
                ];
            }

            return [
                'success' => true,
                'message' => 'Records deleted successfully.',
                'data' => ['deleted_count' => $deleted],
                'status' => Response::HTTP_OK
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while deleting the resources.',
                'errors' => [$e->getMessage()],
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ];
        }
    }

    protected function modelValidator(array $params, string $scenario = 'create'): array
    {
        $validator = Validator::make($params, $this->modelClass->processedRules($scenario));

        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => 'Data validation error',
                'errors' => $validator->errors(),
            ];
        }

        return ['success' => true];
    }
}
