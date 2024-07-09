<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'account_type' => ['required', 'in:phone,card,email'],
            'account_id' => ['required', 'string'],
            'description' => ['required'],
            'payment_id' => ['required'],
            'payment_amount' => ['required'],
            'payment_time' => ['required'],
        ];
    }
}
