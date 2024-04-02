<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;

class ProductResource extends JsonResource
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
            'category_id' => $this->category_id,
            'category' => $this->category,
            'barcode' => $this->barcode,
            'brand_name' => $this->brand_name,
            'generic_name' => $this->generic_name,
            'dosage_form' => $this->formulation,
            'dosage' => $this->formulation,
            'unit_of_measure' => $this->packing,
            'unit' => $this->packing,
            'expires_at' => $this->expires_at,
            'expiration_date' => $this->formatDate($this->expires_at),
            'price' => $this->price,
            'selling_price' => $this->price,
            'stock' => $this->stock,
            'available_stocks' => $this->available_stocks,
            'description' => $this->description,
            'status' => $this->status,
            'image' => $this->image_url ? URL::to($this->image_url) : URL::to('defaults/no-image.png'),
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d')
        ];
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
