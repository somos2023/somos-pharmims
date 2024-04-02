<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id,
            'staff_id' => $this->staff_id,
            'order_number' => $this->order_number,
            'total_quantity' => $this->total_quantity,
            'grand_total' => $this->grand_total,
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'note' => $this->note,
            'status' => ucfirst($this->status),
            'created_at' => $this->formatDate($this->created_at),
            'items' => OrderDetailItemResource::collection($this->items)
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
