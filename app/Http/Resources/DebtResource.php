<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtResource extends JsonResource
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
            'borrower' => [
                'id' => $this->borrower->id,
                'name' => $this->borrower->name,
                'mobile_number' => $this->borrower->mobile_number,
                'avatar' => 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($this->borrower->name),
            ],
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'created_at' => $this->created_at,
            'status' => $this->status,
            'interest_rate' => $this->interest_value,
            'interest_term' => $this->interest_term,
            'notes' => $this->notes,
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
