<?php

namespace Anacreation\CmsContentImporter\Services;

use Anacreation\Cms\ContentModels\FileContent;
use Anacreation\Cms\Contracts\CmsPageInterface;
use Anacreation\Cms\Entities\ContentObject;
use Anacreation\Cms\Models\Language;
use Anacreation\Cms\Models\Page;
use Anacreation\Cms\Services\ContentService;
use Anacreation\Cms\Services\TemplateParser;
use Anacreation\CmsContentImporter\Entities\ImportContentDTO;
use Anacreation\CmsContentImporter\Exceptions\ImportContentFileNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
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

        Validator::extend('hasPageUri', function ($attribute, $value, $input) {

            return !!$this->findPage($this->sanitizedUri($value));

        });
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
        })->map(function (Collection $data) use (&$errors): ?ImportContentDTO {
            try {
                $result = $this->createContentDTO($data->toArray());

                return $result;
            } catch (ImportContentFileNotFoundException $e) {
                $errors[] = $e->getMessage();

                return null;
            }

        })->reject(null)
                   ->each(function (ImportContentDTO $contentDTO): void {
                       $this->service->updateOrCreateContentIndexWithContentObject(
                           $contentDTO->getOwner(),
                           $contentDTO->getContentObject());
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

        $sanitizedUri = $this->sanitizedUri($data['uri']);
        $template = $this->getPageTemplate($sanitizedUri);
        if (is_null($template)) {
            Log::error("Not page found when importing content. uri:" . $data['uri'] . ', identifier:' . $data['identifier']);

            return false;
        }

        $identifiers = $this->templateParser->loadPredefinedIdentifiers("",
            $template);
        $rules = [
            'uri'           => 'required|hasPageUri',
            'language_code' => 'required|exists:languages,code',
            'identifier'    => 'required|in:' . implode(",",
                    array_keys($identifiers)),
            'content'       => 'nullable',
        ];

        $validator = Validator::make($data, $rules);

        return $validator->passes();
    }

    private function createContentDTO(array $data): ImportContentDTO {

        $sanitizedUri = $this->sanitizedUri($data['uri']);

        $page = $this->findPage($sanitizedUri);
        $languageId = Language::whereCode($data['language_code'])->first()->id;

        $identifiers = $this->templateParser->loadPredefinedIdentifiers("",
            $page->template);

        $contentObject = $this->createContentObject($data, $languageId,
            $identifiers);

        $dto = (new ImportContentDTO)->setContentObject($contentObject)
                                     ->setOwner($page);

        return $dto;
    }

    /**
     * @param array $data
     * @param       $languageId
     * @param array $identifiers
     * @return ContentObject
     * @throws \Exception
     */
    private function createContentObject(
        array $data, $languageId, array $identifiers
    ): ContentObject {

        if ($this->identifierIsFileType($data, $identifiers)) {

            $path = storage_path($data['content']);
            if (File::exists($path)) {
                $fileInfo = new \SplFileInfo($path);

                $file = new UploadedFile($fileInfo->getPathname(),
                    $fileInfo->getFilename());

                $contentObject = new ContentObject($data['identifier'],
                    $languageId,
                    "", $identifiers[$data['identifier']]['type'], $file);
            } else {
                throw new ImportContentFileNotFoundException('File not found!');
            }
        } else {
            $contentObject = new ContentObject($data['identifier'], $languageId,
                $data['content'],
                $identifiers[$data['identifier']]['type']);
        }


        return $contentObject;
    }

    private function identifierIsFileType(array $data, array $identifiers
    ): bool {
        $class = (new ContentService)->convertToContentTypeClass($identifiers[$data['identifier']]['type']);
        $_ = new $class;

        return $_ instanceof FileContent;
    }

    private function sanitizedUri(string $uri): string {
        $result = str_replace(config('app.url'), '', $uri);

        $firstChar = mb_substr($result, 0, 1, "UTF-8");

        if ($firstChar === "/") {
            return substr($result, 1);
        }

        return $result;
    }

    /**
     * @param string $sanitizedUri
     * @return mixed
     */
    private function getPageTemplate(string $sanitizedUri): ?string {
        if ($page = $this->findPage($sanitizedUri)) {
            $template = $page->template;

            return $template;
        }


        return null;
    }

    private function findPage(string $sanitizedUri): ?CmsPageInterface {
        $allPages = app(CmsPageInterface::class)->getAllPages();

        return $allPages[$sanitizedUri] ?? null;

    }

}