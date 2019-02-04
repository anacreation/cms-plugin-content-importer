<?php
namespace Anacreation\CmsContentImporter\Services;

use Anacreation\Cms\Models\Page;
use Anacreation\Cms\Services\ContentService;
use Anacreation\Cms\Services\TemplateParser;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Author: Xavier Au
 * Date: 2019-02-03
 * Time: 02:21
 */
class ContentCreationService
{
    /**
     * @var \Anacreation\Cms\Services\TemplateParser
     */
    private $templateParser;
    private $service;

    /**
     * ContentCreationService constructor.
     * @param \Anacreation\Cms\Services\TemplateParser $templateParser
     * @param \Anacreation\Cms\Services\ContentService $service
     */
    public function __construct(
        TemplateParser $templateParser, ContentService $service
    ) {
        $this->templateParser = $templateParser;

        $this->service = $service;
    }


    /**
     * @param \Illuminate\Support\Collection $collection
     * @return array
     */
    public function create(Collection $collection): array {

        $errors = [];

        $collection->filter(function ($data) use (&$errors): bool {
            if ($this->validateImportData($data->toArray()) === false) {
                $errors[] = $data['uri'] ?? "No Uri Found";

                return false;
            };

            return true;
        })->map(function (array $data): array {
            return [
                'contentObject' => $this->createContentObject($data),
                'pageUri'       => $data['uri']
            ];
        })->each(function ($data): void {
            $this->service->updateOrCreateContentIndexWithContentObject(
                Page::whereUri($data['pageUri'])->firstOrFail(),
                $data['contentObject']);
        });

        return $errors;
    }

    /**
     * @param array $data
     * @return array
     */


    /**
     * @param array $data
     * @return bool
     */
    private function validateImportData(array $data): bool {
        try {
            $template = Page::whereUri($data['uri'])
                            ->firstOrFail()->template;
        } catch (\Exception $e) {

            Log::error("Not page found when importing content. uri:" . $data['uri'] . ', identifier:' . $data['identifier']);

            return false;
        }
        $identifiers = $this->templateParser->loadPredefinedIdentifiers("",
            $template);
        $rules = [
            'uri'           => 'required|exists:pages,uri',
            'language_code' => 'required|exists:languages,code',
            'identifier'    => 'required|in:' . implode(",",
                    array_keys($identifiers)),
            'content'       => 'nullable',
        ];

        $validator = Validator::make($data, $rules);

        return $validator->passes();
    }

}