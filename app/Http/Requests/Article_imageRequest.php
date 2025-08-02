<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Article_imageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [

                'article_id' => 'required',
                'images' => 'required',

        ];
    }

    public function messages()
    {
        if(app()->getLocale() == 'en'){

            return
            [
                'aricle_id.required' => 'The Article ID  Is Required',
                'images.required' => 'should Choose at least one image ',
 ];
        }else{
            return
            [
                'aricle_id.required' => 'كود المقال مطلوب ',
                'images.required' => 'يجب اختيار صورة واحده على الاقل',
            ];

        }

    }
}
