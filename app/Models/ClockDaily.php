<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClockDaily extends Model
{
    use HasFactory;

    // Define a tabela associada à model
    protected $table = 'clock_dailies';

    // Defina os campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'morning_clock_in',
        'morning_clock_out',
        'afternoon_clock_in',
        'afternoon_clock_out',
    ];

    // Relacionamento com a model User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Verifica se o horário atual está dentro do intervalo de tolerância do horário de ponto.
     *
     * @param string $currentTime Hora atual
     * @param string $targetTime Hora de ponto alvo
     * @param int $tolerance Intervalo de tolerância em minutos
     * @return bool
     */
    public static function isWithinTolerance($currentTime, $targetTime, $tolerance)
    {
        if (strlen($targetTime) == 5) {
            $targetTime .= ':00';
        }
        

        $currentTime = Carbon::createFromFormat('H:i:s', $currentTime, 'America/Sao_Paulo');
        $targetTime = Carbon::createFromFormat('H:i:s', $targetTime, 'America/Sao_Paulo');

        $toleranceInSeconds = $tolerance * 60;
        
        $lowerLimit = $targetTime->copy()->subSeconds($toleranceInSeconds);
        $upperLimit = $targetTime->copy()->addSeconds($toleranceInSeconds);


        return $currentTime->between($lowerLimit, $upperLimit);
    }

    /**
     * Registra ou atualiza a batida de ponto do usuário.
     *
     * @param int $userId
     * @param array $clockFields
     * @param int $tolerance
     * @param string $currentTime
     * @return mixed
     */
    public static function registerClockIn($userId, $clockFields, $workSchedule, $tolerance, $currentTime)
    {
        $hasClockedIn = true;
        $clockDaily = self::where('user_id', $userId)
            ->whereDate('created_at', now()->toDateString())
            ->first();
        

        foreach ($clockFields as $field) {
            if (self::isWithinTolerance($currentTime, $workSchedule->$field, $tolerance)) {
                if ($clockDaily) {
                    if (is_null($clockDaily->$field)) {
                        $clockDaily->update([$field => $currentTime]);
                    } else {
                        $hasClockedIn = false;
                    }

                } else {
                    $clockDaily = self::create([
                        'user_id' => $userId,
                        $field => $currentTime,
                    ]);

                    AttendanceRecord::createAttendanceRecord($userId, $clockDaily->id, now()->toDateString());
                }
                return [$clockDaily, $hasClockedIn];
            }
        }

        return null; 
    }
}
