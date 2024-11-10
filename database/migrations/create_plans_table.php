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
        Schema::create(config('larasub.tables.plans.name'), function (Blueprint $table) {
            if (config('larasub.tables.plans.uuid')) {
                $table->uuid('id')->primary();
            } else {
                $table->id();
            }

            $table->string('slug')->unique();
            $table->json('name');
            $table->json('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('price')->default('0.0');
            $table->json('currency');
            $table->unsignedSmallInteger('reset_period')->nullable();
            $table->string('reset_period_type')->nullable();
            $table->unsignedMediumInteger('sort_order')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('larasub.tables.plans.name'));
    }
};
