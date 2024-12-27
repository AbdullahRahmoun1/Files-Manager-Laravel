<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
class LoginUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required','email'],
            'password' => ['required','between:1,100']
        ];
    }
}
