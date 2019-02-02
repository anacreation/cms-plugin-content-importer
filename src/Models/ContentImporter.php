<?php
/**
 * Author: Xavier Au
 * Date: 14/4/2018
 * Time: 6:48 PM
 */

namespace Anacreation\CmsContentImporter\Models;


use Anacreation\CmsContentImporter\Controllers\ContentImportersController;
use Illuminate\Support\Facades\Route;

class ContentImporter
{
    public static function routes(): void {

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
                        Route::get('/download',
                            ContentImportersController::class . "@downloadTemplate")
                             ->name('cms:plugins:contentImporters.download');
                    });
            });
    }
}