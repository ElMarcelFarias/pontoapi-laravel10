<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkSchedule extends Model
{
    use HasFactory;

    // Defina a tabela, caso seja diferente da convenção do Laravel
    protected $table = 'work_schedules';

    // Defina os campos que podem ser preenchidos
    protected $fillable = [
        'user_id',
        'schedule_type',
        'morning_clock_in',
        'morning_clock_out',
        'afternoon_clock_in',
        'afternoon_clock_out',
    ];

    // Relacionamento com o modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
