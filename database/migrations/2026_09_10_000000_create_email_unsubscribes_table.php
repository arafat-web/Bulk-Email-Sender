<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Global suppression list so unsubscribe works for contacts
     * AND one-off campaign recipients (TempMailAddress) too.
     */
    public function up(): void
    {
        Schema::create('email_unsubscribes', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('reason')->nullable();
            $table->string('source')->default('footer_link'); // footer_link | one_click | manual
            $table->timestamp('unsubscribed_at')->useCurrent();
            $table->timestamps();

            $table->index('email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_unsubscribes');
    }
};
