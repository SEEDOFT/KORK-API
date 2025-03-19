<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_type',
        'event_name',
        'description',
        'location',
        'poster_url',
        'start_time',
        'end_time'
    ];

    public function organizer(): HasOne
    {
        return $this->HasOne(Organizer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function bookmark(): HasOne
    {
        return $this->hasOne(Bookmark::class);
    }

    public function buyTicket(): HasOne
    {
        return $this->hasOne(BuyTicket::class);
    }
}
