<?php

namespace Mbox\BackCore\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class MakeService extends Command
{
    /**
     * The name and signature of the console command.
     * All options are executed by default
     * @var string
     */
    protected $signature = 'make:service {name}
                            {--a|all : Crear todos los archivos (modelo, controlador, migración, servicio)}
                            {--m|model : Crear solo el modelo}
                            {--c|controller : Crear solo el controlador}
                            {--s|service : Crear solo el servicio}
                            {--i|migration : Crear solo la migración}
                            {--p|api : Crear solo la actualización de la api}
                            {--e|seeder : Crear solo el archivo de seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea la migracion, archivo para poblar, modelo, controlador, servicio y actualizacion en la api';


    private Filesystem $files;

    public function __construct(Filesystem $files)
    {
        parent::__construct();
        $this->files = $files;
    }

    /**
     * Execute the console command.
     * @throws FileNotFoundException
     */
    public function handle(): void
    {
        $model = Str::studly($this->argument('name'));
        $this->info("Creando componentes para el servicio: $model");

        if (
            !$this->option('model') &&
            !$this->option('controller') &&
            !$this->option('service') &&
            !$this->option('migration') &&
            !$this->option('api') &&
            !$this->option('seeder')
        ){
            $this->input->setOption('all', true);
        }

        if  ($this->option('all') || $this->option('migration'))
            $this->createMigration($model);

        if ($this->option('all') || $this->option('model'))
            $this->createModel($model);

        if ($this->option('all') || $this->option('controller'))
            $this->createController($model);

        if ($this->option('all') || $this->option('service'))
            $this->createService($model);

        if ($this->option('all') || $this->option('api'))
            $this->updateApiRoutes($model);

        if ($this->option('all') || $this->option('seeder'))
            $this->createJsonFile($model);

        $this->info("¡Servicio $model creado exitosamente!");
    }

    private function createMigration($model): void
    {
        if (str_contains($model, '/')){
            $explode = explode('/', $model);
            $model = $explode[1];
        }

        $tableName = Str::snake(Str::plural($model));

        $migrationName = "create_{$tableName}_table";

        Artisan::call('make:migration', [
            'name' => $migrationName,
            '--create' => $tableName,
        ]);

        $this->info("Migración creada: $migrationName");
    }

    /**
     * @throws FileNotFoundException
     */
    private function createModel($model): void
    {
        $modelPath = app_path("Models/$model.php");
        $this->makeDirectory($modelPath);

        if ($this->files->exists($modelPath)) {
            $this->error("El modelo $model ya existe.");
            return;
        }

        $module = '';
        if (str_contains($model, '/')){
            $explode = explode('/', $model);
            $module = '\\'.$explode[0];
            $model = $explode[1];
        }

        $stub = $this->getStub('model');
        $this->files->put($modelPath, $this->populateStub($stub, $model, $module));
        $this->info("Modelo creado: $modelPath");
    }

    /**
     * @throws FileNotFoundException
     */
    private function createController($model): void
    {
        $controllerPath = app_path("Http/Controllers/{$model}Controller.php");
        $this->makeDirectory($controllerPath);

        if ($this->files->exists($controllerPath)) {
            $this->error("El controlador {$model}Controller ya existe.");
            return;
        }

        $module = '';
        if (str_contains($model, '/')){
            $explode = explode('/', $model);
            $module = '\\'.$explode[0];
            $model = $explode[1];
        }

        $stub = $this->getStub('controller');
        $this->files->put($controllerPath, $this->populateStub($stub, $model, $module));
        $this->info("Controlador creado: $controllerPath");
    }

    /**
     * @throws FileNotFoundException
     */
    private function createService($model): void
    {
        $servicePath = app_path("Services/{$model}Service.php");
        $this->makeDirectory($servicePath);

        if ($this->files->exists($servicePath)) {
            $this->error("El servicio {$model}Service ya existe.");
            return;
        }

        $module = '';
        if (str_contains($model, '/')){
            $explode = explode('/', $model);
            $module = '\\'.$explode[0];
            $model = $explode[1];
        }

        $stub = $this->getStub('service');
        $this->files->put($servicePath, $this->populateStub($stub, $model, $module));
        $this->info("Servicio creado: $servicePath");
    }

    private function updateApiRoutes($model): void
    {
        $filePath = base_path('routes/api.php');

        if (!file_exists($filePath)) {
            $this->error("El archivo api.php no existe.");
            return;
        }

        $module = '';
        if (str_contains($model, '/')){
            $explode = explode('/', $model);
            $module = '\\'.$explode[0];
            $model = $explode[1];
        }


        $content = file_get_contents($filePath);

        $newRoute = "\nuse App\Http\Controllers$module\\{$model}Controller;";
        $newRoute .= "\nRoute::Resource('" . Str::kebab(Str::plural($model)) . "', {$model}Controller::class);";

        if (str_contains($content, $newRoute)) {
            $this->info("La ruta para $model ya existe en api.php.");
            return;
        }

        $content .= $newRoute;

        file_put_contents($filePath, $content);

        $this->info("Ruta añadida a api.php: $newRoute");
    }

    private function createJsonFile($model): void
    {
        if (str_contains($model, '/')){
            $explode = explode('/', $model);
            $model = $explode[1];
        }

        $tableName = Str::snake(Str::plural($model));

        $dataPath = base_path("database/data");
        $filePath = "$dataPath/$tableName.json";

        if (!file_exists($dataPath)) {
            mkdir($dataPath, 0755, true);
        }

        if (file_exists($filePath)) {
            $this->info("El archivo JSON para la tabla $tableName ya existe: $filePath");
            return;
        }

        $data = json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($filePath, $data);

        $this->info("Archivo JSON creado: $filePath");
    }


    /**
     * @throws FileNotFoundException
     */
    private function getStub($type): string
    {
        return $this->files->get(base_path("stubs/$type.stub"));
    }

    private function populateStub($stub, $model, $module = ''): array|string
    {
        $stub = str_replace('{{ model }}', $model, $stub);
        return str_replace('{{ module }}', $module, $stub);
    }

    private function makeDirectory($path): void
    {
        $directory = dirname($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory, 0755, true);
        }
    }
}
