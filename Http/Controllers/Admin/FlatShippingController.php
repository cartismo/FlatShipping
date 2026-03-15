<?php

namespace Modules\FlatShipping\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\InstalledModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\FlatShipping\Services\FlatShippingService;

class FlatShippingController extends Controller
{
    /**
     * Show shipping method settings
     */
    public function index(): Response
    {
        $module = InstalledModule::where('slug', 'flat-shipping')->firstOrFail();

        $defaultSettings = FlatShippingService::defaultSettings();
        $settings = array_replace_recursive($defaultSettings, $module->settings ?? []);

        return Inertia::render('FlatShipping::Admin/Settings', [
            'module' => $module,
            'settings' => $settings,
            'defaultSettings' => $defaultSettings,
            'options' => [
                'currency' => $this->getCurrencyOptions(),
            ],
            'translations' => $this->getTranslations(),
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
            'settings.sort_order' => 'integer|min:0',
        ]);

        $module->update([
            'settings' => array_replace_recursive(FlatShippingService::defaultSettings(), $validated['settings']),
        ]);

        return back()->with('success', __('flatshipping::settings.saved_successfully'));
    }

    protected function getTranslations(): array
    {
        $locale = app()->getLocale();
        $fallbackLocale = config('app.fallback_locale', 'en');

        $translations = trans('flatshipping::settings');

        if (!is_array($translations)) {
            $translations = trans('flatshipping::settings', [], $fallbackLocale);
        }

        return is_array($translations) ? $translations : [];
    }

    protected function getCurrencyOptions(): array
    {
        $currency = Currency::getBaseCurrency();

        return [
            'code' => $currency?->code ?? 'EUR',
            'symbol' => $currency?->symbol,
            'symbol_left' => $currency?->symbol_left,
            'symbol_right' => $currency?->symbol_right,
        ];
    }
}
