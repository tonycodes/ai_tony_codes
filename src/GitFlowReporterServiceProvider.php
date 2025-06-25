<?php

namespace TonyCodes\GitFlowReporter;

use Illuminate\Support\ServiceProvider;
use TonyCodes\GitFlowReporter\Services\GitHubIssueService;
use TonyCodes\GitFlowReporter\Services\LicenseValidator;

class GitFlowReporterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/gitflow-reporter.php', 'gitflow-reporter'
        );

        $this->app->singleton(GitHubIssueService::class, function ($app) {
            return new GitHubIssueService(
                $app['config']['gitflow-reporter']
            );
        });

        $this->app->singleton(LicenseValidator::class, function ($app) {
            return new LicenseValidator(
                $app['config']['gitflow-reporter.license_key'],
                $app['config']['gitflow-reporter.license_server']
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration
        $this->publishes([
            __DIR__.'/../config/gitflow-reporter.php' => config_path('gitflow-reporter.php'),
        ], 'gitflow-reporter-config');

        // Publish views
        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/gitflow-reporter'),
        ], 'gitflow-reporter-views');

        // Publish assets
        $this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/gitflow-reporter'),
        ], 'gitflow-reporter-assets');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'gitflow-reporter');

        // Load routes
        $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Load migrations (if any)
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register middleware
        $this->registerMiddleware();

        // Validate license
        $this->validateLicense();
    }

    /**
     * Register middleware
     */
    protected function registerMiddleware(): void
    {
        $router = $this->app['router'];
        
        $router->aliasMiddleware('gitflow-reporter.licensed', 
            \TonyCodes\GitFlowReporter\Middleware\ValidateLicense::class
        );
    }

    /**
     * Validate license
     */
    protected function validateLicense(): void
    {
        if ($this->app->environment('production')) {
            $validator = $this->app->make(LicenseValidator::class);
            
            if (!$validator->isValid()) {
                throw new \Exception('GitFlow Reporter: Invalid or expired license. Please contact support.');
            }
        }
    }
}