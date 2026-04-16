<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MedicalStatusController extends Controller
{
    public function show(string $student_id): JsonResponse
    {
        $user = User::query()->where('student_id', $student_id)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'student_id' => (string) $user->student_id,
                'student_number' => (string) ($user->student_number ?? ''),
                'is_health_profile_completed' => (int) ((bool) $user->is_health_profile_completed),
                'status' => (bool) $user->is_health_profile_completed,
                'timestamps' => [
                    'created_at' => optional($user->created_at)->toIso8601String(),
                    'updated_at' => optional($user->updated_at)->toIso8601String(),
                ],
            ],
            'message' => 'Medical status retrieved successfully.',
        ]);
    }
}
