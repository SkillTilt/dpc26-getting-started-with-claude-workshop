<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBidRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * Sellers cannot bid on their own items.
     */
    public function authorize(): bool
    {
        return $this->route('item')->seller_id !== $this->user()->id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $item = $this->route('item');
        $minBid = $item->current_price + 1.00;

        return [
            'amount' => ['required', 'numeric', 'min:' . $minBid],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator(\Illuminate\Validation\Validator $validator): void
    {
        $validator->after(function (\Illuminate\Validation\Validator $validator) {
            $item = $this->route('item');

            if ($item->status !== 'active') {
                $validator->errors()->add('item', 'This auction is no longer active.');
            }

            if ($item->ends_at < now()) {
                $validator->errors()->add('item', 'This auction has already ended.');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $item = $this->route('item');
        $minBid = $item->current_price + 1.00;

        return [
            'amount.min' => 'Bid must be at least $' . number_format($minBid, 2) . '.',
        ];
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization(): void
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('You cannot bid on your own item.');
    }
}
