<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'phone',
        'name',
        'last_interaction_at',
    ];

    protected $casts = [
        'last_interaction_at' => 'datetime',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function conversation()
    {
        return $this->hasOne(Conversation::class);
    }

    public function whatsappMessages()
    {
        return $this->hasMany(WhatsappMessage::class);
    }
}
