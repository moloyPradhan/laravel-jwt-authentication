<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use function Ramsey\Uuid\v1;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('uid', 8)->unique();
            $table->string('user_uid', 8);
            $table->decimal('amount', 10, 2);
            $table->enum('status', [
                'pending',
                'paid',
                'cancelled',
                'failed'
            ])->default('pending');

            $table->timestamps();

            $table->foreign('user_uid')
                ->references('uid')
                ->on('users')
                ->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
