<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\TransactionItem;

class AiController extends Controller
{
    // Web UI for AI Assistant
    public function index()
    {
        $history = session('ai_history', []);
        return view('ai.index', compact('history'));
    }

    // API for Admin Chat (Web)
    public function adminChat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $userMessage = trim((string) $request->input('message'));

        if ($userMessage === '') {
            return response()->json(['message' => 'Please type a message.'], 422);
        }

        // Optional: allow frontend to reset the chat by sending reset=true
        if ($request->boolean('reset')) {
            try {
                if (method_exists($request, 'session')) {
                    $request->session()->forget('ai_history');
                }
            } catch (\Throwable $t) {
                // ignore if session not available
            }
        }

        // Small dynamic context
        $storeName = 'DeSeventeen POS';
        $currency = 'PHP';
        $now = now()->format('Y-m-d H:i');

        // GATHER REAL DB STATS
        $contextData = $this->getBusinessContext();
        $salesContext = $contextData['summary_text'];

        // SYSTEM prompt: smarter + interactive + structured
        $systemPrompt = <<<SYS
You are "DeSeventeen Assistant", a smart, practical, and friendly cafe business assistant for {$storeName}.

PRIMARY GOAL:
Help the admin with sales insights, menu/product strategy, staff operations, promotions, customer experience, and POS workflows—using clear and actionable advice.

REAL-TIME BUSINESS CONTEXT (Database):
{$salesContext}
Note: Inventory/Stock tracking is NOT supported by the system. If asked about stock levels, explicitly say you cannot check them because the system does not track inventory.

CONTEXT:
- Business type: Cafe
- Currency: {$currency}
- Current datetime: {$now}
- You NOW have access to the above summary data.

BEHAVIOR RULES:
1) Be interactive: If the request is missing key details, ask up to 2 clarifying questions BEFORE making assumptions.
2) Use the REAL-TIME BUSINESS CONTEXT provided above to answer questions about sales or top items.
3) Provide actionable outputs: steps, checklists, simple calculations, and concrete examples.
4) Prefer Taglish (Filipino + English) unless the admin uses pure English; then reply in English.
5) Keep replies helpful and concise; avoid long essays unless the admin asks for detail.
6) If the admin asks for "insights", suggest what to check (gross/net sales, transactions, AOV, top items, peak hours, slow movers, margins, wastage).
7) When giving recommendations, include: (a) reason, (b) measurable next step.

DEFAULT RESPONSE FORMAT (use most of the time):
**Quick Answer:** (1–3 sentences)
**Key Insights:** (bullets)
**Recommended Actions:** (numbered steps)
**Question (if needed):** (1–2 questions max)

TONE:
Warm, professional, slightly conversational.
SYS;

        // Session-based history (for more interactive multi-turn chat)
        $maxHistoryMessages = 12; // excluding system
        $history = [];

        try {
            if (method_exists($request, 'session')) {
                $history = $request->session()->get('ai_history', []);
            }
        } catch (\Throwable $t) {
            $history = [];
        }

        // Sanitize history
        if (!is_array($history)) {
            $history = [];
        }

        $history = array_values(array_filter($history, function ($m) {
            return is_array($m)
                && isset($m['role'], $m['content'])
                && in_array($m['role'], ['user', 'assistant'], true)
                && is_string($m['content'])
                && trim($m['content']) !== '';
        }));

        // Add latest user message
        $history[] = ['role' => 'user', 'content' => $userMessage];

        // Trim history
        if (count($history) > $maxHistoryMessages) {
            $history = array_slice($history, -$maxHistoryMessages);
        }

        // Final messages: system + history
        $messages = array_merge(
            [['role' => 'system', 'content' => $systemPrompt]],
            $history
        );

        Log::info('AI System Prompt:', ['prompt' => $systemPrompt]); // DEBUG LOG

        try {
            // Fetch config (defaults provided in config/services.php)
            $ollamaUrl = config('services.ollama.url');
            $ollamaModel = config('services.ollama.model');

            $response = Http::timeout(300)
                ->acceptJson()
                ->asJson()
                ->post($ollamaUrl . '/api/chat', [
                    'model' => $ollamaModel,
                    'stream' => false,
                    'messages' => $messages,
                    'options' => [
                        // Tune for stability + less hallucination
                        'temperature' => 0.4,
                        'top_p' => 0.9,
                        'num_ctx' => 4096,
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('Ollama chat failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'message' => 'AI is sleeping (Error: ' . $response->status() . ')'
                ], 500);
            }

            $content = data_get($response->json(), 'message.content');

            if (!is_string($content) || trim($content) === '') {
                Log::warning('Ollama response missing message.content', [
                    'json' => $response->json(),
                ]);

                return response()->json([
                    'message' => 'AI returned an empty response. Try again.'
                ], 500);
            }

            // Save assistant reply for next turn
            $history[] = ['role' => 'assistant', 'content' => $content];

            if (count($history) > $maxHistoryMessages) {
                $history = array_slice($history, -$maxHistoryMessages);
            }

            try {
                if (method_exists($request, 'session')) {
                    $request->session()->put('ai_history', $history);
                }
            } catch (\Throwable $t) {
                // ignore if session not available (e.g., API routes)
            }

            return response()->json(['message' => $content]);

        } catch (\Exception $e) {
            Log::error('Connection to Ollama failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'message' => 'Connection to AI failed (' . $ollamaUrl . '). Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // API for Sales Insights (Web)
    public function salesInsights(Request $request)
    {
        // Stub implementation
        return response()->json([
            'insights' => 'Sales are trending up! (Stub)'
        ]);
    }

    // Debug Context (Web)
    public function debugContext(Request $request)
    {
        $historyCount = 0;

        try {
            if (method_exists($request, 'session')) {
                $history = $request->session()->get('ai_history', []);
                $historyCount = is_array($history) ? count($history) : 0;
            }
        } catch (\Throwable $t) {
            $historyCount = 0;
        }

        return response()->json([
            'status' => 'Debug context active',
            'history_count' => $historyCount,
        ]);
    }

    // REMOVED: recommend (FlutterFlow)
    // REMOVED: recommendWithN8n (FlutterFlow)

    /**
     * Helper: Fetch summary stats from DB
     */
    private function getBusinessContext()
    {
        try {
            // 1. Daily Sales from POS Transactions
            $today = now()->format('Y-m-d');
            
            $posSales = Transaction::whereDate('created_at', $today)->sum('total_amount');
            $posCount = Transaction::whereDate('created_at', $today)->count();

            // 2. Daily Sales from Mobile Orders (Completed)
            // Assuming 'status' = 'completed' or 'paid'. If uncertain, we count all 'completed'.
            $mobileSales = Order::whereDate('created_at', $today)
                // ->where('status', 'completed') // Uncomment if you want only completed
                ->sum('total_price');
            $mobileCount = Order::whereDate('created_at', $today)->count();

            $totalSales = $posSales + $mobileSales;
            $totalTxns  = $posCount + $mobileCount;

            // 3. Top Selling Items (Today) - from POS Items
            $topItems = TransactionItem::whereDate('created_at', $today)
                ->with('product')
                ->select('product_id', DB::raw('sum(quantity) as qty'))
                ->groupBy('product_id')
                ->orderByDesc('qty')
                ->limit(3)
                ->get();
            
            $topItemsList = $topItems->map(function($i) {
                return ($i->product ? $i->product->name : 'Unknown Product') . " ({$i->qty})";
            })->join(', ');

            if (empty($topItemsList)) $topItemsList = "None yet";

            // 4. Products & Categories (Menu)
            $categories = Category::with('products')->get();
            $menuList = [];
            
            foreach ($categories as $cat) {
                $prods = $cat->products->map(function($p) {
                    return "{$p->name} (₱" . number_format($p->price, 0) . ")";
                })->join(', ');
                
                if (!empty($prods)) {
                    $menuList[] = "- {$cat->name}: {$prods}";
                }
            }
            $menuText = implode("\n", $menuList);
            if (empty($menuText)) $menuText = "No menu items found.";

            // Format for AI
            $text = "
DAILY STATS ({$today}):
- Total Sales: ₱" . number_format($totalSales, 2) . "
- Transactions: {$totalTxns} (POS: {$posCount}, Mobile: {$mobileCount})
- Top Items (POS Today): {$topItemsList}
- Inventory Tracking: Not enabled (No stock data available).

FULL MENU & PRICES:
{$menuText}
            ";

            return [
                'summary_text' => trim($text),
                'total_sales' => $totalSales,
            ];

        } catch (\Exception $e) {
            Log::error("Error fetching AI context: " . $e->getMessage());
            return [
                'summary_text' => "Error fetching database stats. Please check logs.",
                'total_sales' => 0
            ];
        }
    }
}
