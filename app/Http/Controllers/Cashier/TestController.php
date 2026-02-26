<?php

namespace App\Http\Controllers\Cashier;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function testOcr(Request $request)
    {
        try {
            $data = $request->all();
            Log::info('Test OCR received', $data);
            
            return response()->json([
                'success' => true,
                'message' => 'Test OCR working',
                'data' => $data
            ]);
        } catch (\Throwable $e) {
            Log::error('Test OCR failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Test failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
