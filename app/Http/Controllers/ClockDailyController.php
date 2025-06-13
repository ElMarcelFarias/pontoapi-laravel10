<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClockDaily;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClockDailyController extends Controller
{
    /**
     * Registrar a batida de ponto diário para o usuário.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function registerClockIn()
    {
        try {

            $user = Auth::user(); 

            $workSchedule = WorkSchedule::where('user_id', $user->id)->first();
            if (!$workSchedule) {
                return response()->json([
                    'error' => 'Work schedule not found',
                    'message' => 'User work schedule not configured.',
                ], 400);
            }

            $tolerance = $workSchedule->interval ?? 15;
            $currentTime = now()->setTimezone('America/Sao_Paulo')->format('H:i:s');

            $clockFields = [
                'morning_clock_in',
                'morning_clock_out',
                'afternoon_clock_in',
                'afternoon_clock_out',
            ];

            $clockDaily = ClockDaily::registerClockIn($user->id, $clockFields, $workSchedule, $tolerance, $currentTime);

            if (!$clockDaily[1]) {
                return response()->json([
                    'error' => 'Clock-in not completed',
                    'message' => 'The user has not yet clocked in for today.',
                ], 400); 
            }

            return response()->json([
                'data' => $clockDaily[0],
                'message' => 'Clock record successfully saved/updated',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
