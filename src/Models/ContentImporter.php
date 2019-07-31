<?php
/**
 * Author: Xavier Au
 * Date: 14/4/2018
 * Time: 6:48 PM
 */

namespace Anacreation\CmsContentImporter\Models;


use Anacreation\CmsContentImporter\Controllers\ContentImportersController;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class ContentImporter
{
    public static function routes(): callable {
        return function () {
            Route::group(['prefix' => config('admin.route_prefix')],
                function () {
                    Route::group([
                        'middleware' => ['auth:admin', 'web'],
                        'prefix'     => 'contentImporter'
                    ],
                        function () {
                            Route::get('/',
                                ContentImportersController::class . "@index")
                                 ->name('cms:plugins:contentImporters.index');
                            Route::post('/',
                                ContentImportersController::class . "@load")
                                 ->name('cms:plugins:contentImporters.action');

                            Route::get('/latest_log',
                                ContentImportersController::class . "@getLatestLog")
                                 ->name('cms:plugins:contentImporters.latest_log');

                            Route::get('/download',
                                ContentImportersController::class . "@downloadTemplate")
                                 ->name('cms:plugins:contentImporters.download');
                        });
                });
        };
    }

    public static function schedule(): callable {
        return function () {
            /** @var Schedule $schedule */
            $schedule = app(Schedule::class);

            $schedule->call(function () {
                Log::info("Cms Importer schedule");
            })->everyMinute();

            $schedule->call(function () {
                Log::info("Cms Importer another schedule job");
            })->everyMinute();
        };
    }
}