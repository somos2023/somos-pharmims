<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class SystemSettingResouce extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'title' => $this->title,
            'logo' => $this->logo_url ? URL::to($this->logo_url) : URL::to("default/default-logo.jpg"),
            'logo_lg' => $this->logo_url ? URL::to($this->logo_lg_url) : URL::to("default/default-logo-lg.jpg"),
            'cover' => $this->cover_url ? URL::to($this->cover_url) :  URL::to("default/default-cover.jpg"),
            'currency' => $this->currency,
        ];
    }
}
