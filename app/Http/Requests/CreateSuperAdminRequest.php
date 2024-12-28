<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\SuperAdminRules;

class CreateSuperAdminRequest extends FormRequest
{
    public function rules()
    {
        return SuperAdminRules::required()
            ->getRules();
    }

    public function messages(){
        return SuperAdminRules::getMessages();
    }
}
