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
        Schema::create(config('larasub.tables.plan_features.name'), function (Blueprint $table) {
            (
                config('larasub.tables.plans.uuid')
                ? $table->foreignUuid('plan_id')
                : $table->foreignId('plan_id')
            )->constrained(config('larasub.tables.plans.name'))->cascadeOnDelete();

            (
                config('larasub.tables.features.uuid')
                ? $table->foreignUuid('feature_id')
                : $table->foreignId('feature_id')
            )->constrained(config('larasub.tables.features.name'))->cascadeOnDelete();

            $table->string('value')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->timestamps();

            $table->primary(['plan_id', 'feature_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('larasub.tables.plan_features.name'));
    }
};
