<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ArticleRequest extends FormRequest
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
                'images' =>'nullable',
                'category_id' =>'required',
                'article_en' => 'required',
                'article_ar' => 'required',
                'article_desc_en' => 'required',
                'article_desc_ar' => 'required',
                'meta_title_en' => 'required',
                'meta_title_ar' => 'required',
                'tage_en' => 'required',
                'tage_ar' => 'required',
                'meta_desc_en' => 'required',
                'meta_desc_ar' => 'required',
                'article_date' => 'required',
        ];
    }

    public function messages()
    {
        if(app()->getLocale() == 'en'){
            return
            [
                'category_id.requird'  =>'The Category field is requierd',
                // 'images.image'        =>'Please Select Only Images',
                'article_date.requird'=> 'The Date field is requierd',
                'article_en.required' => 'The article In English Field Is Required',
                'article_ar.required' => 'The article In Arabic Field Is Required',
                'article_desc_en.required' => 'The article Description In English Field Is Required',
                'article_desc_ar.required' => 'The article Description In Arabic Field Is Required',
                'meta_title_en.required'=> 'The Meta Title English Field Is Required',
                'meta_title_ar.required'=> 'The Meta Title In Arabic Field Is Required',
                'tage_en.required' => 'The tage In English Field Is Required',
                'tage_ar.required' => 'The tage In Arabic Field Is Required',
                'meta_desc_en.required' => 'The Meta Description In English Field Is Required',
                'meta_desc_ar.required' => 'The Meta Description In Arabic Field Is Required',
            ];
        }else{
            return
            [
                'category_id.requird'  =>'حقل الفئة مطلوب',
                // 'images.image'        =>'من فضلك اختر صورة',
                'article_date.requird'         => 'حقل التاريخ مطلوب',
                'article_en.required' => 'حقل الأسم بالأنجليزية مطلوب',
                'article_ar.required' => 'حقل الأسم بالعربية مطلوب',
                'article_desc_en.required' => 'حقل العنوان بالأنجليزية مطلوب',
                'article_desc_ar.required' => 'حقل العنوان بالعربية مطلوب',
                'meta_title_en.required'=> 'حقل عنوان الميتا بالأنجليزية مطلوب',
                'meta_title_ar.required'=> 'حقل عنوان الميتا بالعربية مطلوب ',
                'tage_en.required' => 'حقل السبيكة بالأنجليزية مطلوب',
                'tage_ar.required' => 'حقل السبيكة بالعربية مطلوب ',
                'meta_desc_en.required' => 'حقل وصف الميتا بالأنجليزية مطلوب',
                'meta_desc_ar.required' => 'حقل وصف الميتا بالعربية مطلوب',

            ];

        }

    }

        protected function failedValidation(Validator $validator)
    {
        return $validator;
        throw new HttpResponseException(response()->json($validator->errors(), 422));
    }
}
