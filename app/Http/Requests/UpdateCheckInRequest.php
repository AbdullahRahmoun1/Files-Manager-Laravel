<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\CheckInRules;

class UpdateCheckInRequest extends FormRequest
{
    public function rules()
    {
        return CheckInRules::getRules();
    }

    public function messages(){
        return CheckInRules::getMessages();
    }
}
