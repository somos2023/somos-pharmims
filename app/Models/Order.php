<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'supplier_id',
        'order_number',
        'total_quantity',
        'grand_total',
        'name',
        'phone',
        'address',
        'note',
        'status',
        'deleted_flag'
    ];

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function staff()
    {
        return $this->belongsTo(User::class);
    }

    public function supplier()
    {
        return $this->belongsTo(User::class);
    }
}
