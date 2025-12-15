<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'idempotency_key' => ['required','string','max:64'],
            'items'           => ['required','array','min:1'],
            'items.*.product_id'   => ['nullable','integer'],
            'items.*.product_name' => ['required','string','max:255'],
            'items.*.unit_price'   => ['required','integer','min:0'],
            'items.*.qty'          => ['required','integer','min:1'],
            'voucher_code'         => ['nullable','string','max:32'],
            'shipping_address'     => ['required','string','max:500'],
            'shipping_method'      => ['required','string','in:standard,express,same_day'],
            'payment_method'       => ['required','string','in:bank_transfer,cod,e_wallet'],
        ];
    }
}
