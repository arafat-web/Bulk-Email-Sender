<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add realtime-tracker columns to campaigns table.
     */
    public function up(): void
    {
        Schema::table('one_time_senders', function (Blueprint $table) {
            if (! Schema::hasColumn('one_time_senders', 'skipped_count')) {
                $table->integer('skipped_count')->default(0)->after('failed_count');
            }
            if (! Schema::hasColumn('one_time_senders', 'last_error')) {
                $table->text('last_error')->nullable()->after('completed_at');
            }
            if (! Schema::hasColumn('one_time_senders', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('one_time_senders', 'type')) {
                $table->string('type', 20)->default('instant')->after('user_id');
            }
            if (! Schema::hasColumn('one_time_senders', 'body')) {
                $table->longText('body')->nullable()->after('subject');
            }
        });
    }

    public function down(): void
    {
        Schema::table('one_time_senders', function (Blueprint $table) {
            $columns = [];
            foreach (['skipped_count', 'last_error', 'type', 'body'] as $col) {
                if (Schema::hasColumn('one_time_senders', $col)) {
                    $columns[] = $col;
                }
            }
            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
            if (Schema::hasColumn('one_time_senders', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {
                    // foreign key may not exist on all drivers
                }
                $table->dropColumn('user_id');
            }
        });
    }
};
