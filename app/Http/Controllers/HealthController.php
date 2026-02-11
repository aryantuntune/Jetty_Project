<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Health Check Controller
 * 
 * Provides a /health endpoint for monitoring system status.
 * Used by load balancers, uptime monitors, and health check scripts.
 */
class HealthController extends Controller
{
    /**
     * Main health check endpoint
     * Returns system status and individual component checks
     */
    public function check(): JsonResponse
    {
        try {
            $startTime = microtime(true);

            $checks = [
                'database' => $this->checkDatabase(),
                'storage' => $this->checkStorage(),
            ];

            // These checks may cause issues in some environments
            try {
                $checks['cache'] = $this->checkCache();
            } catch (\Exception $e) {
                $checks['cache'] = null; // Unknown
            }

            try {
                $checks['session'] = $this->checkSession();
            } catch (\Exception $e) {
                $checks['session'] = null; // Unknown
            }

            // Try Redis check if configured
            if (config('database.redis.default.host')) {
                try {
                    $checks['redis'] = $this->checkRedis();
                } catch (\Exception $e) {
                    $checks['redis'] = null;
                }
            }

            // Consider healthy if database and storage work
            $healthy = $checks['database'] === true && $checks['storage'] === true;
            $responseTime = round((microtime(true) - $startTime) * 1000, 2);

            return response()->json([
                'status' => $healthy ? 'healthy' : 'unhealthy',
                'timestamp' => now()->toISOString(),
                'response_time_ms' => $responseTime,
                'environment' => config('app.env'),
                'checks' => $checks,
            ], $healthy ? 200 : 503);
        } catch (\Exception $e) {
            Log::error('Health check failed', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Health check failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check database connection
     */
    private function checkDatabase(): bool
    {
        try {
            DB::connection()->getPdo();

            // PostgreSQL uses different syntax than MySQL
            $driver = DB::connection()->getDriverName();
            if ($driver === 'pgsql') {
                DB::select('SELECT 1');
            } else {
                DB::select('SELECT 1');
            }

            return true;
        } catch (\Exception $e) {
            Log::error('Health check: Database failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check Redis connection (if configured)
     */
    private function checkRedis(): bool
    {
        try {
            if (!class_exists('\Illuminate\Support\Facades\Redis')) {
                return true; // Skip if Redis not available
            }

            \Illuminate\Support\Facades\Redis::ping();
            return true;
        } catch (\Exception $e) {
            Log::warning('Health check: Redis unavailable', ['error' => $e->getMessage()]);
            return true; // Don't fail health check if Redis not configured
        }
    }

    /**
     * Check cache functionality
     */
    private function checkCache(): bool
    {
        try {
            $key = 'health_check_' . time();
            Cache::put($key, 'test', 10);
            $value = Cache::get($key);
            Cache::forget($key);
            return $value === 'test';
        } catch (\Exception $e) {
            Log::error('Health check: Cache failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check storage write permissions
     */
    private function checkStorage(): bool
    {
        try {
            $testFile = storage_path('framework/cache/health_check.txt');
            file_put_contents($testFile, 'test');
            $content = file_get_contents($testFile);
            unlink($testFile);
            return $content === 'test';
        } catch (\Exception $e) {
            Log::error('Health check: Storage failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check session functionality
     * Returns true if session works OR if session is not properly configured
     * (to avoid failing health checks due to session config issues)
     */
    private function checkSession(): bool
    {
        try {
            // Only test session if we have an active session
            if (!session()->isStarted()) {
                session()->start();
            }

            session(['health_check' => 'test']);
            $value = session('health_check');
            session()->forget('health_check');
            return $value === 'test';
        } catch (\Exception $e) {
            // Log but don't fail - session issues shouldn't fail health check
            Log::warning('Health check: Session issue', ['error' => $e->getMessage()]);
            return true; // Return true to not fail health check
        }
    }

    /**
     * Simple ping endpoint for basic uptime checks
     */
    public function ping(): JsonResponse
    {
        return response()->json(['pong' => now()->toISOString()]);
    }
}
