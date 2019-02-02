<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-24
 * Time: 14:00
 */

namespace Anacreation\CmsContentImporter\Controllers;


use Anacreation\Cms\Models\Page;
use Anacreation\Cms\Models\Permission;
use Anacreation\Cms\Services\ContentService;
use Anacreation\Cms\Services\TemplateParser;
use Anacreation\CmsContentImporter\Exports\ContentExport;
use Anacreation\CmsContentImporter\Imports\PageImport;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class ContentImportersController extends Controller
{
    public function index(Request $request, ContentService $service) {

        return view('cms:contentImporter::index');
    }

    /**
     * @param \Illuminate\Http\Request                 $request
     * @param \Anacreation\Cms\Services\ContentService $service
     * @return mixed
     */
    public function load(Request $request, ContentService $service) {

        $this->validate($request, [
            'file' => 'required|min:0'
        ]);

        $collection = Excel::toCollection(new PageImport, $request->file)
                           ->first();

        $errors = $this->execute($collection);

        return redirect()->route('cms:plugins:content.index')
                         ->withStatus($collection->count() - count($errors) . " content created!")
                         ->withErrors($errors);
    }

    public function downloadTemplate() {
        return Excel::download(new ContentExport, 'import_pages_template.xls');
    }

    /**
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    private function execute(Collection $collection): array {

        $errors = [];

        $collection->filter(function ($data) use (&$errors) {
            if ($this->validateImportData($data) === false) {
                $errors[] = $data['uri'] ?? "No Uri Found";

                return false;
            };

            return true;
        })->map(function (array $data) {
            return $this->transferData($data);
        })->each(function ($data) {
            Page::create($data);
        });

        return $errors;
    }

    private function validateImportData(array $data): bool {

        $layouts = getLayoutFiles()['layouts'];

        $service = new TemplateParser;
        $identifiers = $service->loadPredefinedIdentifiers(null,
            Page::whereUri($data['uri'])->first()->template);

        dd($identifiers);

        $rules = [
            'uri'           => 'required|exists:pages,uri',
            'language_code' => 'required|exists:languages,code',
            'identifier'    => 'required|in:'.implode(",", $identifiers->toArray()),
            'content'       => 'nullable',
        ];

        $validator = Validator::make($data, $rules);

        return $validator->passes();
    }

    private function transferData(array $data) {
        return [
            'uri'           => $data['uri'],
            'template'      => $data['template'],
            'has_children'  => $data['has_children'],
            'is_active'     => $data['is_active'],
            'is_restricted' => $data['is_restricted'],
            'order'         => $data['order'],
            'permission_id' => Permission::whereCode($data['code'])
                                         ->first()->idF,
        ];
    }


}