<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => 'required|exists:invoices,id',
            'booking_id' => 'required|exists:bookings,id',
            'amount' => 'required|numeric|min:1',
            'bank_name' => 'required|string|max:100',
            'bank_account' => 'required|string|max:50',
            'bank_holder' => 'required|string|max:255',
            'payment_date' => 'required|date',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ];
    }
}
