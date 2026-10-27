<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => 'required|string|max:100',
            'title' => 'nullable|string|max:20',
            'gender' => 'nullable|in:male,female',
            'birth_place' => 'nullable|string|max:50',
            'birth_date' => 'nullable|date',
            'marital_status' => 'nullable|in:single,married,widowed,divorced',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'nik' => 'required|string|max:16|min:16',
            'passport_number' => 'nullable|string|max:20',
            'room_type' => 'required|in:single,double,triple,quad',
            'payment_method' => 'required|in:gateway,transfer,office',
            'payment_provider_id' => 'required_if:payment_method,gateway|exists:payment_providers,id',
            'payment_channel' => 'required_if:payment_method,gateway|string|max:30',
        ];
    }
}
