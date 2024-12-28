<?php

namespace App\Models\Rules;

use Wever\Laradot\App\Models\Rules\BaseRules;

class GroupUserRules extends BaseRules
{
    // Define the rules specific to the model
    protected function defineRules(): array
    {
        return [
            'invitation_expires_at' => ['after:' . now()->addHour()],
            'group_id' => ['exists:groups,id'],
            'user_id'  => ['exists:users,id', 'different:'.(request()->user()->id??0)],
        ];
    }

    // Define custom messages specific to the model
    protected function defineMessages(): array
    {
        return [];
    }
}
