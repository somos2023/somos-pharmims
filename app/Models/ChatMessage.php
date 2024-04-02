<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id',
        'unique_id',
        'message',
        'deleted_flag',
    ];

    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function unique_ids()
    {
        return $this->hasMany(Chat::class, "unique_id");
    }
}
