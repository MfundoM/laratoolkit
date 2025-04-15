<?php

namespace Laratoolkit\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateCrud extends Command
{
    protected $signature = 'generate:crud 
    {name : The model name}
    {--inertia= : Use Inertia (vue or react)}
    {--advanced : Generate advanced CRUD features}
    {--base-request : Use BaseFormRequest for validation requests}';
    protected $description = 'Generate basic or advanced CRUD files for a model';

    public function handle()
    {
        $name = $this->argument('name');
        $isAdvanced = $this->option('advanced');
        $useBaseRequest = $this->option('base-request');
        $inertia = $this->option('inertia');
        $isVue = $inertia === 'vue';
        $isReact = $inertia === 'react';

        if ($isAdvanced) {
            $this->info('Generating advanced CRUD files...');
            $this->generateAdvancedCrud($name, $isVue, $isReact, $useBaseRequest);
        } else {
            $this->info('Generating basic CRUD files...');
            $this->generateBasicCrud($name, $isVue, $isReact, $useBaseRequest);
        }

        $this->line("<fg=bright-white;bg=green>🎉 CRUD files for {$name} generated successfully. HAPPY CODING.</>");
    }

    private function generateBasicCrud($name, $isVue, $isReact, $useBaseRequest)
    {
        // Generate basic CRUD files like model, migration, and controller
        $this->generateModel($name);
        $this->generateMigration($name);
        $this->generateController($name, $isVue, $isReact);
        $this->generateServiceClasses($name);
        $this->generateRequestClasses($name, $useBaseRequest);
        $this->generateViews($name, $isVue, $isReact);
        $this->generateWebRoutes($name);
    }

    private function generateAdvancedCrud($name, $isVue, $isReact, $useBaseRequest)
    {
        // Generate model, migration, controller, and request classes
        $this->generateModel($name);
        $this->generateMigration($name);
        $this->generateController($name, $isVue, $isReact);
        $this->generateRequestClasses($name, $useBaseRequest);
        $this->generateServiceClasses($name);
        $this->generateViews($name, $isVue, $isReact);
        $this->generatePolicy($name);
        $this->generateWebRoutes($name);
    }

    /**
     * Generate the model file.
     *
     * @param string $name
     * @return void
     */
    private function generateModel($name)
    {
        $this->call('make:model', ['name' => $name]);
        $this->line("<fg=green>✔</> <options=bold>{$name} Model created successfully.</>");
    }

    /**
     * Generate the migration file.
     *
     * @param string $name
     * @return void
     */
    private function generateMigration($name)
    {
        $plural = Str::plural($name);
        $this->call('make:migration', ['name' => "create{$plural}_table"]);
        $this->line("<fg=green>✔</> <options=bold>{$name} Migration created successfully.</>");
    }

    /**
     * Generate the controller file.
     *
     * @param string $name
     * @return void
     */
    private function generateController($name, $isVue, $isReact)
    {
        $lower = strtolower($name);
        $plural = Str::plural($name);
        $lowerPlural = Str::plural($lower);

        $controllerName = "{$name}Controller";
        $controllerStub = $isVue ? file_get_contents(__DIR__ . '/../../stubs/inertia-vue/controller.stub') : ($isReact ? file_get_contents(__DIR__ . '/../../stubs/inertia-react/controller.stub') : file_get_contents(__DIR__ . '/../../stubs/blade/controller.stub'));
        $controllerStub = str_replace(
            ['{{ model }}', '{{ lower }}', '{{ lowerPlural }}', '{{ plural }}'],
            [$name, $lower, $lowerPlural, $plural],
            $controllerStub
        );
        file_put_contents(app_path("Http/Controllers/{$controllerName}.php"), $controllerStub);
        $this->line("<fg=green>✔</> <options=bold>{$name} Controller created successfully.</>");
    }

    /**
     * Generate the service class file.
     *
     * @param string $name
     * @return void
     */
    private function generateServiceClasses($name)
    {
        $lower = strtolower($name);
        $plural = Str::plural($name);
        $lowerPlural = Str::plural($lower);

        $servicePath = app_path("Services/{$name}Service.php");

        if (!file_exists(dirname($servicePath))) {
            mkdir(dirname($servicePath), 0755, true);
        }

        $serviceStub = file_get_contents(__DIR__ . '/../../stubs/service.stub');
        $serviceContent = str_replace(
            ['{{ model }}', '{{ lower }}', '{{ lowerPlural }}', '{{ plural }}'],
            [$name, $lower, $lowerPlural, $plural],
            $serviceStub
        );
        file_put_contents($servicePath, $serviceContent);
        $this->line("<fg=green>✔</> <options=bold>{$name} Service created successfully.</>");
    }

    /**
     * Generate the request class file.
     *
     * @param string $name
     * @return void
     */
    private function generateRequestClasses($name, $useBaseRequest)
    {
        $lower = strtolower($name);
        $plural = Str::plural($name);
        $lowerPlural = Str::plural($lower);

        $requestPath = app_path("Http/Requests/{$name}Request.php");
        if (!file_exists(dirname($requestPath))) {
            mkdir(dirname($requestPath), 0755, true);
        }

        $baseRequestPath = app_path('Http/Requests/BaseFormRequest.php');
        if ($useBaseRequest) {
            if (!file_exists($baseRequestPath)) {
                $baseRequestStub = file_get_contents(__DIR__ . '/../../stubs/baseRequest.stub');
                file_put_contents($baseRequestPath, $baseRequestStub);
                $this->line("<fg=green>✔</> <options=bold>{$name} BaseFormRequest created successfully.</>");
            }
        }

        if (file_exists($baseRequestPath)) {
            $requestStub = file_get_contents(__DIR__ . '/../../stubs/request.stub');
            $requestContent = str_replace(
                ['{{ model }}', '{{ lower }}', '{{ lowerPlural }}'],
                [$name, $lower, $lowerPlural],
                $requestStub
            );
            file_put_contents($requestPath, $requestContent);
        } else {
            $this->call('make:request', ['name' => "{$name}Request"]);
        }
        $this->line("<fg=green>✔</> <options=bold>{$name} FormRequest class created successfully.</>");
    }

    /**
     * Generate the policy class file.
     *
     * @param string $name
     * @return void
     */
    private function generateViews($name, $isVue, $isReact)
    {
        if ($isVue) {
            $this->generateVueComponent($name);
        } elseif ($isReact) {
            $this->generateReactComponent($name);
        } else {
            $this->generateBladeViews($name);
        }
    }

    /**
     * Generate the Inertia Vue component files.
     *
     * @param string $name
     * @return void
     */
    private function generateVueComponent($name)
    {
        $componentPath = resource_path("js/Pages/{$name}");
        if (!is_dir($componentPath)) {
            mkdir($componentPath, 0777, true);
        }

        foreach (['Index', 'Create', 'Edit', 'Show'] as $view) {
            $stub = file_get_contents(__DIR__ . "/../../stubs/inertia-vue/{$view}.vue.stub");
            file_put_contents("{$componentPath}/{$view}.vue", $stub);
        }
        $this->line("<fg=green>✔</> <options=bold>{$name} Inertia VUE components scaffolded.</>");
    }

    /**
     * Generate the Inertia React component files.
     *
     * @param string $name
     * @return void
     */
    private function generateReactComponent($name)
    {
        $componentPath = resource_path("js/Pages/{$name}");
        if (!is_dir($componentPath)) {
            mkdir($componentPath, 0777, true);
        }

        foreach (['Index', 'Create', 'Edit', 'Show'] as $view) {
            $stub = file_get_contents(__DIR__ . "/../../stubs/inertia-react/{$view}.jsx.stub");
            file_put_contents("{$componentPath}/{$view}.jsx", $stub);
        }
        $this->line("<fg=green>✔</> <options=bold>{$name} Inertia React components scaffolded.</>");
    }

    /**
     * Generate the Blade view files.
     *
     * @param string $name
     * @return void
     */
    private function generateBladeViews($name)
    {
        $plural = Str::plural($name);
        $viewPath = resource_path("views/{$plural}");
        if (!is_dir($viewPath)) {
            mkdir($viewPath, 0777, true);
        }

        foreach (['index', 'create', 'edit', 'show'] as $view) {
            $stub = file_get_contents(__DIR__ . "/../../stubs/blade/{$view}.stub");
            file_put_contents("{$viewPath}/{$view}.blade.php", $stub);
        }
        $this->line("<fg=green>✔</> <options=bold>{$name} Blade files scaffolded.</>");
    }

    /**
     * Generate the policy class file.
     *
     * @param string $name
     * @return void
     */
    private function generatePolicy($name)
    {
        $this->call('make:policy', ['name' => "{$name}Policy"]);
        $this->line("<fg=green>✔</> <options=bold>{$name} Policy created successfully.</>");
    }

    /**
     * Generate the web routes.
     *
     * @param string $name
     * @return void
     */
    private function generateWebRoutes($name)
    {
        $plural = Str::plural(strtolower($name));
        $routesFile = base_path('routes/web.php');
        $routeEntry = "Route::resource('/{$plural}', \\App\\Http\\Controllers\\{$name}Controller::class);";

        if (strpos(file_get_contents($routesFile), $routeEntry) === false) {
            file_put_contents($routesFile, "\n" . $routeEntry, FILE_APPEND);
            $this->line("<fg=green>✔</> <options=bold>{$name} Resource route added to web.php.</>");
        } else {
            $this->line('');
            $this->line('<fg=black;bg=yellow;options=bold> ⚠ Route already exists. Skipping... </>');
            $this->line('');
        }
    }
}
