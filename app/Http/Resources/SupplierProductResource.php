<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;

class SupplierProductResource extends JsonResource
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
            'supplier_id' => $this->user_id,
            'supplier_name' => $this->supplier_fname.' '.$this->supplier_lname,
            'supplier_email' => $this->supplier_email,
            'supplier_phone_number' => $this->supplier_phone_number,
            'supplier_image' => $this->supplier_image ? URL::to($this->supplier_image) : URL::to('defaults/def-avatar.png'),
            'category_id' => $this->category_id,
            'category' => $this->category,
            'barcode' => $this->barcode,
            'brand_name' => $this->brand_name,
            'generic_name' => $this->generic_name,
            'formulation' => $this->formulation,
            'packing' => $this->packing,
            'expires_at' => $this->expires_at,
            'expiration_date' => $this->formatDate($this->expires_at),
            'price' => $this->price,
            'stock' => $this->stock,
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
