<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PolicyRequest extends FormRequest
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
            'title_en' => 'required',
            'body_en' => 'required',
            'title_ar' => 'required',
            'body_ar' => 'required',
        ];
    }

    public function messages()
    {
        if(app()->getLocale() == 'en'){

            return
            [
                'title_en.required' => 'The Title In English Field Is Required',
                'body_en.required' => 'The Body In English Field Is Required',
                'title_ar.required' => 'The Title In Arabic Field Is Required',
                'body_ar.required' => 'The Body In Arabic Field Is Required',
            ];
        }else{
            return
            [
                'body_en.required' => 'المحتوى بالأنجليزية مطلوب',
                'body_ar.required' => 'المحتوى بالعربية مطلوب',
                'title_ar.required' => 'حقل العنوان بالأنجليزية مطلوب',
                'title_ar.required' => 'حقل العنوان بالعربية مطلوب',
            ];

        }

    }
}
