<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\ClockDaily;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordController extends Controller
{
    /**
     * Registrar o registro de presença após a batida de ponto.
     *
     * @param int $clockDailyId
     * @param string $date
     * @return \Illuminate\Http\Response
     */
    public function createAttendanceRecord($clockDailyId, $date)
    {
        $user = Auth::user();

        // Cria o registro de presença
        $attendanceRecord = AttendanceRecord::createAttendanceRecord($user->id, $clockDailyId, $date);

        return response()->json([
            'data' => $attendanceRecord,
            'message' => 'Attendance record created successfully.',
        ], 200);
    }

    /**
     * Buscar todos os registros de presença para o usuário no dia atual,
     * com os horários da tabela `clock_dailies` via INNER JOIN.
     *
     * @return \Illuminate\Http\Response
     */
    public function getTodayRecords()
    {
        try {
            $user = Auth::user();

            $attendanceRecords = AttendanceRecord::getTodayRecordsWithClockTimes($user->id);

            if ($attendanceRecords->isEmpty()) {
                return response()->json([
                    'error' => 'No attendance records found',
                    'message' => 'There are no attendance records for today.',
                ], 404);
            }

            return response()->json([
                'data' => $attendanceRecords,
                'message' => 'Attendance records retrieved successfully.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
