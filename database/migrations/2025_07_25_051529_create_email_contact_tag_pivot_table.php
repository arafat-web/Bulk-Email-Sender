<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('email_contact_tag_pivot', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_contact_id')->constrained()->onDelete('cascade');
            $table->foreignId('contact_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['email_contact_id', 'contact_tag_id']);
            $table->index('email_contact_id');
            $table->index('contact_tag_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_contact_tag_pivot');
    }
};
