<?php

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('buy_tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(Event::class)->constrained();
            $table->foreignIdFor(Ticket::class)->constrained();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();

            $table->string('ticket_code')->unique();
            $table->decimal('price', 10, 2);
            $table->boolean('payment_status')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_tickets');
    }
};
