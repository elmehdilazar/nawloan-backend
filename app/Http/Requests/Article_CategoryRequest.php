<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Article_CategoryRequest extends FormRequest
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

                'category_en' => 'required',
                'category_ar' => 'required',
                'category_desc_en' => 'required',
                'category_desc_ar' => 'required',
                'meta_title_en' => 'required',
                'meta_title_ar' => 'required',
                'slug_en' => 'required',
                'slug_ar' => 'required',
                'meta_desc_en' => 'required',
                'meta_desc_ar' => 'required',
        ];
    }

    public function messages()
    {
        if(app()->getLocale() == 'en'){

            return
            [
                'category_en.required' => 'The Category In English Field Is Required',
                'category_ar.required' => 'The Category In Arabic Field Is Required',
                'category_desc_en.required' => 'The Category Description In English Field Is Required',
                'category_desc_ar.required' => 'The Category Description In Arabic Field Is Required',
                'meta_title_en.required'=> 'The Meta Title English Field Is Required',
                'meta_title_ar.required'=> 'The Meta Title In Arabic Field Is Required',
                'slug_en.required' => 'The Slug In English Field Is Required',
                'slug_ar.required' => 'The Slug In Arabic Field Is Required',
                'meta_desc_en.required' => 'The Meta Description In English Field Is Required',
                'meta_desc_ar.required' => 'The Meta Description In Arabic Field Is Required',
            ];
        }else{
            return
            [
                'category_en.required' => 'حقل الأسم بالأنجليزية مطلوب',
                'category_ar.required' => 'حقل الأسم بالعربية مطلوب',
                'category_desc_en.required' => 'حقل العنوان بالأنجليزية مطلوب',
                'category_desc_ar.required' => 'حقل العنوان بالعربية مطلوب',
                'meta_title_en.required'=> 'حقل عنوان الميتا بالأنجليزية مطلوب',
                'meta_title_ar.required'=> 'حقل عنوان الميتا بالعربية مطلوب ',
                'slug_en.required' => 'حقل السبيكة بالأنجليزية مطلوب',
                'slug_ar.required' => 'حقل السبيكة بالعربية مطلوب ',
                'meta_desc_en.required' => 'حقل وصف الميتا بالأنجليزية مطلوب',
                'meta_desc_ar.required' => 'حقل وصف الميتا بالعربية مطلوب',

            ];

        }

    }
}
