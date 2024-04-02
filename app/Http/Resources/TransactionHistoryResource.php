<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionHistoryResource extends JsonResource
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
            'user_id' => $this->user_id,
            'total_quantity' => $this->total_quantity,
            'net_total' => $this->net_total,
            'grand_total' => $this->grand_total,
            'discount_type' => $this->discount_type,
            'discount' => $this->discount,
            'discount_amount' => $this->discount_amount,
            'additional_information' => $this->additional_information,
            'items' => TransactionItemResource::collection($this->items)
        ];
    }
}
