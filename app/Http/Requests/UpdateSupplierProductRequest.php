<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSupplierProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $roleId = $this->user()->role_id;

        return [
            'id' => 'exists:products,id',
            'role_id' => 'exists:roles,id',
            'user_id' => 'exists:users,id',
            'category_id' => 'exists:categories,id',
            'barcode' => [
                'required',
                'string',
                Rule::unique('products', 'barcode')->where(function ($query) use ($roleId) {
                    return $query->where('role_id', '=', 3);
                })->ignore($this->input('id')), // Adjust 'id' as needed for update scenarios
            ],
            'brand_name' => 'required|string',
            'generic_name' => 'nullable|string',
            'dosage' => 'required|string',
            'unit' => 'required|string',
            'expiration_date' => 'required|date',
            'price' => 'required|numeric',
            'stock' => 'required|numeric',
            'old_stock' => 'nullable|numeric',
            'stock_added' => 'nullable|numeric',
            'description' => 'nullable|string',
            'image_url' => 'nullable|string'
        ];
    }
}
