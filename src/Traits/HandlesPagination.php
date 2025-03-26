<?php

namespace Mbox\BackCore\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;

trait HandlesPagination
{
    public function applyPagination(Builder $query, array $pagination = []): array
    {
        $currentPage = $pagination['page'] ?? 1;
        $perPage = $pagination['pageSize'] ?? $query->getModel()->getPerPage();

        Paginator::currentPageResolver(function () use ($currentPage) {
            return $currentPage;
        });

        $paginate = $query->paginate($perPage);

        return [
            'data' => $paginate->items(),
            'total' => $paginate->total(),
            'last_page' => $paginate->lastPage()
        ];
    }
}
