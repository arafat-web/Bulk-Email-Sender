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
        Schema::create('custom_contact_fields', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Field name like 'discount_code', 'customer_tier'
            $table->string('label'); // Human readable label like 'Discount Code', 'Customer Tier'
            $table->enum('type', ['text', 'number', 'email', 'url', 'textarea', 'select', 'date'])->default('text');
            $table->text('description')->nullable(); // Description for the field
            $table->json('options')->nullable(); // For select type fields, store options as JSON
            $table->string('default_value')->nullable(); // Default value for the field
            $table->boolean('is_required')->default(false); // Whether field is required
            $table->boolean('is_active')->default(true); // Whether field is active
            $table->integer('sort_order')->default(0); // Order for displaying fields
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Owner of the custom field
            $table->timestamps();

            $table->unique(['name', 'user_id']); // Unique field name per user
            $table->index(['user_id', 'is_active']);
            $table->index('sort_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_contact_fields');
    }
};
