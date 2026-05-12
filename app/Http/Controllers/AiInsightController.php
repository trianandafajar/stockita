<?php

namespace App\Http\Controllers;

use App\Models\AiInsight;
use App\Services\Ai\AiInsightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiInsightController extends Controller
{
    private const STALE_AFTER_MINUTES = 10;

    public function __construct(private AiInsightService $ai) {}

    public function index()
    {
        $user = Auth::user();

        $role = match (true) {
            $user->hasRole('admin') => 'admin',
            $user->hasRole('owner') => 'owner',
            $user->hasRole('buyer') => 'buyer',
            default                 => 'guest',
        };

        return view('ai-insights.index', [
            'role'     => $role,
            'apiReady' => !empty(config('services.groq.api_key')),
        ]);
    }

    public function recommendations(Request $request): JsonResponse
    {
        return $this->serve('recommendations', $request,
            fn($scope) => $this->ai->recommendations($scope['store_id'])
        );
    }

    public function topProducts(Request $request): JsonResponse
    {
        return $this->serve('top-products', $request,
            fn($scope) => $this->ai->topProductsEvaluation($scope['store_id'])
        );
    }

    public function stockSuggestions(Request $request): JsonResponse
    {
        return $this->serve('stock-suggestions', $request,
            fn($scope) => $this->ai->stockSuggestions($scope['store_id'])
        );
    }

    public function personalized(Request $request): JsonResponse
    {
        return $this->serve('personalized', $request,
            fn($scope) => $this->ai->personalizedForBuyer($scope['user_id'])
        );
    }

    // ─────────────────────────────────────────────────────────
    // Core: serve from DB, regenerate if stale or ?refresh=1.
    // ─────────────────────────────────────────────────────────
    private function serve(string $type, Request $request, \Closure $generator): JsonResponse
    {
        try {
            $scope   = $this->resolveScope($type);
            $refresh = $request->boolean('refresh');

            $insight = AiInsight::query()
                ->where('type', $type)
                ->where('store_id', $scope['store_id'])
                ->where('user_id',  $scope['user_id'])
                ->first();

            $regenerate = $refresh
                || !$insight
                || $insight->isStale(self::STALE_AFTER_MINUTES);

            if ($regenerate) {
                $fresh = $generator($scope);

                $insight = AiInsight::updateOrCreate(
                    [
                        'type'     => $type,
                        'store_id' => $scope['store_id'],
                        'user_id'  => $scope['user_id'],
                    ],
                    [
                        'payload'      => $fresh,
                        'generated_at' => now(),
                    ]
                );
            }

            $response = $insight->payload;
            $response['generated_at'] = $insight->generated_at->toDateTimeString();
            $response['from_storage'] = !$regenerate;
            $response['stale_after']  = $insight->generated_at
                ->addMinutes(self::STALE_AFTER_MINUTES)
                ->toDateTimeString();

            return response()->json($response);
        } catch (Throwable $e) {
            Log::error('[AI Insights] ' . $e->getMessage(), ['exception' => $e]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buyer  → keyed by user_id.
     * Owner  → keyed by own store_id.
     * Admin  → optional ?store_id=N, default null = system-wide.
     */
    private function resolveScope(string $type): array
    {
        $user = Auth::user();

        if ($type === 'personalized') {
            return ['store_id' => null, 'user_id' => $user->id];
        }

        if ($user->hasRole('admin')) {
            $id = request()->integer('store_id');
            return ['store_id' => $id > 0 ? $id : null, 'user_id' => null];
        }

        return ['store_id' => $user->store?->id, 'user_id' => null];
    }
}
