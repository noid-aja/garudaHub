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
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignid('project_id')
                ->constrained('projects')
                ->cascadeondelete();

            $table->foreignid('developer_id')
                ->constrained('users')
                ->cascadeondelete();

            $table->bigInteger('harga');
            $table->unsignedInteger('durasi');
            $table->text('proposal');
            $table->boolean('is_winner')
                ->default('0');
            $table->timestamps();

            $table->unique('project_id', 'developer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
