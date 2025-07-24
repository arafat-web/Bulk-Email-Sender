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
        Schema::create('email_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Account name/label
            $table->string('email')->unique(); // Email address
            $table->string('smtp_host'); // SMTP host
            $table->integer('smtp_port'); // SMTP port
            $table->string('smtp_username'); // SMTP username
            $table->string('smtp_password'); // SMTP password (encrypted)
            $table->enum('smtp_encryption', ['tls', 'ssl', 'none'])->default('tls'); // Encryption type
            $table->string('from_name'); // Display name for "From" field
            $table->boolean('is_default')->default(false); // Is this the default account
            $table->boolean('is_active')->default(true); // Is this account active
            $table->timestamp('last_used_at')->nullable(); // When was this account last used
            $table->integer('emails_sent')->default(0); // Count of emails sent
            $table->text('notes')->nullable(); // Optional notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_accounts');
    }
};
