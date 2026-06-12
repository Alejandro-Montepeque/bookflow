<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = auth()->user()
            ->services()
            ->withCount(['bookings', 'availabilityRules'])
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();

        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', Service::class);

        return Inertia::render('Services/Create');
    }

    public function store(StoreServiceRequest $request): RedirectResponse
    {
        $service = auth()->user()->services()->create($request->validated());

        // Send the provider straight to Edit so they can configure availability rules
        // before sharing the public link.
        return redirect()->route('services.edit', $service->id)
            ->with('success', 'Service created. Now set your availability.');
    }

    public function edit(Service $service): Response
    {
        Gate::authorize('update', $service);

        return Inertia::render('Services/Edit', [
            'service' => $service->load('availabilityRules'),
        ]);
    }

    public function update(UpdateServiceRequest $request, Service $service): RedirectResponse
    {
        // Authorization already enforced inside UpdateServiceRequest::authorize().
        $service->update($request->validated());

        return redirect()->route('services.index')
            ->with('success', 'Service updated.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        Gate::authorize('delete', $service);

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'Service deleted.');
    }
}
