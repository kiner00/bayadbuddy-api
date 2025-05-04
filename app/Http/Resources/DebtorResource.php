<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtorResource extends JsonResource
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
            ],

            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'interest_rate' => $this->interest_value,
            'notes' => $this->notes,
        ];
    }
}
