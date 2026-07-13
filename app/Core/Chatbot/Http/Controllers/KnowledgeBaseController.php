<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\ChatbotKbDocument;
use App\Models\Chatbot\Enterprise\KbCategory;
use App\Core\Chatbot\Services\ChatbotRAGService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class KnowledgeBaseController extends Controller
{
    public function __construct(
        private readonly ChatbotRAGService $ragService
    ) {}

    public function index()
    {
        Gate::authorize('chatbot.kb.view');
        $documents = ChatbotKbDocument::with('category')
            ->withCount('chunks')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('chatbot.admin.kb.index', compact('documents'));
    }

    public function upload(Request $request)
    {
        Gate::authorize('chatbot.kb.upload');
        $request->validate([
            'file' => 'required|file|mimes:pdf,docx,xlsx,txt,csv|max:10240',
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:chatbot_kb_categories,id',
        ]);

        $file = $request->file('file');
        $path = $file->store('kb-documents', 'public');

        $content = '';
        $ext = $file->getClientOriginalExtension();

        if ($ext === 'txt' || $ext === 'csv') {
            $content = file_get_contents($file->getRealPath());
        } elseif ($ext === 'pdf') {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($file->getRealPath());
            $content = $pdf->getText();
        }

        if (empty(trim($content))) {
            $content = "Uploaded document: {$request->title}. File: {$file->getClientOriginalName()}";
        }

        $this->ragService->saveKbDocument(
            sourceId: 'upload-' . uniqid(),
            title: $request->title,
            type: $ext === 'pdf' ? 'pdf' : ($ext === 'docx' ? 'docx' : 'file'),
            content: $content,
            categoryId: $request->input('category_id'),
        );

        return back()->with('success', 'Document uploaded and indexed successfully.');
    }

    public function destroy(ChatbotKbDocument $document)
    {
        Gate::authorize('chatbot.kb.delete');
        $document->delete();
        return back()->with('success', 'Document deleted successfully.');
    }

    public function indexCms()
    {
        Gate::authorize('chatbot.kb.index-cms');
        $this->ragService->indexCmsContent();
        return back()->with('success', 'CMS content re-indexed successfully.');
    }

    public function categories()
    {
        Gate::authorize('chatbot.kb.view');
        $categories = KbCategory::withCount('documents')->orderBy('name')->get();
        return view('chatbot.admin.kb.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        Gate::authorize('chatbot.kb.upload');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'parent_id' => 'nullable|exists:chatbot_kb_categories,id',
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        KbCategory::create($data);

        return back()->with('success', 'Category created successfully.');
    }

    public function destroyCategory(KbCategory $category)
    {
        Gate::authorize('chatbot.kb.delete');
        $category->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
