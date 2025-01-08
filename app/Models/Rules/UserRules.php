<?php
namespace App\Models\Rules;
use Wever\Laradot\App\Models\Rules\BaseRules;
class UserRules extends BaseRules
{
    // Define the rules specific to the model
    protected function defineRules(): array
    {
        return [
            'email' => ['email','unique:users,email'],
            'name' => ['between:1,100','unique:users,name'],
            'password' => ['between:1,100'],
            'fcm_token' => ['string','between:1,255']
        ];
    }

    // Define custom messages specific to the model
    protected function defineMessages(): array
    {
        return [

        ];
    }
}

