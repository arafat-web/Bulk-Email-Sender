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
        Schema::create('custom_contact_field_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained('email_contacts')->onDelete('cascade');
            $table->foreignId('field_id')->constrained('custom_contact_fields')->onDelete('cascade');
            $table->text('value')->nullable(); // Store the actual field value
            $table->timestamps();

            $table->unique(['contact_id', 'field_id']); // One value per field per contact
            $table->index('contact_id');
            $table->index('field_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_contact_field_values');
    }
};
