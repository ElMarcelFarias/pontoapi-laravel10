<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clock_dailies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relacionamento com a tabela users
            $table->time('morning_clock_in')->nullable(); // Entrada manhã
            $table->time('morning_clock_out')->nullable(); // Saída manhã
            $table->time('afternoon_clock_in')->nullable(); // Entrada tarde
            $table->time('afternoon_clock_out')->nullable(); // Saída tarde
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clock_dailies');
    }
};
