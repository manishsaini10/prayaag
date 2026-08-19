<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

/**
 * Validates uploaded files using PHP finfo (magic bytes) instead of
 * trusting the client-supplied MIME type or file extension.
 *
 * Also provides a ClamAV malware scanning stub that:
 *  - Does nothing (logs a warning) when ClamAV is disabled.
 *  - Rejects the file if ClamAV finds a virus.
 *  - Rejects the file if ClamAV is enabled but unavailable (fail-closed),
 *    unless CLAMAV_FAIL_OPEN=true is set in .env.
 */
class MimeVerifier
{
    /**
     * Allowed document MIME types (for résumés / attachments).
     * Maps magic-byte detected MIME  →  human-readable label.
     */
    public const ALLOWED_DOCUMENT_MIMES = [
        'application/pdf'  => 'PDF',
        'application/msword' => 'DOC',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
    ];

    /**
     * Allowed video MIME types (for uploaded video testimonials).
     */
    public const ALLOWED_VIDEO_MIMES = [
        'video/mp4'       => 'MP4',
        'video/quicktime' => 'MOV',
        'video/webm'      => 'WEBM',
        'video/x-msvideo' => 'AVI',
    ];

    /**
     * Verify that the uploaded file's real MIME type (from magic bytes)
     * is in the provided allow-list.
     *
     * @param  UploadedFile  $file
     * @param  string[]      $allowedMimes  Array of allowed MIME type strings.
     * @return bool  True if the file passes the magic-byte check.
     */
    public function verifyMime(UploadedFile $file, array $allowedMimes): bool
    {
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $realMime = $finfo->file($file->getRealPath());

        if (! in_array($realMime, $allowedMimes, true)) {
            Log::warning('MimeVerifier: suspicious file rejected', [
                'claimed_mime' => $file->getMimeType(),
                'real_mime'    => $realMime,
                'extension'    => $file->getClientOriginalExtension(),
                'original_name'=> $file->getClientOriginalName(),
            ]);
            return false;
        }

        return true;
    }

    /**
     * Scan a file for malware via ClamAV.
     *
     * Returns true  →  file is clean (or ClamAV is disabled/bypassed).
     * Returns false →  virus found, or ClamAV unavailable in fail-closed mode.
     */
    public function scanForMalware(UploadedFile $file): bool
    {
        if (! config('privacy.clamav_enabled', false)) {
            return true; // scanning disabled — skip silently
        }

        $binary = config('privacy.clamav_binary', 'clamdscan');
        $path   = $file->getRealPath();

        // Verify the binary exists before calling it
        $binaryPath = trim(shell_exec('which ' . escapeshellarg($binary)) ?: '');
        if (empty($binaryPath) && PHP_OS_FAMILY === 'Windows') {
            $binaryPath = trim(shell_exec('where ' . escapeshellarg($binary)) ?: '');
        }

        if (empty($binaryPath)) {
            $failOpen = config('privacy.clamav_fail_open', false);
            Log::warning('ClamAV binary not found', [
                'binary'     => $binary,
                'fail_open'  => $failOpen,
                'file'       => $file->getClientOriginalName(),
            ]);
            return $failOpen; // respect configured fail-open / fail-closed policy
        }

        $command    = escapeshellarg($binaryPath) . ' --no-summary ' . escapeshellarg($path) . ' 2>&1';
        $output     = [];
        $returnCode = 0;

        exec($command, $output, $returnCode);

        if ($returnCode === 0) {
            return true; // clean
        }

        if ($returnCode === 1) {
            Log::critical('ClamAV: virus detected — upload rejected', [
                'file'   => $file->getClientOriginalName(),
                'output' => implode(' ', $output),
            ]);
            return false; // infected
        }

        // Return code 2 = ClamAV error
        $failOpen = config('privacy.clamav_fail_open', false);
        Log::error('ClamAV scan error', [
            'code'      => $returnCode,
            'output'    => implode(' ', $output),
            'fail_open' => $failOpen,
        ]);
        return $failOpen;
    }

    /**
     * Convenience: verify MIME + scan for malware in one call.
     * Returns an error message string on failure, or null on success.
     */
    public function validate(UploadedFile $file, array $allowedMimes): ?string
    {
        if (! $this->verifyMime($file, $allowedMimes)) {
            return 'The uploaded file type is not permitted. Allowed types: ' . implode(', ', array_values($allowedMimes)) . '.';
        }

        if (! $this->scanForMalware($file)) {
            return 'The uploaded file was rejected by our malware scanner. Please ensure the file is safe and try again.';
        }

        return null;
    }
}
