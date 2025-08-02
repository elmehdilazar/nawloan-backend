<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;

class LoginRequest extends FormRequest
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
            'phoneOrMail' => 'required',
            'password' => 'required'
        ];
    }
    public function getCredentials()
    {
        // The form field for providing phone or password
        // have name of "phoneOrMail", however, in order to support
        // logging users in with both (phoneOrMail and email)
        // we have to check if user has entered one or another
        $phone = $this->get('phoneOrMail');

        if ($this->isEmail($phone)) {
            return [
                'email' => $phone,
                'password' => $this->get('password')
            ];
        }

        return $this->only('phone', 'password');
    }

    private function isEmail($param)
    {
        $factory = $this->container->make(ValidationFactory::class);

        return !$factory->make(
            ['phone' => $param],
            ['phone' => 'email']
        )->fails();
    }
}
