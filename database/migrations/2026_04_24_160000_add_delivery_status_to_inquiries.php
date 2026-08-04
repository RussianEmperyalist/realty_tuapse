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
        foreach (['contact_requests', 'callback_requests', 'booking_requests', 'property_messages'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->string('delivery_status', 32)->default('pending')->after('recipient_email');
                $table->text('delivery_error')->nullable()->after('delivery_status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['contact_requests', 'callback_requests', 'booking_requests', 'property_messages'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn(['delivery_status', 'delivery_error']);
            });
        }
    }
};
