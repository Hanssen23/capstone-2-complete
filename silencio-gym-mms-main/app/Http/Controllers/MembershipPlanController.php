<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MembershipPlanController extends Controller
{
    /**
     * Get all active membership plans for members
     */
    public function getActivePlans()
    {
        $cacheKey = 'active_membership_plans';
        
        return Cache::remember($cacheKey, 60, function () {
            return MembershipPlan::active()
                ->orderBy('price')
                ->get()
                ->map(function ($plan) {
                    return [
                        'id' => $plan->id,
                        'name' => $plan->name,
                        'description' => $plan->description,
                        'price' => $plan->price,
                        'duration_days' => $plan->duration_days,
                        'features' => $plan->features,
                        'currency' => '₱',
                        'pricing' => $this->calculatePricing($plan)
                    ];
                });
        });
    }

    /**
     * Get pricing information for all plans
     */
    public function getPricing()
    {
        $cacheKey = 'membership_pricing';
        
        return Cache::remember($cacheKey, 60, function () {
            $plans = MembershipPlan::active()->get();
            $durationTypes = config('membership.duration_types');
            
            $pricing = [];
            
            foreach ($plans as $plan) {
                $planPricing = [
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                    'base_price' => $plan->price,
                    'durations' => []
                ];
                
                foreach ($durationTypes as $key => $duration) {
                    $planPricing['durations'][] = [
                        'type' => $key,
                        'name' => $duration['name'],
                        'multiplier' => $duration['multiplier'],
                        'days' => $duration['days'],
                        'price' => $plan->price * $duration['multiplier']
                    ];
                }
                
                $pricing[] = $planPricing;
            }
            
            return $pricing;
        });
    }

    /**
     * Store a new membership plan
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:membership_plans,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $plan = MembershipPlan::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_days' => $request->duration_days,
                'features' => $request->features ?? [],
                'is_active' => $request->get('is_active', true)
            ]);

            // Clear cache to ensure real-time updates
            $this->clearMembershipCache();

            // Log the action
            Log::info('Membership plan created', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'created_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Membership plan created successfully',
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to create membership plan', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create membership plan'
            ], 500);
        }
    }

    /**
     * Update an existing membership plan
     */
    public function update(Request $request, MembershipPlan $plan)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:membership_plans,name,' . $plan->id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'features' => 'nullable|array',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $oldPlan = $plan->toArray();
            
            $plan->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_days' => $request->duration_days,
                'features' => $request->features ?? [],
                'is_active' => $request->get('is_active', $plan->is_active)
            ]);

            // Clear cache to ensure real-time updates
            $this->clearMembershipCache();

            // Trigger real-time update notification
            $this->triggerRealtimeUpdate();

            // Log the action
            Log::info('Membership plan updated', [
                'plan_id' => $plan->id,
                'old_data' => $oldPlan,
                'new_data' => $plan->toArray(),
                'updated_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Membership plan updated successfully',
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update membership plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update membership plan'
            ], 500);
        }
    }

    /**
     * Delete a membership plan
     */
    public function destroy(MembershipPlan $plan)
    {
        try {
            // Check if plan has active members
            $activeMembers = $plan->members()->where('status', 'active')->count();
            
            if ($activeMembers > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete plan. {$activeMembers} active members are using this plan."
                ], 400);
            }

            $planData = $plan->toArray();
            $plan->delete();

            // Clear cache to ensure real-time updates
            $this->clearMembershipCache();

            // Trigger real-time update notification
            $this->triggerRealtimeUpdate();

            // Log the action
            Log::info('Membership plan deleted', [
                'plan_data' => $planData,
                'deleted_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Membership plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to delete membership plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete membership plan'
            ], 500);
        }
    }

    /**
     * Toggle plan status (active/inactive)
     */
    public function toggleStatus(MembershipPlan $plan)
    {
        try {
            $plan->update(['is_active' => !$plan->is_active]);

            // Clear cache to ensure real-time updates
            $this->clearMembershipCache();

            // Trigger real-time update notification
            $this->triggerRealtimeUpdate();

            // Log the action
            Log::info('Membership plan status toggled', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'new_status' => $plan->is_active ? 'active' : 'inactive',
                'toggled_by' => auth()->user()->id ?? 'system'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan status updated successfully',
                'is_active' => $plan->is_active
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to toggle membership plan status', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan status'
            ], 500);
        }
    }

    /**
     * Get usage statistics for a plan
     */
    public function getUsageStats(MembershipPlan $plan)
    {
        try {
            $stats = [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'total_members' => $plan->members()->count(),
                'active_members' => $plan->members()->where('status', 'active')->count(),
                'total_revenue' => $plan->payments()->where('status', 'completed')->sum('amount'),
                'monthly_revenue' => $plan->payments()
                    ->where('status', 'completed')
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
                'recent_payments' => $plan->payments()
                    ->where('status', 'completed')
                    ->latest('payment_date')
                    ->take(5)
                    ->get(['id', 'member_id', 'amount', 'payment_date'])
            ];

            return response()->json([
                'success' => true,
                'stats' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get plan usage stats', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get usage statistics'
            ], 500);
        }
    }

    /**
     * Get all plans for admin management
     */
    public function getAllPlans()
    {
        $plans = MembershipPlan::withCount(['members', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    /**
     * Calculate pricing for different durations
     */
    private function calculatePricing($plan)
    {
        $durationTypes = config('membership.duration_types');
        $pricing = [];

        foreach ($durationTypes as $key => $duration) {
            $pricing[$key] = [
                'name' => $duration['name'],
                'multiplier' => $duration['multiplier'],
                'days' => $duration['days'],
                'price' => $plan->price * $duration['multiplier']
            ];
        }

        return $pricing;
    }

    /**
     * Clear membership-related cache
     */
    private function clearMembershipCache()
    {
        Cache::forget('active_membership_plans');
        Cache::forget('membership_pricing');
        Cache::forget('membership_plan_types');
        Cache::forget('membership_duration_types');
        
        // Clear Laravel's application cache
        \Artisan::call('cache:clear');
        
        // Clear config cache to ensure config changes are reflected
        \Artisan::call('config:clear');
        
        // Clear view cache to ensure template changes are reflected
        \Artisan::call('view:clear');
        
        Log::info('Membership cache cleared', [
            'cleared_by' => auth()->user()->id ?? 'system',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Trigger real-time update notification
     */
    private function triggerRealtimeUpdate()
    {
        // This method can be used to trigger additional real-time update mechanisms
        // For now, we'll just log the update trigger
        Log::info('Real-time update triggered', [
            'triggered_by' => auth()->user()->id ?? 'system',
            'timestamp' => now()->toISOString()
        ]);
    }

    /**
     * Get plan types configuration
     */
    public function getPlanTypes()
    {
        $cacheKey = 'membership_plan_types';
        
        return Cache::remember($cacheKey, 3600, function () {
            return config('membership.plan_types');
        });
    }

    /**
     * Get duration types configuration
     */
    public function getDurationTypes()
    {
        $cacheKey = 'membership_duration_types';
        
        return Cache::remember($cacheKey, 3600, function () {
            return config('membership.duration_types');
        });
    }

    /**
     * Update plan types configuration
     */
    public function updatePlanTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_types' => 'required|array',
            'plan_types.*.name' => 'required|string|max:255',
            'plan_types.*.base_price' => 'required|numeric|min:0',
            'plan_types.*.description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update config file
            $configPath = config_path('membership.php');
            $config = include $configPath;
            $config['plan_types'] = $request->plan_types;
            
            file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');

            // Clear cache
            $this->clearMembershipCache();

            // Trigger real-time update notification
            $this->triggerRealtimeUpdate();

            // Log the action
            Log::info('Plan types configuration updated', [
                'updated_by' => auth()->user()->id ?? 'system',
                'new_plan_types' => $request->plan_types
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan types updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update plan types', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update plan types'
            ], 500);
        }
    }

    /**
     * Stream real-time updates for membership plans using Server-Sent Events
     */
    public function streamUpdates()
    {
        return response()->stream(function () {
            $lastPlansHash = '';
            $lastPricingHash = '';
            
            while (true) {
                // Get current plans and pricing
                $plans = $this->getActivePlans();
                $pricing = $this->getPricing();
                
                $currentPlansHash = md5(json_encode($plans));
                $currentPricingHash = md5(json_encode($pricing));
                
                // Check if data has changed
                if ($currentPlansHash !== $lastPlansHash || $currentPricingHash !== $lastPricingHash) {
                    $data = [
                        'plans' => $plans,
                        'pricing' => $pricing,
                        'timestamp' => now()->toISOString(),
                        'plans_changed' => $currentPlansHash !== $lastPlansHash,
                        'pricing_changed' => $currentPricingHash !== $lastPricingHash
                    ];
                    
                    echo "data: " . json_encode($data) . "\n\n";
                    ob_flush();
                    flush();
                    
                    $lastPlansHash = $currentPlansHash;
                    $lastPricingHash = $currentPricingHash;
                }
                
                // Sleep for 5 seconds before next check
                sleep(5);
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Headers' => 'Cache-Control'
        ]);
    }

    /**
     * Update duration types configuration
     */
    public function updateDurationTypes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'duration_types' => 'required|array',
            'duration_types.*.name' => 'required|string|max:255',
            'duration_types.*.multiplier' => 'required|numeric|min:1',
            'duration_types.*.days' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Update config file
            $configPath = config_path('membership.php');
            $config = include $configPath;
            $config['duration_types'] = $request->duration_types;
            
            file_put_contents($configPath, '<?php return ' . var_export($config, true) . ';');

            // Clear cache
            $this->clearMembershipCache();

            // Trigger real-time update notification
            $this->triggerRealtimeUpdate();

            // Log the action
            Log::info('Duration types configuration updated', [
                'updated_by' => auth()->user()->id ?? 'system',
                'new_duration_types' => $request->duration_types
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Duration types updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update duration types', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update duration types'
            ], 500);
        }
    }
}
