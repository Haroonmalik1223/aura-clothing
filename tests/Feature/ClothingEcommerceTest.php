<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ClothingEcommerceTest extends TestCase
{
    use DatabaseTransactions;
    public function test_storefront_home_page_loads()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('AURA');
    }

    public function test_catalog_page_loads()
    {
        $response = $this->get('/shop');
        $response->assertStatus(200);
        $response->assertSee('Complete Apparel Collection');
    }

    public function test_product_detail_page_loads_with_dynamic_variants()
    {
        $product = Product::first();
        $response = $this->get('/product/' . $product->slug);
        $response->assertStatus(200);
        $response->assertSee($product->name);
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $response = $this->get('/admin/products');
        $response->assertRedirect('/login');
    }

    public function test_login_page_loads()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Atelier Admin Studio');
    }

    public function test_admin_can_login_with_provided_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'admin@admiin.com',
            'password' => '12345678',
        ]);

        $response->assertRedirect('/admin/products');
        $this->assertAuthenticated();
    }

    public function test_admin_products_index_page_loads_for_authenticated_admin()
    {
        $user = User::where('email', 'admin@admiin.com')->first();
        $response = $this->actingAs($user)->get('/admin/products');
        $response->assertStatus(200);
        $response->assertSee('Apparel Products & Variants', false);
    }

    public function test_admin_product_create_flow_page_loads_for_authenticated_admin()
    {
        $user = User::where('email', 'admin@admiin.com')->first();
        $response = $this->actingAs($user)->get('/admin/products/create');
        $response->assertStatus(200);
        $response->assertSee('Dynamic Variant Flow');
    }

    public function test_admin_categories_index_page_loads_for_authenticated_admin()
    {
        $user = User::where('email', 'admin@admiin.com')->first();
        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertStatus(200);
        $response->assertSee('Clothing Categories');
    }

    public function test_can_create_new_category_when_authenticated()
    {
        $user = User::where('email', 'admin@admiin.com')->first();
        $randomSuffix = rand(1000, 9999);

        $response = $this->actingAs($user)->post('/admin/categories', [
            'name' => 'Knitwear & Sweaters ' . $randomSuffix,
            'description' => 'Heavy knit cardigans and merino sweaters.',
            'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2',
            'is_active' => '1',
        ]);

        $response->assertRedirect('/admin/categories');
        $this->assertDatabaseHas('categories', [
            'name' => 'Knitwear & Sweaters ' . $randomSuffix,
        ]);
    }

    public function test_can_create_product_with_dynamic_variants_format_when_authenticated()
    {
        $user = User::where('email', 'admin@admiin.com')->first();
        $category = Category::first();
        $randomSuffix = rand(1000, 9999);

        $sku1 = 'FLN-M-' . $randomSuffix;
        $sku2 = 'FLN-L-' . $randomSuffix;

        $payload = [
            'name' => 'Washed Cotton Flannel Overshirt ' . $randomSuffix,
            'category_id' => $category->id,
            'brand' => 'AURA STUDIO',
            'base_price' => 85.00,
            'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2',
            'description' => 'Brushed heavy flannel with horn buttons.',
            'is_active' => '1',
            'variants' => [
                [
                    'sku' => $sku1,
                    'price' => 85.00,
                    'stock_quantity' => 15,
                    'attributes' => [
                        'Size' => 'M',
                        'Color' => 'Forest Green Plaid',
                        'Fit' => 'Oversized',
                    ],
                ],
                [
                    'sku' => $sku2,
                    'price' => 89.00,
                    'stock_quantity' => 20,
                    'attributes' => [
                        'Size' => 'L',
                        'Color' => 'Forest Green Plaid',
                        'Fit' => 'Oversized',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($user)->post('/admin/products', $payload);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/admin/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Washed Cotton Flannel Overshirt ' . $randomSuffix,
            'base_price' => 85.00,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'sku' => $sku1,
            'price' => 85.00,
            'stock_quantity' => 15,
        ]);

        $this->assertDatabaseHas('product_variants', [
            'sku' => $sku2,
            'price' => 89.00,
            'stock_quantity' => 20,
        ]);
    }

    public function test_can_add_variant_to_shopping_cart()
    {
        $variant = ProductVariant::first();

        $response = $this->post('/cart/add', [
            'variant_id' => $variant->id,
            'quantity' => 1,
        ]);

        $response->assertSessionHas('success');

        $cartResponse = $this->get('/cart');
        $cartResponse->assertStatus(200);
        $cartResponse->assertSee($variant->product->name);
        $cartResponse->assertSee($variant->sku);
    }

    public function test_admin_can_logout()
    {
        $user = User::where('email', 'admin@admiin.com')->first();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_chatbot_endpoint_validates_required_message()
    {
        $response = $this->postJson('/chat/message', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['message']);
    }

    public function test_chatbot_endpoint_returns_ai_reply()
    {
        \Illuminate\Support\Facades\Http::fake([
            'generativelanguage.googleapis.com/*' => \Illuminate\Support\Facades\Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'We offer Cash on Delivery across Pakistan and free shipping on orders over Rs. 3,000!']
                            ]
                        ]
                    ]
                ]
            ], 200)
        ]);

        $response = $this->postJson('/chat/message', [
            'message' => 'What are your delivery and COD policies?',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'reply' => 'We offer Cash on Delivery across Pakistan and free shipping on orders over Rs. 3,000!',
        ]);
    }
}
