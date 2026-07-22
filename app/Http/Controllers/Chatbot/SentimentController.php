<?php
namespace App\Http\Controllers\Chatbot;

use App\Core\Chatbot\Sentiment\SentimentService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SentimentController extends Controller
{
    protected SentimentService $service;

    public function __construct(SentimentService $service)
    {
        $this->service = $service;
    }

    /**
     * POST /api/chatbot/sentiment
     */
    public function analyse(Request $request)
    {
        $text = $request->input('message');
        $sentiment = $this->service->analyse($text);
        return response()->json(['sentiment' => $sentiment]);
    }
}
?>
