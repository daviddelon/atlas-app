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
        Schema::create('taxa', function (Blueprint $table) {
            $table->id();
            $table->string('scientific_name');
            $table->string('common_name');
            $table->string('kingdom');
            $table->string('phylum')->nullable();
            $table->string('subphylum')->nullable();
            $table->string('class')->nullable();
            $table->string('subclass')->nullable();
            $table->string('order')->nullable();
            $table->string('suborder')->nullable();
            $table->string('family')->nullable();
            $table->string('subfamily')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxa');
    }
};
