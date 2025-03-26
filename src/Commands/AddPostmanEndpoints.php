<?php

namespace Mbox\BackCore\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class AddPostmanEndpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'postman:add {model : Nombre del modelo} {endpoint : Endpoint base}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new model to postman library';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $model = $this->argument('model');
        $endpoint = $this->argument('endpoint');

        $filePath = App::base_path('api_docs/postman_collection.json');

        $content = File::get($filePath);
        $data = json_decode($content, true);

        $newModel = [
            "name" => $model,
            "item" => [
                [
                    "name" => "get-$model",
                    "protocolProfileBehavior" => [
                        "disableBodyPruning" => true
                    ],
                    "request" => [
                        "method" => "GET",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "",
                            "options" => [
                                "raw" => [
                                    "language" => "json"
                                ]
                            ]
                        ],
                        "url" => [
                            "raw" => "{{base_url}}/$endpoint",
                            "host" => ["{{base_url}}"],
                            "path" => [$endpoint]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "get_by_id-$model",
                    "protocolProfileBehavior" => [
                        "disableBodyPruning" => true
                    ],
                    "request" => [
                        "method" => "GET",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "",
                            "options" => [
                                "raw" => [
                                    "language" => "json"
                                ]
                            ]
                        ],
                        "url" => [
                            "raw" => "{{base_url}}/$endpoint/1",
                            "host" => ["{{base_url}}"],
                            "path" => [$endpoint, "1"]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "store-$model",
                    "request" => [
                        "method" => "POST",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "",
                            "options" => [
                                "raw" => [
                                    "language" => "json"
                                ]
                            ]
                        ],
                        "url" => [
                            "raw" => "{{base_url}}/$endpoint",
                            "host" => ["{{base_url}}"],
                            "path" => [$endpoint]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "update-$model",
                    "request" => [
                        "method" => "PUT",
                        "header" => [],
                        "body" => [
                            "mode" => "raw",
                            "raw" => "",
                            "options" => [
                                "raw" => [
                                    "language" => "json"
                                ]
                            ]
                        ],
                        "url" => [
                            "raw" => "{{base_url}}/$endpoint/1",
                            "host" => ["{{base_url}}"],
                            "path" => [$endpoint, "1"]
                        ]
                    ],
                    "response" => []
                ],
                [
                    "name" => "delete-$model",
                    "request" => [
                        "method" => "DELETE",
                        "header" => [],
                        "url" => [
                            "raw" => "{{base_url}}/$endpoint/1",
                            "host" => ["{{base_url}}"],
                            "path" => [$endpoint, "1"]
                        ]
                    ],
                    "response" => []
                ]
            ]
        ];

        $data['item'][] = $newModel;

        $newContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        File::put($filePath, $newContent);

        $this->info("Modelo '$model' agregado correctamente con el endpoint '$endpoint'.");
    }
}
