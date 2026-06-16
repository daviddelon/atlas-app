<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('DROP TABLE IF EXISTS communes');
        Schema::create('communes', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';

            $table->id('OGR_FID');
            $table->geometry('geom');
            $table->string('code', 10)->nullable();
            $table->text('nom')->nullable();
            $table->text('departement')->nullable();
            $table->text('region')->nullable();
            $table->text('commune')->nullable();
            $table->integer('plm')->nullable();
            $table->text('epci')->nullable();
            $table->unique(['OGR_FID']);
            $table->spatialIndex(['geom'], 'geom');
            $table->spatialIndex(['geom'], 'idx_communes_geom');
        });

        // Post-processing: drop/recreate column as NOT NULL
        DB::statement('DROP INDEX geom ON communes');
        DB::statement('DROP INDEX idx_communes_geom ON communes');
        DB::statement('ALTER TABLE communes DROP COLUMN geom');
        DB::statement('ALTER TABLE communes ADD COLUMN geom GEOMETRY NOT NULL');
        DB::statement('CREATE SPATIAL INDEX geom ON communes (geom)');
        DB::statement('CREATE SPATIAL INDEX idx_communes_geom ON communes (geom)');


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communes');
    }
};
