<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceReport extends Model
{
    use HasFactory;

    // Adiciona os campos que podem ser preenchidos em massa
    protected $fillable = [
        'admin_id',   // Permite que o admin_id seja atribuído em massa
        'start_date',
        'end_date',
        'pdf_file',
    ];
}

