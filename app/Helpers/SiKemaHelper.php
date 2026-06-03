<?php

use Illuminate\Http\JsonResponse;

if (! function_exists('format_rupiah')) {
    /**
     * Format angka ke bentuk mata uang Rupiah.
     */
    function format_rupiah(int|float $nominal): string
    {
        return 'Rp ' . number_format($nominal, 0, ',', '.');
    }
}

if (! function_exists('format_tanggal')) {
    /**
     * Format tanggal ke format Indonesia.
     */
    function format_tanggal(string $tanggal): string
    {
        return \Carbon\Carbon::parse($tanggal)->locale('id')->isoFormat('D MMMM YYYY');
    }
}

if (! function_exists('api_error_response')) {
    /**
     * Standardized error response untuk API / JSON.
     */
    function api_error_response(int $code, string $message, array $errors = []): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'code' => $code,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }
}

if (! function_exists('api_success_response')) {
    /**
     * Standardized success response untuk API / JSON.
     */
    function api_success_response(string $message, mixed $data = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}
