<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'expense_category_id' => ['required', function ($attribute, $value, $fail) {
                // Allow "new:Name" for auto-creation, otherwise must exist
                if (str_starts_with((string) $value, 'new:')) {
                    $name = trim(substr($value, 4));
                    if ($name === '' || strtolower($name) === 'purchases') {
                        $fail('The category name is invalid.');
                    }
                } elseif (! \App\Models\ExpenseCategory::where('id', $value)->exists()) {
                    $fail('The selected category is invalid.');
                } else {
                    $category = \App\Models\ExpenseCategory::find($value);
                    if ($category && $category->name === 'Purchases') {
                        $fail('The selected category is invalid for manual expense entry.');
                    }
                }
            }],
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'supplier_id' => 'nullable|string|max:255',
            'supplier_name' => 'prohibited',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'purchase_id' => 'prohibited',
        ];
    }
}
