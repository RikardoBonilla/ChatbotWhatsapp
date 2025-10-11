<?php

declare(strict_types=1);

namespace App\Presentation\Http\Controllers\WhatsApp;

use App\Domain\WhatsApp\Services\AnalyticsServiceInterface;
use App\Http\Controllers\Controller;
use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class AnalyticsController extends Controller
{
    public function __construct(
        private AnalyticsServiceInterface $analyticsService
    ) {}

    public function dashboard(Request $request): JsonResponse
    {
        try {
            $startDate = $request->get('start_date')
                ? DateTimeImmutable::createFromFormat('Y-m-d', $request->get('start_date'))
                : null;

            $endDate = $request->get('end_date')
                ? DateTimeImmutable::createFromFormat('Y-m-d', $request->get('end_date'))
                : null;

            $data = $this->analyticsService->getDashboardData($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve analytics data',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function popularKeywords(Request $request): JsonResponse
    {
        try {
            $limit = (int) ($request->get('limit', 10));
            $keywords = $this->analyticsService->getPopularKeywords($limit);

            return response()->json([
                'success' => true,
                'data' => $keywords,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve popular keywords',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function dailyStats(Request $request): JsonResponse
    {
        try {
            $date = $request->get('date')
                ? DateTimeImmutable::createFromFormat('Y-m-d', $request->get('date'))
                : new DateTimeImmutable();

            $stats = $this->analyticsService->getMessageStatistics($date);
            $peakHours = $this->analyticsService->getPeakHours($date);
            $activeUsers = $this->analyticsService->getActiveUsers($date);

            return response()->json([
                'success' => true,
                'data' => [
                    'statistics' => $stats,
                    'peak_hours' => $peakHours,
                    'active_users' => $activeUsers,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to retrieve daily statistics',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}