<?php

namespace Anacreation\CmsContentImporter\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContentExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection() {
        return collect([
            [
                'dummy-page-uri',
                'en',
                'title',
                'this is dummy title',
            ]
        ]);
    }

    /**
     * @return array
     */
    public function headings(): array {
        return [
            'uri',
            'language_code',
            'identifier',
            'content',
        ];
    }
}
