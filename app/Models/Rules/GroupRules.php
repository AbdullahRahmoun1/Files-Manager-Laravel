<?php
namespace App\Models\Rules;
use Wever\Laradot\App\Models\Rules\BaseRules;
class GroupRules extends BaseRules
{
    // Define the rules specific to the model
    protected function defineRules(): array
    {
        return [
            'name' => ['unique:groups,name','between:1,255'],
            'description' => ['between:1,255'],
            'color' => ['regex:/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/'],
            'lang' => ['in:ar,en'],
            'creator_id'=> ['forbidden'],
        ];
    }

    // Define custom messages specific to the model
    protected function defineMessages(): array
    {
        return [

        ];
    }
}

