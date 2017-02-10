<?php

namespace JohnDev\Breadcrumbs;

use Illuminate\Support\ServiceProvider;

class BreadcrumbsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerBreadcrumbsBuilder();

        $this->app->alias('breadcrumbs', 'JohnDev\Breadcrumbs\BreadcrumbsBuilder');
    }

    /**
     * Register the HTML builder instance.
     *
     * @return void
     */
    protected function registerBreadcrumbsBuilder()
    {
        $this->app->singleton('breadcrumbs', function ($app) {
            return new BreadcrumbsBuilder($app['view'], $app['router'], $app['translator'], $app['request']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['breadcrumbs', 'JohnDev\Breadcrumbs\BreadcrumbsBuilder'];
    }

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        /* Translations */
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'breadcrumbs');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/breadcrumbs'),
        ]);

        /* Views */
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'breadcrumbs');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/breadcrumbs'),
        ]);
    }
}
