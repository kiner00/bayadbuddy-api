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
            'borrower_id' => $this->borrower_id,
            'amount' => $this->amount,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'interest_type' => $this->interest_type,
            'interest_value' => $this->interest_value,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
