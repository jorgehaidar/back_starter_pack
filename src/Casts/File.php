<?php

namespace App\Casts;


use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class File implements CastsAttributes
{

    protected string $disk;
    protected string $namePrefix;

    public function __construct($namePrefix = '')
    {
        $this->disk = config('filecaster.disk') ?? 'public';
        $this->namePrefix = $namePrefix;
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value)
            return Storage::disk($this->disk)->url($value);
        return null;
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (is_file($value)) {
            if (isset($attributes[$key])) {
                if (Storage::disk($this->disk)->exists($attributes[$key])) {
                    Storage::disk($this->disk)->delete($attributes[$key]);
                }
            }
            $file = $value;

            $filenameWithExt = $this->getFileName($file);
            $path = $this->filePath($model, $attributes);

            return $file->storeAs($path, $filenameWithExt, $this->disk);
        } else {
            return $value;
        }
    }

    protected function getId($attributes = null, $modelName = null)
    {
        if (isset($attributes['id'])) {
            return $attributes['id'];
        } else {
            $model = $modelName::orderBy('id', 'desc')->first();
            if ($model) {
                return $model->id + 1;
            } else {
                return 1;
            }
        }
    }

    protected function getClassName(Model $model): string
    {
        return strtolower(substr(get_class($model), strrpos(get_class($model), '\\') + 1));
    }

    protected function filePath(Model $model, array $attributes): mixed
    {
        $definedPath = config('filecaster.path');
        if ($definedPath == 'by_model_name_and_id') {
            return $this->pathByModelNameAndId($model, $attributes);
        } elseif ($definedPath == 'defined_path_in_model') {
            return $this->pathByDefinedPathInModel($model);
        } else {
            throw new Exception("Invalid path defined in config");
        }
    }

    protected function pathByModelNameAndId(Model $model, array $attributes): mixed
    {
        $class = $this->getClassName($model);
        $id = $this->getId($attributes, $model);
        return $class . '/' . $id;
    }

    protected function pathByDefinedPathInModel(Model $model): mixed
    {
        if (!isset($model->fileUploadPath)) {
            throw new Exception("Model does not have a variable named fileUploadPath");
        }
        return $model->fileUploadPath;
    }

    protected function getFileName(UploadedFile $file): string
    {
        $fileName = config('filecaster.file_name');
        $namePrefix = $this->namePrefix;
        $name = '';
        if ($fileName == 'original_file_name') {
            $name = $file->getClientOriginalName();
        } elseif ($fileName == 'hash_name') {
            $name = $file->hashName();
        } else {
            throw new Exception("Invalid file name defined in config");
        }
        if ($namePrefix && $namePrefix != '') {
            $name = $namePrefix . '-' . $name;
        }
        return $name;
    }
}
