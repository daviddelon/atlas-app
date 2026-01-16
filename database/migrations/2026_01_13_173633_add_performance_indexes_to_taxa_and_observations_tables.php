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
        // Index pour la table taxa - optimisations pour les requêtes fréquentes
        Schema::table('taxa', function (Blueprint $table) {
            // Index composite pour kingdom + class + family (utilisé dans presque toutes les requêtes)
            $table->index(['kingdom', 'class', 'family'], 'taxa_kingdom_class_family_index');
            // Index pour scientific_name (utilisé pour filtrer les noms d'espèces)
            $table->index('scientific_name', 'taxa_scientific_name_index');
        });

        // Index pour la table observations - optimisations pour les jointures
        Schema::table('observations', function (Blueprint $table) {
            // Index pour code (commune) - filtrage principal
            $table->index('code', 'observations_code_index');
            // Index composite pour taxon_id + code - optimisation des jointures
            $table->index(['taxon_id', 'code'], 'observations_taxon_code_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Suppression des index dans l'ordre inverse
        Schema::table('observations', function (Blueprint $table) {
            $table->dropIndex('observations_taxon_code_index');
            $table->dropIndex('observations_code_index');
        });

        Schema::table('taxa', function (Blueprint $table) {
            $table->dropIndex('taxa_scientific_name_index');
            $table->dropIndex('taxa_kingdom_class_family_index');
        });
    }
};
