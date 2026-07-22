<?php
namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Core\Chatbot\Sentiment\SentimentService;

class SentimentServiceTest extends TestCase
{
    public function testPositiveSentiment()
    {
        $svc = new SentimentService();
        $this->assertEquals('positive', $svc->analyse('I am feeling great and happy'));
    }

    public function testNegativeSentiment()
    {
        $svc = new SentimentService();
        $this->assertEquals('negative', $svc->analyse('This is terrible and the worst'));
    }

    public function testNeutralSentiment()
    {
        $svc = new SentimentService();
        $this->assertEquals('neutral', $svc->analyse('Just an ordinary day'));
    }
}
?>
