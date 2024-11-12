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
        Schema::create(config('larasub.tables.subscription_feature_usage.name'), function (Blueprint $table) {
            if (config('larasub.tables.subscription_feature_usage.uuid')) {
                $table->uuid('id')->primary();
            } else {
                $table->id();
            }

            (
                config('larasub.tables.subscriptions.uuid')
                ? $table->foreignUuid('subscription_id')
                : $table->foreignId('subscription_id')
            )->constrained(config('larasub.tables.subscriptions.name'))->cascadeOnDelete();

            (
                config('larasub.tables.features.uuid')
                ? $table->foreignUuid('feature_id')
                : $table->foreignId('feature_id')
            )->constrained(config('larasub.tables.features.name'))->cascadeOnDelete();

            $table->string('value');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('larasub.tables.subscription_feature_usage.name'));
    }
};
