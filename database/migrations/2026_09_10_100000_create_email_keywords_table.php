<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Stores admin-defined dynamic keywords (placeholders) like [company_name], [name], etc.
     * Each keyword maps to a contact field (for per-recipient personalization)
     * or to a fixed default value.
     */
    public function up(): void
    {
        Schema::create('email_keywords', function (Blueprint $table) {
            $table->id();
            // Key without brackets, e.g. "company_name" renders as [company_name]
            $table->string('key', 50)->unique();
            $table->string('label')->nullable();
            // Contact field used for per-recipient value: email|first_name|last_name|full_name|phone|company|notes|null(fixed value only)
            $table->string('source_column', 30)->nullable();
            $table->text('default_value')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_keywords');
    }
};
