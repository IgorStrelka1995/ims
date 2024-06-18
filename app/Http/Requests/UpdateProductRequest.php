<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
            'sku' => ['sometimes', 'required', 'unique:products', 'max:120'],
            'name' => ['sometimes', 'required', 'max:120'],
            'description' => ['sometimes', 'required'],
            'price' => ['sometimes', 'required', 'decimal:2'],
            'stock' => ['sometimes', 'required', 'integer'],
            'user_id' => ['sometimes', 'required', Rule::exists('users', 'id')]
        ];
    }
}
