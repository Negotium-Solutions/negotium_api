<?php

namespace App\Http\Requests\Tenant;

use App\Rules\SouthAfricanIdNumber;
use App\Rules\SouthAfricanPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class DynamicModelFieldRequest extends FormRequest
{
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
        if ( $this->has('validation') && $this->input('validation') !== 1) {
            return [];
        }

        $validationArray = [];
        $steps = $this->input('steps');
        foreach ($steps as $step)
        {
            if ( $this->has('step_id') && $this->input('step_id') !== $step['id'] ) {
                continue;
            }

            foreach ($step['fields'] as $field) {
                $validations = [];
                if (!isset($field['validations'])) {
                    continue;
                }
                foreach ($field['validations'] as $validation) {
                    switch ($validation['name']) {
                        case 'sa_id_number':
                            $validations[] = new SouthAfricanIdNumber;
                            break;
                        case 'sa_phone_number':
                            $validations[] = new SouthAfricanPhoneNumber;
                            break;
                        default:
                            $validations[] = $validation['name'];
                            break;
                    }
                }
                // Build the validation rules
                $validationArray[$field['field']] = $validations;
                // Reconstruct the request
                $this->merge([$field['field'] => $field['value']]);
            }
        }

        return $validationArray;
    }
}
