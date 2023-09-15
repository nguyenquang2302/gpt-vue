<?php

namespace App\Http\Requests\Backend\Branch;

use App\Models\Branch\Branch;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StoreBranchRequest.
 */
class StoreBranchRequest extends FormRequest
{
    /**
     * Determine if the branch is authorized to make this request.
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
            'province_id' => ['sometimes'],
            'district_id' => ['sometimes'],
            'ward_id' => ['sometimes'],
            'address' => ['sometimes']
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
