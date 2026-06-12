<?php

namespace App\Http\Controllers;

use App\Http\Requests\SyncAvailabilityRulesRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class AvailabilityRuleController extends Controller
{
    /**
     * Replace the full set of availability rules for a service.
     * Simpler than per-rule CRUD: the frontend sends the entire desired state
     * and we sync inside a single transaction.
     */
    public function sync(SyncAvailabilityRulesRequest $request, Service $service): RedirectResponse
    {
        DB::transaction(function () use ($request, $service) {
            $service->availabilityRules()->delete();

            $rules = collect($request->validated('rules', []))
                ->map(fn (array $r) => [
                    'day_of_week' => $r['day_of_week'],
                    'start_time' => $r['start_time'],
                    'end_time' => $r['end_time'],
                ])
                ->all();

            if (!empty($rules)) {
                $service->availabilityRules()->createMany($rules);
            }
        });

        return redirect()->route('services.edit', $service->id)
            ->with('success', 'Availability updated.');
    }
}
