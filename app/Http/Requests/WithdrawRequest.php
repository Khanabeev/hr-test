<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
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
            'description' => ['required', 'string'],
            'payment_id' => ['required', 'string'],
            'payment_amount' => ['required', 'numeric'],
            'payment_time' => ['required', 'numeric'],
        ];
    }
}
