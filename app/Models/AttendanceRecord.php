<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    // Definir os campos que podem ser preenchidos
    protected $fillable = [
        'user_id',
        'clock_daily_id',
        'date',
    ];

    /**
     * Relacionamento com a tabela ClockDaily.
     */
    public function clockDaily()
    {
        return $this->belongsTo(ClockDaily::class);
    }

    /**
     * Relacionamento com a tabela User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Cria um novo registro de presença.
     *
     * @param int $userId
     * @param int $clockDailyId
     * @param string $date
     * @return AttendanceRecord
     */
    public static function createAttendanceRecord($userId, $clockDailyId, $date)
    {
        return self::create([
            'user_id' => $userId,
            'clock_daily_id' => $clockDailyId,
            'date' => $date,
        ]);
    }

    /**
     * Consulta os registros de presença do usuário no dia atual,
     * com os horários da tabela clock_dailies via INNER JOIN.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getTodayRecordsWithClockTimes($userId)
    {
        return self::select(
                'attendance_records.*',
                'clock_dailies.morning_clock_in',
                'clock_dailies.morning_clock_out',
                'clock_dailies.afternoon_clock_in',
                'clock_dailies.afternoon_clock_out'
            )
            ->join('clock_dailies', 'attendance_records.clock_daily_id', '=', 'clock_dailies.id')
            ->where('attendance_records.user_id', $userId)
            ->whereDate('attendance_records.date', now()->toDateString())
            ->get();
    }

    /**
     * Consulta de todos os registros de presença,
     * com os horários da tabela clock_dailies via INNER JOIN.
     *
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getAllRecordsWithClockTimes()
    {
        return self::select(
                'attendance_records.*',
                'clock_dailies.morning_clock_in',
                'clock_dailies.morning_clock_out',
                'clock_dailies.afternoon_clock_in',
                'clock_dailies.afternoon_clock_out'
            )
            ->join('clock_dailies', 'attendance_records.clock_daily_id', '=', 'clock_dailies.id')
            ->whereDate('attendance_records.date', now()->toDateString())
            ->get();
    }
}


