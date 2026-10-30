<?php

namespace App\Http\Controllers;

use App\Services\GeminiChatService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiChatService $geminiService)
    {
        $this->geminiService = $geminiService;
    }

    /**
     * Handle incoming chat message from the customer floating widget.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'history' => 'nullable|array|max:20',
        ]);

        $userMessage = $request->input('message');
        $history = $request->input('history', []);

        $reply = $this->geminiService->chat($userMessage, $history);

        return response()->json([
            'status' => 'success',
            'reply' => $reply,
        ]);
    }
}
