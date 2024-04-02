<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'barcode',
        'quantity',
        'purchase_price',
        'expires_at',
        'status',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function order()
    {
        return $this->hasOne(Order::class);
    }
}
