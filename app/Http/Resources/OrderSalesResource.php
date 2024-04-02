<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;

class OrderSalesResource extends JsonResource
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
            'order_number' => $this->order_number,
            'total_quantity' => $this->total_quantity,
            'total_amount' => $this->grand_total,
            'ordered_by' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'note' => $this->note,
            'status' => ucfirst($this->status),
            'order_date' => $this->formatDate($this->created_at),
            // 'items' => OrderItemResource::collection($this->items)
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
