<?php

namespace App\Http\Requests;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class DeductInventoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'deductions' => ['required', 'array', 'min:1'],
            'deductions.*.id' => ['required', 'exists:items,id'],
            'deductions.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'deductions.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $deductions = $this->input('deductions', []);
            
            foreach ($deductions as $index => $deduction) {
                $item = Item::find($deduction['id'] ?? null);
                
                if ($item && $item->current_quantity < $deduction['quantity']) {
                    $validator->errors()->add(
                        "deductions.{$index}.quantity",
                        "Insufficient stock for {$item->name}. Available: {$item->current_quantity}, Requested: {$deduction['quantity']}"
                    );
                }
            }
        });
    }

    public function messages(): array
    {
        return [
            'deductions.required' => 'At least one item is required for deduction.',
            'deductions.*.id.required' => 'Item ID is required.',
            'deductions.*.id.exists' => 'Item not found.',
            'deductions.*.quantity.required' => 'Quantity is required.',
            'deductions.*.quantity.min' => 'Quantity must be greater than zero.',
        ];
    }
}