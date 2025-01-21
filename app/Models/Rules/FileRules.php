<?php

namespace App\Models\Rules;

use App\Models\File;
use Illuminate\Validation\Rule;
use Wever\Laradot\App\Models\Rules\BaseRules;

class FileRules extends BaseRules
{
    protected function defineRules(): array
    {
        return array_merge(
            [
                'name' => ['string', 'between:1,255',],
                'parent_id' => [Rule::exists('files', 'id')->whereNull('path')],
                'path' => [
                    'file',
                    function ($attribute, $value, $fail) {
                        $mimeType = $value->getMimeType();
                        if (str_starts_with($mimeType, 'image/') || str_starts_with($mimeType, 'video/')) {
                            $fail("The $attribute must not be an image or video.");
                        }
                    },
                ]
            ],
        );
    }
    protected function defineMessages(): array
    {
        return [];
    }
}
