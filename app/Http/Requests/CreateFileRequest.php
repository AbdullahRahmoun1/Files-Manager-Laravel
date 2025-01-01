<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Rules\FileRules;

class CreateFileRequest extends FormRequest
{
    public function rules()
    {
        return FileRules::required('parent_id','path')
            ->append('path',['required_if:is_folder,false'])
            ->append('is_folder',['required','boolean'])
            ->append('group_id',['required','exists:groups,id'])
            ->getRules();
    }

    public function messages(){
        return FileRules::getMessages();
    }
}
