<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\SuperAdminRules;

class UpdateSuperAdminRequest extends FormRequest
{
    public function rules()
    {
        return SuperAdminRules::getRules();
    }

    public function messages(){
        return SuperAdminRules::getMessages();
    }
}
