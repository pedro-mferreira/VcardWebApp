<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVcardRequest extends FormRequest
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
            'phone_number' => 'required|regex:/(9)[0-9]{8}/|unique:vcards',
            'password' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'photo_url' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'confirmation_code' => 'required|digits:4'
        ];
    }
}
