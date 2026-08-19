<?php

namespace Tests\Unit;

use App\Services\MimeVerifier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MimeVerifierTest extends TestCase
{
    protected MimeVerifier $verifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->verifier = new MimeVerifier();
    }

    public function test_document_mimes_allowlist_contains_pdf_doc_docx(): void
    {
        $allowed = array_keys(MimeVerifier::ALLOWED_DOCUMENT_MIMES);

        $this->assertContains('application/pdf', $allowed);
        $this->assertContains('application/msword', $allowed);
        $this->assertContains('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $allowed);
    }

    public function test_video_mimes_allowlist_contains_mp4_mov_webm(): void
    {
        $allowed = array_keys(MimeVerifier::ALLOWED_VIDEO_MIMES);

        $this->assertContains('video/mp4', $allowed);
        $this->assertContains('video/quicktime', $allowed);
        $this->assertContains('video/webm', $allowed);
    }

    public function test_verify_mime_accepts_valid_pdf_magic_bytes(): void
    {
        $pdfContent = "%PDF-1.4 test pdf header";
        $file = UploadedFile::fake()->createWithContent('document.pdf', $pdfContent);

        $result = $this->verifier->verifyMime($file, ['application/pdf']);
        $this->assertTrue($result);
    }

    public function test_verify_mime_rejects_disguised_exe_file(): void
    {
        $exeContent = "MZ\x90\x00\x03\x00\x00\x00\x04\x00\x00\x00\xFF\xFF";
        $file = UploadedFile::fake()->createWithContent('malicious.pdf', $exeContent);

        $result = $this->verifier->verifyMime($file, ['application/pdf']);
        $this->assertFalse($result);
    }

    public function test_scan_for_malware_passes_when_clamav_disabled(): void
    {
        config(['privacy.clamav_enabled' => false]);

        $file = UploadedFile::fake()->create('test.pdf', 100, 'application/pdf');

        $this->assertTrue($this->verifier->scanForMalware($file));
    }
}
