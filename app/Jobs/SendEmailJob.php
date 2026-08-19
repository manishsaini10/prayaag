<?php

namespace App\Jobs;

use App\Core\Mail\DTO\EmailMessage;
use App\Core\Mail\MailManager;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public readonly string $emailLogId,
        public readonly EmailMessage $message
    ) {
        $this->onQueue('emails');
    }

    public function handle(MailManager $manager): void
    {
        $log = EmailLog::find($this->emailLogId);
        if (!$log) {
            Log::warning("SendEmailJob: EmailLog with ID {$this->emailLogId} not found.");
            return;
        }

        $result = $manager->executeSend($log, $this->message);

        if (!$result->success) {
            throw new \RuntimeException("Email send failed: {$result->error}");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("SendEmailJob permanently failed for log {$this->emailLogId}: {$exception->getMessage()}");
        $log = EmailLog::find($this->emailLogId);
        if ($log) {
            $log->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);
        }
    }
}
