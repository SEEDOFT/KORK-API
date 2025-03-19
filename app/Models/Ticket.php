<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type',
        'qty',
        'available_qty',
        'sold_qty',
        'price'
    ];

    protected $casts = [
        'event_id' => 'integer',
        'qty' => 'integer',
        'available_qty' => 'integer',
        'sold_qty' => 'integer',
        'price' => 'decimal:2'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function buyTickets(): HasMany
    {
        return $this->hasMany(BuyTicket::class);
    }
}
