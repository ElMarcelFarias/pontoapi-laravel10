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
        Schema::create('attendance_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade'); // Relacionamento com admin (usuário)
            $table->date('start_date'); // Data de início do relatório
            $table->date('end_date'); // Data de término do relatório
            $table->string('pdf_file'); // Caminho para o arquivo PDF gerado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('attendance_reports');
    }
};
