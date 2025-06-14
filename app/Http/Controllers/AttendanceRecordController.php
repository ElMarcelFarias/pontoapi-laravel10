<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AttendanceReport;
use App\Models\ClockDaily;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

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

    /**
     * Buscar todos os registros de presença de todos os usuários, 
     * com os horários da tabela `clock_dailies` via INNER JOIN.
     *
     * @return \Illuminate\Http\Response
     */
    public function generateAttendanceReport()
    {
        try {
            $user = Auth::user();
    
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
    
            $startDate = Carbon::createFromFormat('d/m/Y', now()->setTimezone('America/Sao_Paulo')->format('d/m/Y'))->format('Y-m-d');
            $endDate = $startDate;
    
            $attendanceRecords = AttendanceRecord::getAllRecordsWithClockTimes();
    
            if ($attendanceRecords->isEmpty()) {
                return response()->json([
                    'error' => 'No attendance records found',
                    'message' => 'There are no attendance records.',
                ], 404);
            }
    
            $pdf = Pdf::loadView('reports.attendance_report', [
                'attendanceRecords' => $attendanceRecords,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'user' => $user,
            ]);
    
            $fileName = 'attendance_report_' . $user->id . '_' . now()->timestamp . '.pdf';
            $exportPath = public_path('exports/' . $fileName);
    
            $pdf->save($exportPath);
    
            $attendanceReport = AttendanceReport::create([
                'admin_id' => $user->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'pdf_file' => 'exports/' . $fileName,
            ]);
    
            $downloadUrl = URL::temporarySignedRoute(
                'download.report',  
                now()->addMinutes(60),  
                ['fileName' => $fileName]
            );
    
            return response()->json([
                'data' => $attendanceReport,
                'download_url' => $downloadUrl,
                'message' => 'Attendance report generated and saved successfully.',
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
    
}
