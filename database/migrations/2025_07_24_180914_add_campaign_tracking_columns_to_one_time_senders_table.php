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
        Schema::table('one_time_senders', function (Blueprint $table) {
            $table->string('subject', 500)->nullable()->after('total_email_address');
            $table->enum('status', ['processing', 'queued', 'completed', 'failed'])->default('processing')->after('subject');
            $table->integer('sent_count')->default(0)->after('status');
            $table->integer('failed_count')->default(0)->after('sent_count');
            $table->timestamp('started_at')->nullable()->after('failed_count');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('one_time_senders', function (Blueprint $table) {
            $table->dropColumn(['subject', 'status', 'sent_count', 'failed_count', 'started_at', 'completed_at']);
        });
    }
};
