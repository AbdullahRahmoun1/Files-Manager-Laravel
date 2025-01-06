<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\CheckInRules;

class CreateCheckInRequest extends FormRequest
{
    public function rules()
    {
        return CheckInRules::required()
            ->getRules();
    }

    public function messages(){
        return CheckInRules::getMessages();
    }
}
