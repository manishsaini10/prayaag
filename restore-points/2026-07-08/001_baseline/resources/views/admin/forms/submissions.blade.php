@extends('admin.layout')

@section('title', $form->title . ' — Submissions')
@section('subtitle', $form->submissions->count() . ' ' . \Illuminate\Support\Str::plural('submission', $form->submissions->count()))

@section('actions')
    <a href="{{ url('/admin/forms') }}" class="btn">← All forms</a>
    <a href="{{ url('/admin/forms/'.$form->id.'/edit') }}" class="btn">Edit form</a>
@endsection

@section('content')

@php $fields = $form->fields ?? []; @endphp

<div class="card" style="overflow:auto">
    <table>
        <thead>
            <tr>
                <th>Received</th>
                @foreach ($fields as $f)<th>{{ $f['label'] }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($form->submissions as $sub)
                <tr>
                    <td class="muted" style="white-space:nowrap">{{ $sub->created_at?->diffForHumans() }}</td>
                    @foreach ($fields as $f)
                        @php $v = data_get($sub->data, $f['key']); @endphp
                        <td>{{ is_array($v) ? implode(', ', $v) : ($v !== null && $v !== '' ? $v : '—') }}</td>
                    @endforeach
                </tr>
            @empty
                <tr><td colspan="{{ count($fields) + 1 }}" class="empty">No submissions yet. Share the form at <strong>/forms/{{ $form->slug }}</strong>.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection
