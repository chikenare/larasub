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
        Schema::create(config('larasub.tables.subscription_statuses.name'), function (Blueprint $table) {
            if (config('larasub.tables.subscription_statuses.uuid')) {
                $table->uuid('id')->primary();
            } else {
                $table->id();
            }

            $table->string('slug')->unique();
            $table->json('name');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('larasub.tables.subscription_statuses.name'));
    }
};
