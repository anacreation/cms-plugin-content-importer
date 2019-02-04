<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-24
 * Time: 14:00
 */

namespace Anacreation\CmsContentImporter\Controllers;


use Anacreation\CmsContentImporter\Exports\ContentExport;
use Anacreation\CmsContentImporter\Imports\PageImport;
use Anacreation\CmsContentImporter\Services\ContentCreationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ContentImportersController extends Controller
{
    public function index() {

        return view('cms:contentImporter::index');
    }

    /**
     * @param \Illuminate\Http\Request                 $request
     * @param \Anacreation\Cms\Services\ContentService $service
     * @return mixed
     */
    public function load(Request $request, ContentCreationService $service) {
        
        $this->validate($request, [
            'file' => 'required|min:0'
        ]);

        $collection = Excel::toCollection(new PageImport, $request->file)
                           ->first();

        $errors = $service->create($collection);

        return redirect()->route('cms:plugins:contentImporters.index')
                         ->withStatus($collection->count() - count($errors) . " content created!")
                         ->withErrors($errors);
    }

    public function downloadTemplate() {
        return Excel::download(new ContentExport, 'import_pages_template.xls');
    }

}