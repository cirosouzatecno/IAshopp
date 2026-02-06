<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $fillable = [
        'customer_id',
        'direction',
        'message_id',
        'payload_json',
        'status',
    ];

    protected $casts = [
        'payload_json' => 'array',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
