<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Illuminate\Support\Facades\URL;

class UserListResource extends JsonResource
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
            'role_id' => $this->role_id,
            'user_role' => $this->role_id,
            'role' => ucfirst($this->role),
            'role_small' => $this->role,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'full_name' => $this->first_name.' '.$this->middle_name.' '.$this->last_name,
            'email' => $this->email,
            'address' => $this->address?? '',
            'phone_number' => (string)$this->phone_number ?? '',
            'status' => $this->status,
            'image' => $this->image_url ? URL::to($this->image_url) : URL::to('defaults/def-avatar.png'),
            'created_at' => (new DateTime($this->created_at))->format('Y-m-d')
        ];
    }
}
