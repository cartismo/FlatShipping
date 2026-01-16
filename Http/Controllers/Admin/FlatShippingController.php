<?php

namespace Modules\FlatShipping\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstalledModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FlatShippingController extends Controller
{
    /**
     * Show shipping method settings
     */
    public function index(): Response
    {
        $module = InstalledModule::where('slug', 'flat-shipping')->firstOrFail();

        $defaultSettings = [
            'enabled' => true,
            'title' => 'Flat Rate Shipping',
            'description' => 'Standard flat rate shipping',
            'cost' => 0.00,
            'free_shipping_threshold' => null,
            'tax_class' => null,
            'sort_order' => 0,
        ];

        $settings = array_replace_recursive($defaultSettings, $module->settings ?? []);

        return Inertia::render('FlatShipping::Admin/Settings', [
            'module' => $module,
            'settings' => $settings,
            'defaultSettings' => $defaultSettings,
        ]);
    }

    /**
     * Update shipping method settings
     */
    public function update(Request $request): RedirectResponse
    {
        $module = InstalledModule::where('slug', 'flat-shipping')->firstOrFail();

        $validated = $request->validate([
            'settings.enabled' => 'boolean',
            'settings.title' => 'required|string|max:255',
            'settings.description' => 'nullable|string|max:1000',
            'settings.cost' => 'required|numeric|min:0',
            'settings.free_shipping_threshold' => 'nullable|numeric|min:0',
            'settings.tax_class' => 'nullable|string',
            'settings.sort_order' => 'integer|min:0',
        ]);

        $module->update([
            'settings' => $validated['settings'],
        ]);

        return back()->with('success', 'Shipping settings updated successfully.');
    }
}