<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LeadStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'interested_package_id' => 'nullable|exists:packages,id',
            'source' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:2000',
        ];
    }
}
