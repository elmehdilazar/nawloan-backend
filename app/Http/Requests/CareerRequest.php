<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class CareerRequest extends FormRequest
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
            'name_en' => 'required',
            'name_ar' => 'required',
            'address_en' => 'required',
            'address_ar' => 'required',
            'category_id' => 'required|numeric',
            'desc_en' => 'required',
            'desc_ar' => 'required',
        ];
    }
    public function messages()
    {
        if(app()->getLocale() == 'en'){

            return
            [
                'name_en.required' => 'The Name In English Field Is Required',
                'name_ar.required' => 'The Name In Arabic Field Is Required',
                'address_en.required' => 'The Address In English Field Is Required',
                'address_ar.required' => 'The Address In Arabic Field Is Required',
                'category_id.required'=> 'The Category Field Is Required',
                'category_id.numeric'=> 'The Category Field Should Be Is Number',
                'desc_en.required' => 'The Description In English Field Is Required',
                'desc_ar.required' => 'The Description In Arabic Field Is Required',
            ];
        }else{
            return
            [
                'name_en.required' => 'حقل الأسم بالأنجليزية مطلوب',
                'name_ar.required' => 'حقل الأسم بالعربية مطلوب',
                'address_en.required' => 'حقل العنوان بالأنجليزية مطلوب',
                'address_ar.required' => 'حقل العنوان بالعربية مطلوب',
                'catergory_id.required' => 'حقل الفئة مطلوب',
                'catergory_id.numeric'   => 'حقل الفئة يجب ان يكون رقم',
                'desc_en.required' =>  'حقل الوصف بالأنجليزية مطلوب',
                'desc_ar.required' => 'حقل الوصف بالعربية مطلوب',

            ];

        }

    }

    // protected function failedValidation(Validator $validator)
    // {
    //     throw new HttpResponseException(response()->json($validator->errors(), 422));
    // }
}
