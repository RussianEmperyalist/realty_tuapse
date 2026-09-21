<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('properties', 'owner_phone')) {
            return;
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->string('owner_phone', 64)->nullable()->after('phone_override');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('properties', 'owner_phone')) {
            return;
        }

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('owner_phone');
        });
    }
};
