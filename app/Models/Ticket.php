<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'ticket_type',
        'qty',
        'available_qty',
        'sold_qty',
        'price',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
