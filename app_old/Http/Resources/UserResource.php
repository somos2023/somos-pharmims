<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;

class UserResource extends JsonResource
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
            'role_id' => $this->role_id,
            'role' => $this->role,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'address' => $this->address,
            'phone_number' => $this->phone_number,
            'status' => $this->status,
            'image_url' => $this->image_url ? URL::to($this->image_url) : URL::to("images/avatars/default-avatar.png"),
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d')
        ];
    }
}


// php artisan make:resource UserResource