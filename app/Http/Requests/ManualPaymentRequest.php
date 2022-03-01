<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class ManualPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'event_datetime'    => 'date|required',
            'customer_name'     => 'required|min:5',
            'email'             => 'email|required|min:5|max:255',
            'product_id'        => 'required',
            'amount_collected'  => 'numeric|required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'event_datetime'    => 'Payment Date',
            'customer_name'     => 'Customer Name',
            'email'             => 'Email Address',
            'amount_collected'  => 'Amount Collected',

        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}
