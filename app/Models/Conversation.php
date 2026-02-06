<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'customer_id',
        'state',
        'context_json',
        'last_message_at',
    ];

    protected $casts = [
        'context_json' => 'array',
        'last_message_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
