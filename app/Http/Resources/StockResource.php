<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;

class StockResource extends JsonResource
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
            'barcode' => $this->barcode,
            'brand_name' => $this->brand_name,
            'formulation' => $this->formulation,
            'dosage_form' => $this->formulation,
            'packing' => $this->packing,
            'unit_of_measure' => $this->packing,
            'image' => $this->image_url ? URL::to($this->image_url) : URL::to('defaults/no-image.png'),
            'expiration_date' => $this->formatDate($this->expires_at),
            'quantity' => (string)$this->quantity,
            'purchase_price' => (string)$this->purchase_price,
            'status' => $this->status == 'sold out' ? 'out of stock' :  $this->status,
            'created_at' =>  $this->formatDate($this->created_at),
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
