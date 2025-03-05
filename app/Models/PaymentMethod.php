<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'card_number',
        'card_holder_name',
        'expired_date',
        'card_hash'
    ];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
