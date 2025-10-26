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
        Schema::create('restaurant_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uid')->unique()->index();
            $table->uuid('restaurant_uid');  
            $table->enum('type', ['fssai', 'pan', 'front_image', 'inside_image']);
            $table->string('file_path');
            $table->timestamps();

            $table->foreign('restaurant_uid')->references('uid')->on('restaurants')->onDelete('cascade');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('restaurant_documents');
    }
};
