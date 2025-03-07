<?php

namespace Regnerisch\LaravelBeyond\Commands;

use Regnerisch\LaravelBeyond\Resolvers\AppNameSchemaResolver;

class MakeControllerCommand extends BaseCommand
{
    protected $signature = 'beyond:make:controller {name?} {--api} {--i|invokable} {--force}';

    protected $description = 'Make a new controller';

    public function handle(): void
    {
        try {
            $name = $this->argument('name');
            $api = $this->option('api');
            $force = $this->option('force');
            $invokable = $this->option('invokable');

            $stub = null;

            if ($api) {
                $stub = 'controller.api.stub';
            } elseif ($invokable) {
                $stub = 'controller.invokable.stub';
            } else {
                $stub = 'controller.stub';
            }

            $stub = match (true) {
                $api && !$invokable => 'controller.api.stub',
                $invokable && !$api => 'controller.invokable.stub',
                default => 'controller.stub'
            };

            $schema = (new AppNameSchemaResolver($this, $name))->handle();

            beyond_copy_stub(
                $stub,
                $schema->path('Controllers'),
                [
                    '{{ namespace }}' => $schema->namespace(),
                    '{{ className }}' => $schema->className(),
                ],
                $force
            );

            $this->components->info('Controller created.');
        } catch (\Exception $exception) {
            $this->components->error($exception->getMessage());
        }
    }
}
