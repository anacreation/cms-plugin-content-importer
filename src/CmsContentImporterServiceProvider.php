<?php

namespace Anacreation\CmsContentImporter;

use Anacreation\Cms\Models\Cms;
use Anacreation\Cms\Plugin\Plugin;
use Anacreation\CmsContentImporter\Commands\ContentImporterTestCommand;
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

    private function views() {
        $this->loadViewsFrom(__DIR__ . '/views', 'cms:contentImporter');

        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/cms:contentImporter'),
        ], 'cms:contentImporter');

    }

    private function registerCmsPlugin(): void {
        Cms::registerPlugin((new Plugin("CmsContentImporter"))
            ->setEntryPath('contentImporter', "Cms Import Content")
            ->setRoutes(ContentImporter::routes())
            ->setCommands([
                ContentImporterTestCommand::class
            ])
            ->setScheduleFunction(ContentImporter::schedule()));
    }

}
