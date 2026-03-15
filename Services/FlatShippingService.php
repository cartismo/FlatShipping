<?php

namespace Modules\FlatShipping\Services;

use App\Contracts\AbstractShippingMethod;
use App\Models\InstalledModule;

class FlatShippingService extends AbstractShippingMethod
{
    public static function defaultSettings(): array
    {
        return [
            'enabled' => true,
            'title' => self::resolveDefaultTranslation('default_title', 'Flat Shipping'),
            'description' => self::resolveDefaultTranslation('default_description', 'Standard delivery to address'),
            'cost' => 0.00,
            'free_shipping_threshold' => null,
            'sort_order' => 0,
        ];
    }

    protected static function resolveDefaultTranslation(string $key, string $fallback): string
    {
        $translationKey = 'flatshipping::settings.' . $key;
        $translated = trans($translationKey);

        if ($translated !== $translationKey) {
            return $translated;
        }

        $fallbackLocale = config('app.fallback_locale', 'en');
        $fallbackTranslation = trans($translationKey, [], $fallbackLocale);

        return $fallbackTranslation !== $translationKey ? $fallbackTranslation : $fallback;
    }

    public function __construct(?InstalledModule $module = null)
    {
        // If no module passed, load it
        if ($module === null) {
            $module = InstalledModule::where('slug', 'flat-shipping')->first();
        }

        parent::__construct($module);

        // Merge with default settings
        $this->settings = array_replace_recursive(self::defaultSettings(), $this->settings);
    }

    /**
     * Get delivery type - this is address-based delivery
     */
    public function getDeliveryType(): string
    {
        return self::DELIVERY_TYPE_ADDRESS;
    }

    /**
     * Calculate shipping cost for cart
     */
    public function calculateCost(float $cartTotal, float $cartWeight = 0, array $deliveryData = []): float
    {
        if (!$this->isEnabled()) {
            return 0.00;
        }

        $cost = (float) ($this->settings['cost'] ?? 0.00);
        $threshold = $this->settings['free_shipping_threshold'] ?? null;

        // Free shipping if cart total exceeds threshold
        if ($threshold !== null && $cartTotal >= (float) $threshold) {
            return 0.00;
        }

        return $cost;
    }

    /**
     * Check if free shipping is available based on cart total
     */
    public function hasFreeShipping(float $cartTotal): bool
    {
        $threshold = $this->settings['free_shipping_threshold'] ?? null;

        return $threshold !== null && $cartTotal >= (float) $threshold;
    }

    /**
     * Get amount remaining for free shipping
     */
    public function getAmountForFreeShipping(float $cartTotal): ?float
    {
        $threshold = $this->settings['free_shipping_threshold'] ?? null;

        if ($threshold === null || $cartTotal >= (float) $threshold) {
            return null;
        }

        return (float) $threshold - $cartTotal;
    }

    /**
     * Get icon for this method
     */
    protected function getIcon(): string
    {
        return 'truck';
    }
}
