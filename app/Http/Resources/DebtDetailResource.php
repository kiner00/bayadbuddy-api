<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'interest_type' => $this->interest_type,
            'interest_value' => (float) $this->interest_value,
            'interest_term' => $this->interest_term,
            'notes' => $this->notes,
            'borrower' => [
                'id' => $this->borrower->id,
                'name' => $this->borrower->name,
                'mobile_number' => $this->borrower->mobile_number,
            ],
            'payments' => $this->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'date' => $payment->created_at->toDateString(),
                    'amount' => (float) $payment->amount,
                    'method' => $payment->method ?? 'Cash',
                ];
            }),
        ];
    }
}
