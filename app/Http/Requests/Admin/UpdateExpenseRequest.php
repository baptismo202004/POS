<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // The authorization logic will be handled by a policy later.
        // For now, we'll just prevent editing of purchase-linked expenses.
        $expense = $this->route('expense');
        return $expense && is_null($expense->purchase_id);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'expense_category_id' => ['required', 'exists:expense_categories,id', function ($attribute, $value, $fail) {
                $category = \App\Models\ExpenseCategory::find($value);
                if ($category && $category->name === 'Purchases') {
                    $fail('The selected category is invalid for manual expense entry.');
                }
            }],
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'reference_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'purchase_id' => 'prohibited',
        ];
    }
}
