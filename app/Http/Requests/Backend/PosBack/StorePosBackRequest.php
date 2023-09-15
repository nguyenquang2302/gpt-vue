<?php

namespace App\Http\Requests\Backend\PosBack;

use App\Models\Pos\Pos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class StorePosBackRequest.
 */
class StorePosBackRequest extends FormRequest
{
    /**
     * Determine if the pos is authorized to make this request.
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
            'name' => ['required'],
            'money' => ['required'],
            'pos_id' => ['sometimes'],
            'note' => ['sometimes'],
            'lo' => ['required'],
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
