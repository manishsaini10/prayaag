@extends('admin.layout')

@section('title', 'Admission Forms')
@section('subtitle', $forms->count() . ' ' . \Illuminate\Support\Str::plural('form', $forms->count()))

@section('actions')
    <a href="{{ url('/admin/forms/create') }}" class="btn primary"><x-admin.icon name="plus"/> New form</a>
@endsection

@section('content')

@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<div class="card" style="overflow:hidden">
    <table>
        <thead><tr><th>Form</th><th>Fields</th><th>Submissions</th><th>Status</th><th style="text-align:right">Actions</th></tr></thead>
        <tbody>
            @forelse ($forms as $form)
                <tr>
                    <td><strong>{{ $form->title }}</strong> <span class="muted">/forms/{{ $form->slug }}</span></td>
                    <td>{{ count($form->fields ?? []) }}</td>
                    <td>{{ $form->submissions_count }}</td>
                    <td><span class="badge {{ $form->is_published ? 'published' : 'draft' }}">{{ $form->is_published ? 'Published' : 'Draft' }}</span></td>
                    <td style="text-align:right;white-space:nowrap">
                        <a class="btn-sm" href="{{ url('/forms/'.$form->slug) }}" target="_blank" rel="noopener">View</a>
                        <a class="btn-sm" href="{{ url('/admin/forms/'.$form->id.'/submissions') }}">Submissions</a>
                        <a class="btn-sm primary" href="{{ url('/admin/forms/'.$form->id.'/edit') }}">Edit</a>
                        <form method="POST" action="{{ url('/admin/forms/'.$form->id) }}" style="display:inline" onsubmit="return confirm('Delete this form and all submissions?')">
                            @csrf @method('DELETE')
                            <button class="btn-sm" type="submit" style="color:var(--danger)">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="empty">No forms yet — create your first admission form.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
