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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignid('client_id')
                ->constrained('users')
                ->cascadeondelete();

            $table->string('judul', 100);
            $table->text('deskripsi',);
            $table->string('kategori', 50);
            $table->bigInteger('budget_min')
                    ->default(0);
            $table->bigInteger('budget_max')
                    ->default(0);
            $table->enum('status', ['terbuka','ditutup'])
                    ->default('terbuka');
            $table->date('batas_lelang');
            $table->timestamps();
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
