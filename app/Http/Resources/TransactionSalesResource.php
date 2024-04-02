<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Nette\Utils\DateTime;
use App\Models\User;

class TransactionSalesResource extends JsonResource
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
            // 'staff_id' => $this->staff_id,
            'operated_by' => $this->getUser($this->staff_id),
            'transaction_number' => $this->transaction_number,
            'total_quantity' => $this->total_quantity,
            'total_amount' => $this->grand_total,
            'transaction_date' => $this->formatDate($this->created_at),
            // 'items' => TransactionItemResource::collection($this->items)
        ];
    }

    private function getUser($id){
        $user = User::where('id', $id)->first();

        return $user->first_name.' '.$user->last_name;
    }

    private function formatDate($date)
    {
        $dateTime = DateTime::from($date);
        return $dateTime->format('d M Y');
    }
}
