<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVoucherRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'code'            => ['required','string','max:32','regex:/^[A-Z0-9\-]+$/','unique:vouchers,code'],
            'type'            => ['required','in:percentage,fixed'],
            'value'           => ['required','integer','min:1'],
            'max_uses'        => ['nullable','integer','min:0'],
            'per_user_limit'  => ['nullable','integer','min:0'],
            'min_order_amount'=> ['nullable','integer','min:0'],
            'starts_at'       => ['nullable','date'],
            'ends_at'         => ['nullable','date','after:starts_at'],
            'is_active'       => ['boolean'],
        ];
    }
}
