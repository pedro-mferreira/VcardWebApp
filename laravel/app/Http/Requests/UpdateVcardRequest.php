<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVcardRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'new_confirmation_code' => 'digits:4',
            'new_password' => 'different:old_password',
            'old_password' => 'required_with:new_password,new_confirmation_code|current_password:api'
        ];
    }
}
