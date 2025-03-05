<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organizer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'description'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
