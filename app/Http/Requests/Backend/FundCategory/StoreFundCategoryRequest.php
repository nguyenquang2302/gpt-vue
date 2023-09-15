<?php

namespace App\Http\Requests\Backend\FundCategory;

use App\Models\FundCategory\FundCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreFundCategoryRequest.
 */
class StoreFundCategoryRequest extends FormRequest
{
    /**
     * Determine if the fundCategory is authorized to make this request.
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
            'type' => ['required'],
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
