<?php

namespace App\Http\Requests\Backend\Branch;

use App\Models\Branch\Branch;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateBranchRequest.
 */
class UpdateBranchRequest extends FormRequest
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

    /**
     * Handle a failed authorization attempt.
     *
     * @return void
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    protected function failedAuthorization()
    {
        throw new AuthorizationException(__('Only the administrator can update this branch.'));
    }
}
