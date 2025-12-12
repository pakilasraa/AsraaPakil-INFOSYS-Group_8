<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    /**
     * POST /api/ai/recommend
     *
     * Body JSON:
     * {
     *   "preferences": "I want a sweet iced non-coffee drink"
     * }
     *
     * Response JSON: array of suggestions
     */
    public function recommend(Request $request)
{
    // 1) Validate input
    $data = $request->validate([
        'preferences' => ['required', 'string', 'max:500'],
    ]);

    $preferences = $data['preferences'];

// 2) Category IDs
// TODO: palitan ang mga numbers sa ACTUAL IDs sa `categories` table mo.
$drinkCategoryIds = [1, 2, 5];   // hal. Coffee, Non-Coffee, Tea
$foodCategoryIds  = [3, 4];      // hal. Pastries, Sandwiches
$allowedCategoryIds = array_merge($drinkCategoryIds, $foodCategoryIds);

// 3) Simple heuristics: gusto ba niya ng DRINK / FOOD?
$lower = mb_strtolower($preferences);

$drinkKeywords = ['drink', 'coffee', 'latte', 'tea', 'milk tea', 'iced', 'hot', 'matcha'];
$foodKeywords  = ['eat', 'pastry', 'pastries', 'muffin', 'croissant', 'sandwich', 'snack', 'cake', 'cookie'];

$wantsDrink = false;
foreach ($drinkKeywords as $word) {
    if (str_contains($lower, $word)) {
        $wantsDrink = true;
        break;
    }
}

$wantsFood = false;
foreach ($foodKeywords as $word) {
    if (str_contains($lower, $word)) {
        $wantsFood = true;
        break;
    }
}


    // 4) Get products (drinks + foods only)
    $products = Product::whereIn('category_id', $allowedCategoryIds)
        ->orderBy('name')
        ->get([
            'id',
            'name',
            'category_id',
            'price',
            'price_small',
            'price_medium',
            'price_large',
        ]);

    // 5) Build menu JSON with type = "drink" | "food"
    $productsJson = $products->map(function ($p) use ($drinkCategoryIds, $foodCategoryIds) {
        $type = in_array($p->category_id, $drinkCategoryIds)
            ? 'drink'
            : 'food';

        return [
            'id'           => $p->id,
            'name'         => $p->name,
            'category_id'  => $p->category_id,
            'type'         => $type, // drink or food
            'price'        => $p->price !== null ? (float) $p->price : null,
            'price_small'  => $p->price_small !== null ? (float) $p->price_small : null,
            'price_medium' => $p->price_medium !== null ? (float) $p->price_medium : null,
            'price_large'  => $p->price_large !== null ? (float) $p->price_large : null,
        ];
    })->values()->toJson();

    // 6) Prompt for Ollama
    $prompt = <<<PROMPT
You are a barista assistant for a cafe.

The menu JSON contains both drinks and foods.
Each item has:
- id
- name
- type: "drink" or "food"
- prices (base price and optional size-based prices for drinks)

BEHAVIOR RULES (VERY IMPORTANT):
- There are two boolean flags: UserWantsDrink and UserWantsFood.
- If UserWantsDrink is true AND UserWantsFood is false:
    - The FIRST item in your list MUST be a DRINK (type = "drink").
    - You MAY add 0-2 FOOD items (type = "food") as side snacks.
- If UserWantsFood is true AND UserWantsDrink is false:
    - The FIRST item in your list MUST be a FOOD item (type = "food").
    - You MAY add 0-1 DRINK items (type = "drink") as a pairing.
- If BOTH UserWantsDrink and UserWantsFood are true:
    - The FIRST item MUST be a DRINK.
    - The SECOND item SHOULD be a FOOD item if possible.
- If both are false, choose reasonable items based on the preferences text.
- Do NOT return items that are not in the menu.
- Do NOT invent products.
- Do NOT return only food items when UserWantsDrink is true.
- Do NOT return only drink items when UserWantsFood is true.

Output format:
Respond STRICTLY as a JSON array of objects with fields:
- product_id (number, from menu.id)
- type ("drink" or "food")
- size (for drinks: "small","medium","large"; for food: null)
- reason (short string, why it fits)

Do not include any text before or after the JSON.

UserWantsDrink: {$wantsDrink}
UserWantsFood: {$wantsFood}
Customer preferences: "{$preferences}"

Menu JSON:
{$productsJson}
PROMPT;


    // 7) Call Ollama
    try {
        $response = Http::timeout(180)->post('http://127.0.0.1:11434/api/chat', [
            'model' => 'llama3',
            'stream' => false,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $prompt,
                ],
            ],
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'error' => 'Cannot connect to AI service',
            'details' => $e->getMessage(),
        ], 500);
    }

    if (! $response->ok()) {
        return response()->json([
            'error' => 'AI service error',
            'details' => $response->body(),
        ], 500);
    }

    $raw = $response->json();

    // 8) Kunin ang content sa posibleng formats
    $content = null;

    if (isset($raw['message']['content'])) {
        $content = $raw['message']['content'];
    } elseif (isset($raw['message']) && is_string($raw['message'])) {
        $content = $raw['message'];
    } elseif (isset($raw['response']) && is_string($raw['response'])) {
        $content = $raw['response'];
    }

    if (! $content) {
        return response()->json([
            'error' => 'Invalid AI response format',
            'raw'   => $raw,
        ], 500);
    }

    // 9) Parse JSON from content
    $suggestions = json_decode($content, true);

    if (! is_array($suggestions)) {
        if (preg_match('/(\[.*\]|\{.*\})/s', $content, $m)) {
            $suggestions = json_decode($m[1], true);
        }
    }

    if (! is_array($suggestions)) {
        return response()->json([
            'error' => 'AI did not return valid JSON',
            'raw'   => $content,
        ], 500);
    }

    // 10) Enrich suggestions with product info + unit price
    $enriched = collect($suggestions)->map(function ($item) use ($products, $drinkCategoryIds, $foodCategoryIds) {
        $productId = $item['product_id'] ?? null;
        $product = $products->firstWhere('id', $productId);

        if (! $product) {
            return null; // AI referenced unknown product_id
        }

        $isDrink = in_array($product->category_id, $drinkCategoryIds);
        $type = $item['type'] ?? ($isDrink ? 'drink' : 'food');
        $size = $item['size'] ?? null;

        if ($type === 'drink') {
            // default size kung wala
            if (! $size) {
                $size = 'medium';
            }

            switch ($size) {
                case 'small':
                    $unitPrice = $product->price_small ?? $product->price;
                    break;
                case 'large':
                    $unitPrice = $product->price_large ?? $product->price;
                    break;
                case 'medium':
                default:
                    $unitPrice = $product->price_medium ?? $product->price;
                    break;
            }
        } else {
            // FOOD: walang size, unit_price = base price
            $size = null;
            $unitPrice = $product->price ?? 0;
        }

        return [
            'product_id'   => $product->id,
            'name'         => $product->name,
            'type'         => $type,      // drink or food
            'size'         => $size,      // null for food
            'reason'       => $item['reason'] ?? '',
            'unit_price'   => (float) $unitPrice,
            'price'        => $product->price !== null ? (float) $product->price : null,
            'price_small'  => $product->price_small !== null ? (float) $product->price_small : null,
            'price_medium' => $product->price_medium !== null ? (float) $product->price_medium : null,
            'price_large'  => $product->price_large !== null ? (float) $product->price_large : null,
        ];
    })->filter()->values();

    return response()->json($enriched);
}


    /**
     * Helper to get menu JSON for AI context
     */
    private function getMenuContext()
    {
        $drinkCategoryIds = [1, 2, 5];
        $foodCategoryIds  = [3, 4];
        $allowedCategoryIds = array_merge($drinkCategoryIds, $foodCategoryIds);

        $products = Product::whereIn('category_id', $allowedCategoryIds)
            ->orderBy('name')
            ->get([
                'id', 'name', 'category_id', 'price',
                'price_small', 'price_medium', 'price_large',
            ]);

        return $products->map(function ($p) use ($drinkCategoryIds) {
            $type = in_array($p->category_id, $drinkCategoryIds) ? 'drink' : 'food';
            return [
                'id'           => $p->id,
                'name'         => $p->name,
                'type'         => $type,
                'price'        => (float) $p->price,
                'price_small'  => (float) $p->price_small,
                'price_medium' => (float) $p->price_medium,
                'price_large'  => (float) $p->price_large,
            ];
        })->values()->toJson();
    }

    /**
     * POST /api/recommend-products
     * Proxies to n8n with rich context (Menu + Budget)
     */
    public function recommendWithN8n(Request $request)
    {
        $validated = $request->validate([
            'preferences' => 'required|string',
            'budget'      => 'nullable|numeric',
        ]);

        $n8nUrl = 'http://localhost:5678/webhook-test/recommend-products';
        
        // Build context
        $menuJson = $this->getMenuContext();
        
        try {
            $response = Http::post($n8nUrl, [
                'preferences' => $validated['preferences'],
                'budget'      => $validated['budget'] ?? null,
                'menu_context'=> $menuJson, 
            ]);

            return response($response->body(), $response->status())
                ->header('Content-Type', 'application/json');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to connect to n8n',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
