<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Organizer extends Model
{
    use HasFactory;
    protected $fillable = [
        'org_name',
        'org_email',
        'org_description',
        'event_id'
    ];

    protected $casts = [
        'event_id' => 'integer'
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }
}
