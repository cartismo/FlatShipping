<?php

namespace Modules\FlatShipping\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Traits\HasMultiStoreModuleSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\FlatShipping\Services\FlatShippingService;

class FlatShippingController extends Controller
{
    use HasMultiStoreModuleSettings;

    protected function getModuleSlug(): string
    {
        return 'flat-shipping';
    }

    protected function getDefaultSettings(): array
    {
        return FlatShippingService::defaultSettings();
    }

    /**
     * Show shipping method settings
     */
    public function index(): Response
    {
        $data = $this->getMultiStoreData();
        $data['options'] = [
            'currency' => $this->getCurrencyOptions(),
        ];
        $data['translations'] = __('flatshipping::settings');

        return Inertia::render('FlatShipping::Admin/Settings', $data);
    }

    /**
     * Update shipping method settings
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'is_enabled' => 'boolean',
            'settings.enabled' => 'boolean',
            'settings.title' => 'required|string|max:255',
            'settings.description' => 'nullable|string|max:1000',
            'settings.cost' => 'required|numeric|min:0',
            'settings.free_shipping_threshold' => 'nullable|numeric|min:0',
            'settings.sort_order' => 'integer|min:0',
        ]);

        return $this->saveStoreSettings($request);
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