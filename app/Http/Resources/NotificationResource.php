<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use Carbon\Carbon;
use App\Models\Product;
use App\Models\Notification;

class NotificationResource extends JsonResource
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
            'type' => $this->type,
            'message' => $this->message,
            'stock'=> $this->stock,
            'status'=> $this->status,
            'product'=> new NotificationProductResource($this->getProduct($this->stock->barcode)),
            'date_time' => $this->getTime($this->created_at)
        ];
            
    }

    private function getProduct($barcode) {
        $product = Product::where('barcode', $barcode)->first();
        return $product;
    }

    private function getTime($created_at) {

        $messageTimestamp = strtotime($created_at);
        $timeDifference = time() - $messageTimestamp;

        $recent_time = '';
        if ($timeDifference < 60) {
            $recent_time = "Just now";
        } else if ($timeDifference < 3600) {
            $minutes = floor($timeDifference / 60);
            $recent_time = "$minutes min ago";
        } else if ($timeDifference < 86400) {
            $hours = floor($timeDifference / 3600);
            $recent_time = "$hours hr ago";
        } else {
            $days = floor($timeDifference / 86400);
            $recent_time = "$days day(s) ago";
        }


        return $recent_time;
    }
}
