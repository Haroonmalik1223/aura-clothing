<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', env('GEMINI_API_KEY'));
        $this->model = config('services.gemini.model', 'gemini-3.5-flash-lite');
    }

    /**
     * Send user message with history and live store context to Gemini.
     */
    public function chat(string $userMessage, array $history = []): string
    {
        if (empty($this->apiKey)) {
            return "I apologize, but the Gemini API key is not configured in the store settings. Please provide a valid GEMINI_API_KEY in your .env file.";
        }

        $systemPrompt = $this->buildSystemPrompt();

        // Build contents payload conforming to Gemini API v1beta
        $contents = [];

        // Seed with system prompt
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => "SYSTEM INSTRUCTIONS & STORE KNOWLEDGE:\n" . $systemPrompt . "\n\nPlease confirm you understand."]
            ]
        ];

        $contents[] = [
            'role' => 'model',
            'parts' => [
                ['text' => "Understood. I am AURA's AI Personal Stylist. I will assist customers with polite, stylish, concise guidance regarding AURA's apparel, live inventory, sizing, and policies in Pakistani Rupees (PKR, Rs.)."]
            ]
        ];

        // Append conversation history
        foreach ($history as $turn) {
            $role = ($turn['role'] ?? 'user') === 'user' ? 'user' : 'model';
            $text = trim($turn['text'] ?? '');
            if ($text !== '') {
                $contents[] = [
                    'role' => $role,
                    'parts' => [
                        ['text' => $text]
                    ]
                ];
            }
        }

        // Append current message
        $contents[] = [
            'role' => 'user',
            'parts' => [
                ['text' => $userMessage]
            ]
        ];

        // Try high-speed primary model with robust fallback
        $modelsToTry = array_unique([$this->model, 'gemini-3.5-flash-lite', 'gemini-3.5-flash']);

        foreach ($modelsToTry as $currentModel) {
            $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$currentModel}:generateContent?key={$this->apiKey}";

            try {
                $response = Http::timeout(10)->withHeaders([
                    'Content-Type' => 'application/json'
                ])->post($endpoint, [
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 400,
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return trim($reply);
                    }
                }

                // If 503 (overloaded) or 429 (rate limit), continue to next model
                if (in_array($response->status(), [503, 429])) {
                    Log::warning("Model {$currentModel} returned {$response->status()}, trying fallback...");
                    continue;
                }

                Log::error("Gemini API Error ({$currentModel}): " . $response->body());
            } catch (\Exception $e) {
                Log::warning("Gemini Exception ({$currentModel}): " . $e->getMessage());
                continue;
            }
        }

        return "Thank you for reaching out to AURA. I am having a brief connection issue with our stylist server. Feel free to ask again or browse our collections!";
    }

    /**
     * Build real-time store knowledge prompt from database and brand policies.
     */
    protected function buildSystemPrompt(): string
    {
        // 1. Live Categories
        $categories = Category::active()->withCount('products')->get()->map(function ($c) {
            return "- {$c->name} ({$c->products_count} styles): {$c->description} [Link: /shop?category={$c->slug}]";
        })->implode("\n");

        // 2. Live Products & Availability Matrix
        $products = Product::active()
            ->with(['category', 'variants.attributeValues.attribute'])
            ->get()
            ->map(function ($p) {
                $sizes = [];
                $colors = [];
                $stock = $p->total_stock;

                foreach ($p->variants as $v) {
                    foreach ($v->attributeValues as $val) {
                        if ($val->attribute) {
                            $attrName = strtolower($val->attribute->name);
                            if (strpos($attrName, 'size') !== false) {
                                $sizes[] = $val->value;
                            } elseif (strpos($attrName, 'color') !== false) {
                                $colors[] = $val->value;
                            }
                        }
                    }
                }

                $sizesStr = !empty($sizes) ? implode(', ', array_unique($sizes)) : 'Standard';
                $colorsStr = !empty($colors) ? implode(', ', array_unique($colors)) : 'Assorted';
                $priceStr = $p->price_display; // Single PKR price: Rs. X
                $stockStatus = $stock > 0 ? "In Stock ({$stock} units)" : "Sold Out";
                $catName = $p->category->name ?? 'Collection';

                return "- {$p->name} ({$p->brand}) | Category: {$catName} | Price: {$priceStr} | Sizes: {$sizesStr} | Colors: {$colorsStr} | Status: {$stockStatus} | URL: /product/{$p->slug}";
            })->implode("\n");

        return <<<PROMPT
You are the official AI Personal Stylist and Customer Concierge for "AURA", a contemporary luxury Pakistani clothing brand.

BRAND ESSENCE & STYLE:
- Brand Name: AURA Clothing
- Aesthetic: Modern minimalist tailoring, Japanese raw denim, heavyweight organic French terry cotton, and architectural silhouettes.
- Tone: Friendly, sophisticated, professional, concise, and helpful. Always maintain high-end retail etiquette.

STORE POLICIES & FAQS:
1. CURRENCY: All prices are strictly in Pakistani Rupees (PKR), formatted as "Rs. X" (e.g. Rs. 5,490). Never use dollar signs (\$) and never show price ranges.
2. SHIPPING & DELIVERY:
   - Free shipping across Pakistan on all orders over Rs. 3,000.
   - Standard shipping fee: Rs. 200 for orders under Rs. 3,000.
   - Delivery Timeline: 3 to 5 business days nationwide (Karachi, Lahore, Islamabad, Rawalpindi, Faisalabad, Multan, Peshawar, etc.).
   - Dispatched via premier express couriers with tracking.
3. PAYMENT METHODS:
   - Cash on Delivery (COD) available everywhere in Pakistan.
   - Online payments via Visa, MasterCard, and direct debit.
4. RETURNS & EXCHANGES:
   - 30-day hassle-free exchange and return policy.
   - Items must be unworn, unwashed, with all original tags attached.
   - Free reverse pickup for size exchanges.
5. SIZE GUIDE:
   - S: Chest 36", Waist 30", Length 27"
   - M: Chest 38", Waist 32", Length 28"
   - L: Chest 40", Waist 34", Length 29"
   - XL: Chest 42", Waist 36", Length 30"
6. CUSTOMER CARE:
   - Email: support@aura.com.pk
   - WhatsApp / Phone: +92 300 1234567 (Mon-Sat, 9 AM - 9 PM PKT).

LIVE PRODUCT INVENTORY & AVAILABILITY (REAL-TIME DATABASE):
{$products}

ACTIVE COLLECTIONS / CATEGORIES:
{$categories}

CONVERSATION GUIDELINES:
- When a customer asks about a product, recommend specific pieces and provide their exact price (e.g. Rs. 5,490) and clickable markdown link in format: [Product Name](/product/slug).
- If they ask for clothing recommendations (e.g. "Suggest a casual outfit" or "What hoodies do you have in size L?"), look at the inventory and suggest matching items that are currently in stock!
- If asked about policies, answer directly and concisely using the FAQs above.
- Never make up fake products that are not listed in the inventory above.
PROMPT;
    }
}
