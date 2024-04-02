<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'role_id' => $this->user()->role_id,
            'user_id' => $this->user()->id
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules()
    {
        $roleId = $this->user()->role_id;

        return [
            'role_id' => 'exists:roles,id',
            'user_id' => 'exists:users,id',
            'category_id' => 'exists:categories,id',
            'barcode' => [
                'required',
                'string',
                Rule::unique('products', 'barcode')->where(function ($query) use ($roleId) {
                    return $query->where('role_id', '!=', 3);
                })->ignore($this->input('id')), // Adjust 'id' as needed for update scenarios
            ],
            'brand_name' => 'required|string',
            'generic_name' => 'nullable|string',
            'dosage' => 'required|string',
            'unit' => 'required|string',
            'expiration_date' => 'required|date',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string'
        ];
    }
}
