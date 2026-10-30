<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProductEditTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::where('email', 'admin@admiin.com')->first();
        if (!$this->admin) {
            $this->admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@admiin.com',
                'password' => bcrypt('12345678'),
                'is_admin' => true,
            ]);
        }
    }

    public function test_admin_product_edit_page_loads_with_dynamic_variants()
    {
        $product = Product::with('variants.attributeValues.attribute')->first();
        $this->assertNotNull($product);

        $response = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));
        $response->assertStatus(200);
        $response->assertSee('Dynamic Variant Flow');
        $response->assertSee('Dynamic Clothing Attributes');
        $response->assertSee('Generated Variant Matrix');
    }

    public function test_admin_can_update_product_and_dynamic_variants()
    {
        $product = Product::with('variants')->first();
        $this->assertNotNull($product);

        $firstVariant = $product->variants->first();

        $updateData = [
            'name' => $product->name . ' Updated',
            'category_id' => $product->category_id,
            'brand' => 'AURA TEST',
            'base_price' => 5990,
            'image' => $product->image,
            'description' => 'Updated apparel description',
            'is_active' => '1',
            'variants' => [
                [
                    'id' => $firstVariant->id,
                    'sku' => $firstVariant->sku,
                    'price' => 5990,
                    'stock_quantity' => 45,
                    'attributes' => [
                        'Size' => 'M',
                        'Color' => 'Washed Black'
                    ]
                ],
                [
                    'id' => null, // newly added variant
                    'sku' => 'AURA-NEW-XXL-01',
                    'price' => 6490,
                    'stock_quantity' => 12,
                    'attributes' => [
                        'Size' => 'XXL',
                        'Color' => 'Washed Black'
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), $updateData);
        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertEquals($product->name, $updateData['name']);
        $this->assertEquals($product->base_price, 5990);

        // Check that new variant was created
        $this->assertDatabaseHas('product_variants', [
            'product_id' => $product->id,
            'sku' => 'AURA-NEW-XXL-01',
            'stock_quantity' => 12
        ]);
    }
}
