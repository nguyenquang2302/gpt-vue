<?php

namespace App\Http\Requests\Backend\Bank;

use App\Models\Bank\Bank;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreBankRequest.
 */
class StoreBankRequest extends FormRequest
{
    /**
     * Determine if the bank is authorized to make this request.
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
            'accId' => ['required', 'max:100'],
            'shortName' => ['required', 'max:100'],
            'active' => ['sometimes', 'in:1'],
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
}
