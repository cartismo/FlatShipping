<?php

namespace Modules\FlatShipping\Tests\Unit;

use App\Models\Currency;
use App\Models\InstalledModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

require_once dirname(__DIR__, 2) . '/Services/FlatShippingService.php';

use Modules\FlatShipping\Services\FlatShippingService;

class FlatShippingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_flat_shipping_applies_free_shipping_threshold(): void
    {
        Currency::query()->create([
            'name' => 'Euro',
            'code' => 'EUR',
            'symbol' => 'EUR',
            'symbol_left' => 'EUR ',
            'symbol_right' => null,
            'decimal_places' => 2,
            'exchange_rate' => 1,
            'is_base' => true,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $module = new InstalledModule([
            'slug' => 'flat-shipping',
            'name' => 'Flat Shipping',
            'settings' => [
                'enabled' => true,
                'title' => 'Flat Shipping',
                'description' => 'Standard delivery to address',
                'cost' => 9.99,
                'free_shipping_threshold' => 100,
                'sort_order' => 3,
            ],
        ]);

        $service = new FlatShippingService($module);

        $this->assertSame(9.99, $service->calculateCost(99.99));
        $this->assertSame(0.00, $service->calculateCost(100.00));
        $this->assertFalse($service->hasFreeShipping(99.99));
        $this->assertTrue($service->hasFreeShipping(100.00));
        $this->assertSame(0.01, round($service->getAmountForFreeShipping(99.99), 2));
        $this->assertNull($service->getAmountForFreeShipping(100.00));

        $method = $service->getShippingMethod(99.99);

        $this->assertSame(3, $method['sort_order']);
        $this->assertSame(9.99, $method['cost']);
        $this->assertIsString($method['formatted_cost']);
    }
}
