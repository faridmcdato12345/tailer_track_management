<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMotelRequest extends FormRequest
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
        return [
            'yard_name' => 'required|unique:motels,motel_name',
            'yard_address' => 'required',
            'status' => 'nullable'
        ];
    }

    public function messages()
    {
        return [
            'yard_name.required' => 'Motel name is required.',
            'yard_name.unique' => 'Motel name has been registered.',
            'yard_address.required' => 'Motel address is required',
        ];
    }
}
