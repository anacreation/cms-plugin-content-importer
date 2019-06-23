<?php

namespace Anacreation\CmsContentImporter;

use Anacreation\Cms\Models\Cms;
use Anacreation\CmsContentImporter\Models\ContentImporter;
use Illuminate\Support\ServiceProvider;

class CmsContentImporterServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {

        $this->views();

        $this->registerCmsPlugin();

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {

    }

    private function views() {
        $this->loadViewsFrom(__DIR__ . '/views', 'cms:contentImporter');

        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/cms:contentImporter'),
        ], 'cms:contentImporter');

    }

    private function registerCmsPlugin(): void {
        Cms::registerCmsPlugins(
            'CmsContentImporter',
            'Cms Import Content',
            'contentImporter');
        Cms::registerCmsPluginRoutes('CmsContentImporter', function () {
            ContentImporter::routes();
        });
    }

}
