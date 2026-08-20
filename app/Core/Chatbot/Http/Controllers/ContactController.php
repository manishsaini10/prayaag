<?php

namespace App\Core\Chatbot\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Chatbot\Enterprise\Contact;
use App\Models\Chatbot\Enterprise\Note;
use App\Models\Chatbot\Enterprise\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('chatbot.contacts.view');
        $query = Contact::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', "%{$request->search}%")
                  ->orWhere('last_name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        $contacts = $query->latest()->paginate(20);

        if ($request->wantsJson()) {
            return response()->json($contacts);
        }

        return view('chatbot.admin.contacts.index', compact('contacts'));
    }

    public function show(Contact $contact)
    {
        Gate::authorize('chatbot.contacts.view');
        if (request()->wantsJson()) {
            return response()->json($contact->load(['visitor', 'companies']));
        }

        return view('chatbot.admin.contacts.show', compact('contact'));
    }

    public function create()
    {
        Gate::authorize('chatbot.contacts.create');
        return redirect()->route('admin.chatbot.contacts.index');
    }

    public function edit(Contact $contact)
    {
        Gate::authorize('chatbot.contacts.update');
        return redirect()->route('admin.chatbot.contacts.show', $contact);
    }


    public function store(Request $request)
    {
        Gate::authorize('chatbot.contacts.create');
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'alternative_phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'source' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
            'visitor_id' => 'nullable|exists:chatbot_visitors,id',
            'custom_fields' => 'nullable|json',
            'tags' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        $contact = Contact::create($data);

        if ($request->wantsJson()) {
            return response()->json($contact, 201);
        }

        return redirect()->route('admin.chatbot.contacts.index')
            ->with('success', 'Contact created successfully.');
    }

    public function update(Request $request, Contact $contact)
    {
        Gate::authorize('chatbot.contacts.update');
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'alternative_phone' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:10',
            'source' => 'nullable|string|max:30',
            'status' => 'nullable|string|max:20',
            'custom_fields' => 'nullable|json',
            'tags' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        $contact->update($data);

        if ($request->wantsJson()) {
            return response()->json($contact);
        }

        return redirect()->route('admin.chatbot.contacts.index')
            ->with('success', 'Contact updated successfully.');
    }

    public function destroy(Contact $contact)
    {
        Gate::authorize('chatbot.contacts.delete');
        $contact->delete();

        if (request()->wantsJson()) {
            return response()->json(['message' => 'Contact deleted successfully.']);
        }

        return redirect()->route('admin.chatbot.contacts.index')
            ->with('success', 'Contact deleted successfully.');
    }

    public function addNote(Request $request, Contact $contact)
    {
        Gate::authorize('chatbot.contacts.update');
        $data = $request->validate([
            'body' => 'required|string',
            'is_pinned' => 'boolean',
        ]);

        $note = Note::create([
            'author_id' => auth()->id(),
            'notable_type' => get_class($contact),
            'notable_id' => $contact->id,
            'body' => $data['body'],
            'is_pinned' => $data['is_pinned'] ?? false,
        ]);

        if ($request->wantsJson()) {
            return response()->json($note, 201);
        }

        return back()->with('success', 'Note added successfully.');
    }

    public function addTag(Request $request, Contact $contact)
    {
        Gate::authorize('chatbot.contacts.update');
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $tag = Tag::create([
            'name' => $data['name'],
            'slug' => \Illuminate\Support\Str::slug($data['name']),
            'color' => $data['color'] ?? '#6366f1',
            'taggable_type' => get_class($contact),
            'taggable_id' => $contact->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json($tag, 201);
        }

        return back()->with('success', 'Tag added successfully.');
    }
}
