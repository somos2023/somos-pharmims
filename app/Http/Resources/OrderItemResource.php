<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Nette\Utils\DateTime;

class OrderItemResource extends JsonResource
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
            'order_id' => $this->order_id,
            'product_id' => $this->product_id,
            'expiration_date' => $this->formatDate($this->expires_at),
            'price' => $this->price,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'created_at' => $this->formatDate($this->created_at),

            'barcode' => $this->barcode,
            'brand_name' => $this->brand_name,
            'generic_name' => $this->generic_name,
            'formulation' => $this->formulation,
            'packing' => $this->packing,
            'image' => $this->image_url ? URL::to($this->image_url) : URL::to('defaults/no-image.png')
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
