<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // adjust if using policies
    }

    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:product_categories,id',

            'name' => 'required|string|max:255',

            'price' => 'required|numeric|min:0',

            'stock' => 'required|integer|min:0',

            'description' => 'required|string',

            'status' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category.',
            'category_id.exists'   => 'Selected category is invalid.',

            'name.required' => 'Product name is required.',

            'price.required' => 'Price is required.',
            'price.numeric'  => 'Price must be a number.',

            'stock.required' => 'Stock is required.',
            'stock.integer'  => 'Stock must be an integer.',

            'status.required' => 'Please select status.',
        ];
    }

}
