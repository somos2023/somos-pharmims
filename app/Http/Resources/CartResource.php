<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;
use Nette\Utils\DateTime;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->cart_id,
            'user_id' => $this->user_id,
            'user_name' => $this->first_name.' '.$this->last_name,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,

            'barcode' => $this->barcode,
            'brand_name' => $this->brand_name,
            'generic_name' => $this->generic_name,
            'formulation' => $this->formulation,
            'packing' => $this->packing,
            'price' => $this->price,
            'stock' => $this->stock,
            'expires_at' => $this->expires_at,
            'expiration_date' => $this->formatDate($this->expires_at),
            'product_image' => $this->product_image ? URL::to($this->product_image) : URL::to('defaults/no-image.png')
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
