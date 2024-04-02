<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;

class TransactionResource extends JsonResource
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
            'staff_id' => $this->staff_id,
            'operated_by' => $this->operated_by,
            'transaction_number' => $this->transaction_number,
            'total_quantity' => $this->total_quantity,
            'grand_total' => $this->grand_total,
            'created_at' => $this->formatDate($this->created_at),
            'items' => TransactionItemResource::collection($this->items)
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
