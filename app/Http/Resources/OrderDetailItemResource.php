<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Nette\Utils\DateTime;

class OrderDetailItemResource extends JsonResource
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

            'barcode' => $this->product->barcode,
            'brand_name' => $this->product->brand_name,
            'generic_name' => $this->product->generic_name,
            'formulation' => $this->product->formulation,
            'packing' => $this->product->packing,
            'image' => $this->product->image_url ? URL::to($this->product->image_url) : URL::to('defaults/no-image.png')

            // 'product' => new ProductResource($this->product)
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
