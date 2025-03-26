<?php

namespace Mbox\BackCore\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Mbox\BackCore\Models\CoreModel;


class ModelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    use Exportable;

    private CoreModel $modelClass;
    private array $columns;
    private array $headings;
    private array $ids;

    public function __construct(CoreModel $model, array $columns, array $friendlyColumns, array $ids)
    {
        $this->modelClass = $model;
        $this->columns = $columns;
        $this->headings = $friendlyColumns;
        $this->ids = $ids;
    }

    public function collection(): Collection
    {
        $relations = collect($this->columns)
            ->filter(fn($column) => str_contains($column, '.'))
            ->map(fn($column) => explode('.', $column)[0])
            ->unique()
            ->toArray();

        return $this->modelClass::query()
            ->with($relations)
            ->whereIn('id', $this->ids)
            ->get();
    }

    public function headings(): array
    {
        return array_values($this->headings);
    }

    public function map($row): array
    {
        return array_map(function ($column) use ($row) {
            if (str_contains($column, '.')) {
                $segments = explode('.', $column);
                $value = $row;

                foreach ($segments as $segment) {
                    $value = $value->{$segment} ?? null;

                    if (is_iterable($value)) {
                        $value = $value->pluck(end($segments))->join(', ');
                        break;
                    }
                }

                return $value;
            }

            return $row->{$column} ?? null;
        }, $this->columns);
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => [
                    'name' => 'Arial',
                    'size' => 12,
                    'bold' => true,
                ],
            ],
            'A1:Z1' => [
                'font' => [
                    'bold' => true,
                ],
            ],
        ];
    }
}
