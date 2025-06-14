<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AttendanceReportController extends Controller
{
    // ... outros métodos

    /**
     * Permitir o download do relatório gerado.
     * 
     * @param string $fileName
     * @return \Illuminate\Http\Response
     */
    public function downloadReport($fileName)
    {
        try {
            $filePath = public_path('exports/' . $fileName);

            if (!file_exists($filePath)) {
                return response()->json(['error' => 'File not found'], 404);
            }

            return response()->download($filePath);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Internal Server Error',
                'message' => $e->getMessage(),
            ], 500); 
        }
    }
}
