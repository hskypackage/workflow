<?php

namespace HskyZhou\Workflow;

use Illuminate\Support\ServiceProvider;

/**
 * @author Boris Koumondji <brexis@yahoo.fr>
 */
class WorkflowServiceProvider extends ServiceProvider
{
    protected $commands = [
        'HskyZhou\Workflow\Commands\WorkflowDumpCommand',
    ];

    /**
    * Bootstrap the application services...
    *
    * @return void
    */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/../databases');
        
        $this->handleViews();

        $this->handleRoutes();

        $this->handleRecources();

    }

    /**
    * Register the application services.
    *
    * @return void
    */
    public function register()
    {
        $this->commands($this->commands);

        $this->app->singleton(
            'workflow', function ($app) {
                return new WorkflowRegistry($app['config']['workflow']);
            }
        );
    }

    /**
    * Get the services provided by the provider.
    *
    * @return array
    */
    public function provides()
    {
        return ['workflow'];
    }

    private function handleViews() {
        $this->loadViewsFrom(__DIR__.'/../views','workflow');
        $this->publishes([__DIR__.'/../views' => base_path('resources/views/vendor/workflow')],'view');
    }


    private function handleRoutes() {
        include __DIR__.'/../routes.php';
    }

    private function handleRecources(){
        $this->publishes([
            __DIR__ . '/../resources/' => public_path('workflows')
        ],'datatables');


    }
}
