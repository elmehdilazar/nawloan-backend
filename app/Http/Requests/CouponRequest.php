<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
        'name' => 'required|unique:coupons,name,' . $this->id,
        'code' => 'required|unique:coupons,code,' . $this->id,
        'type' => ['required', Rule::in(['percentage', 'fixed'])],
        'start_date' => 'required|date',
        'expiry_date' => 'required|date|after_or_equal:start_date',
        'number_availabe' => 'required|numeric|min:1',
        'discount' => 'required|numeric|min:0',
        'apply_to' => ['required', Rule::in(['customer', 'enterprise', 'all'])],
    ];
}


  public function messages()
{
    if (app()->getLocale() == 'en') {
        return [
            'name.required' => 'The Coupon Name is required',
            'name.unique' => 'The Coupon Name already exists',
            'code.required' => 'The Coupon Code is required',
            'code.unique' => 'The Coupon Code already exists',
            'type.required' => 'The Coupon Type is required',
            'type.in' => 'Invalid coupon type',
            'start_date.required' => 'The Start Date is required',
            'expiry_date.required' => 'The Expiry Date is required',
            'expiry_date.after_or_equal' => 'The Expiry Date must be after or equal to the Start Date',
            'number_availabe.required' => 'Available number is required',
            'discount.required' => 'The discount value is required',
            'apply_to.required' => 'Apply To is required',
            'apply_to.in' => 'Invalid value for Apply To',
        ];
    } else {
        return [
            'name.required' => 'اسم الكوبون مطلوب',
            'name.unique' => 'اسم الكوبون موجود بالفعل',
            'code.required' => 'كود الكوبون مطلوب',
            'code.unique' => 'كود الكوبون موجود بالفعل',
            'type.required' => 'نوع الكوبون مطلوب',
            'type.in' => 'نوع الكوبون غير صالح',
            'start_date.required' => 'تاريخ البدء مطلوب',
            'expiry_date.required' => 'تاريخ الانتهاء مطلوب',
            'expiry_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
            'number_availabe.required' => 'العدد المتاح مطلوب',
            'discount.required' => 'قيمة الخصم مطلوبة',
            'apply_to.required' => 'يجب اختيار نوع المستخدم المستهدف',
            'apply_to.in' => 'قيمة غير صالحة للحقل Apply To',
        ];
    }
}


    //     protected function failedValidation(Validator $validator)
    // {
    //     if($validator){
    //         return $validator;
    //         throw new HttpResponseException(response()->json($validator->errors(), 422));

    //     }
    //  }
}
