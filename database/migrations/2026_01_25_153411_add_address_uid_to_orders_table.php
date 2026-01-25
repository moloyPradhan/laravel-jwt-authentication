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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('address_uid')
                ->nullable() // ← FIXED
                ->after('user_uid');

            $table->foreign('address_uid')
                ->references('uid')
                ->on('addresses')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['address_uid']); // ← drop FK first
            $table->dropColumn('address_uid');
        });
    }
};
