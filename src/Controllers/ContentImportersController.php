<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-24
 * Time: 14:00
 */

namespace Anacreation\CmsContentImporter\Controllers;


use Anacreation\CmsContentImporter\Exports\ContentExport;
use Anacreation\CmsContentImporter\Services\ContentCreationService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
            'file' => 'required|min:0|mimes:csv,txt'
        ]);

        $result = $service->import($request->file);

        $msg = $service->headerError ?
            "The file header is not correct! Please verify." :
            'Total number of rows: ' . $result->get('count') . ". <br> " . ($result->get('count') - count($result->get('errors'))) . " rows of content was successfully created!";


        return redirect()->route('cms:plugins:contentImporters.index')
                         ->withStatus($msg)
                         ->withErrors($result->get('errors'));
    }

    public function downloadTemplate() {
        return Excel::download(new ContentExport, 'import_pages_template.xls');
    }

    public function getLatestLog() {

        $files = File::files(storage_path('logs'));

        $files = array_values(
            array_reverse(
                array_sort(
                    array_filter(
                        $files,
                        function (\SplFileInfo $fileInfo) {
                            return strpos($fileInfo->getFilename(),
                                    'content-import') !== false;
                        }),
                    function (\SplFileInfo $file) {
                        return $file->getMTime();
                    })));

        if (isset($files[0])) {
            $path = $files[0]->getPathname();

            return view('cms:contentImporter::log', compact('path'));
        }

        return redirect()->back()->withStatus("Not log available");

    }

}