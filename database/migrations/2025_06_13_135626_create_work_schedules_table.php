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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relacionamento com usuários
            $table->string('schedule_type')->default('regular'); // Tipo de jornada, por exemplo, 'regular', 'flexível', etc.
            $table->time('morning_clock_in')->default('08:00'); // Entrada manhã
            $table->time('morning_clock_out')->default('12:00'); // Saída manhã
            $table->time('afternoon_clock_in')->default('14:00'); // Entrada tarde
            $table->time('afternoon_clock_out')->default('18:00'); // Saída tarde
            $table->integer('interval')->default(15); // Intervalo em minutos
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_schedules');
    }
};
