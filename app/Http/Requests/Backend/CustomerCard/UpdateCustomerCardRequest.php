<?php

namespace App\Http\Requests\Backend\CustomerCard;

use App\Models\CustomerCard\CustomerCard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCustomerCardRequest.
 */
class UpdateCustomerCardRequest extends FormRequest
{
    /**
     * Determine if the customerCard is authorized to make this request.
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
            'name' => ['required', 'max:100'],
            'start_number' => ['required', 'max:100'],
            'end_number' => ['required', 'max:100'],
            'card_number' => ['sometimes', 'max:100'],
            'due_date' => ['sometimes', 'max:100'],
            'day_statement' => ['required', 'max:31','integer'],
            'currency_limit' => ['required'],
            'bank_id' => ['required'],
            'note' => ['sometimes'],
            'active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
        ];
    }

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException(__('Only the administrator can update this customerCard.'));
    }
}
