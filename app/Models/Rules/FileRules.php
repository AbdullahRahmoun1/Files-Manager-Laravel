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
                'name' => ['string','between:1,255',],
                'parent_id' => [Rule::exists('files','id')->whereNull('path')]
            ],
            app(File::class)->getAllFieldsRules()
        );
    }
    protected function defineMessages(): array
    {
        return [];
    }
}
