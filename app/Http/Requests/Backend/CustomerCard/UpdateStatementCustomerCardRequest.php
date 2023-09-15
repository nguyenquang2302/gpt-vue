<?php

namespace App\Http\Requests\Backend\CustomerCard;

use App\Models\CustomerCard\CustomerCard;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCustomerCardRequest.
 */
class UpdateStatementCustomerCardRequest extends FormRequest
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
            'currency_payment' =>['sometimes'],
            'due_date2' =>['sometimes'],
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
