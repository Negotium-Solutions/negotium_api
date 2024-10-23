<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ActivityRequest extends FormRequest
{
    const RADIO = 7;
    const CHECKBOX = 8;
    const DROPDOWN = 9;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $requestArray = [
            'name' => 'required|string',
            'step_id' => 'required|string',
            'dynamic_model_field_type_id' => 'integer|required'
        ];

        $options = [];
        if (in_array($this->input('dynamic_model_field_type_id'), [self::RADIO, self::CHECKBOX, self::DROPDOWN])) {
            $options = ['options' => 'required|array'];
        }

        return array_merge($requestArray, $options);
    }
}
