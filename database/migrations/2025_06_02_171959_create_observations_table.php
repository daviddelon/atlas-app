<?php

use App\Models\Taxon;
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
        Schema::create('observations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Taxon::class)->constrained();
            $table->date('observed_on');
            $table->integer('observed_by');
            $table->string('license');
            $table->decimal('latitude', 15, 10);
            $table->decimal('longitude', 15, 10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('observations');
    }
};
