<?php

namespace App\Http\Requests;

use App\Models\Vcard;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
    public function rules(Request $request)
    {
        $vcard = Vcard::where('phone_number', $request->vcard)->get();
        //dd($vcard->first()->max_debit);

        $max_debit = $vcard->first()->max_debit;
        // $max_debit = $vcard->max_debit;
        $balance = $vcard->first()->balance;
        // $balance = $vcard->balance;

        // . é para concatenar strings
        $rules = [
            
            //

            
            'payment_type' => 'required',
            'vcard' => 'required|regex:/(9)[0-9]{8}/|exists:vcards,phone_number',
            'date' => 'required|date_format:Y-m-d|after:yesterday',
            // 'datetime' => 'required|date_format:Y-m-d H:i:s|after:yesterday',
            'datetime' => 'required|after:yesterday',
            'type' => 'required|in:D,C',
            'old_balance' => 'required|numeric',
            'new_balance' => 'required|numeric',
            'pair_transaction'=> 'nullable',
            'pair_vcard'=> 'nullable|different:vcard|exists:vcards,phone_number',
            'category_id'=> 'nullable',
            'description'=> 'nullable',
            //'value' => 'required|numeric|min:0|lte:balance|lte:max_debit',
            //'payment_type' => 'required'
            'password' => 'current_password:api'
        ];

        if ($request->get('type') == 'D'){
        
            $rules['value'] = 'required|numeric|min:0|lte:' .$balance.'|lte:' .$max_debit;
        }

        if ($request->get('type') == 'C'){
        
            $rules['value'] = 'required|numeric|min:0';
        }

        if ($request->get('payment_type') == 'VCARD'){
        
            $rules['payment_reference'] = 'required|regex:/(9)[0-9]{8}/|different:vcard';
        }
        
        if($request->get('payment_type') == 'IBAN'){
            $rules['payment_reference'] = 'required|regex:/[A-Za-z]{2}[0-9]{23}/';
        }

        if($request->get('payment_type') == 'MASTERCARD'){
            $rules['payment_reference'] = 'required|regex:/[0-9]{16}/';
        }
        if($request->get('payment_type') == 'MB'){
            $rules['payment_reference'] = 'required|regex:/[0-9]{5}+(-[0-9]{9}+)/';
        }
        if($request->get('payment_type') == 'MBWAY'){
            $rules['payment_reference'] = 'required|regex:/(9)[0-9]{8}/';
        }
        if($request->get('payment_type') == 'PAYPAL'){
            $rules['payment_reference'] = 'required|email';
        }
        if($request->get('payment_type') == 'VISA'){
            $rules['payment_reference'] = 'required|regex:/[0-9]{16}/';
        }

        if($request->get('payment_type') == 'ADMIN'){
            $rules['payment_reference'] = 'required|email';
        }
        

        return $rules;
    }
}
