<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffProduct extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'staff_id',
        'category_id',
        'barcode',
        'brand_name',
        'generic_name',
        'formulation',
        'packing',
        'expires_at',
        'price',
        'stock',
        'description',
        'status',
        'image_url',
        'delete_flag',
    ];
}
