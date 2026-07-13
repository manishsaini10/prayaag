@extends('admin.layout')
@section('title', 'Departments')
@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 style="font-size:22px;font-weight:800;color:#0e2f5e">Departments</h1>
    <a href="{{ route('departments.create') }}" class="btn-primary px-4 py-2 rounded-lg text-sm font-bold" style="background:#0b2545;color:#fff">+ New Department</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($departments as $dept)
    <div class="premium-card p-5" style="border-left:5px solid {{ $dept->color }}">
        <div class="flex justify-between items-start">
            <div>
                <h3 style="font-size:16px;font-weight:700;color:#0e2f5e">{{ $dept->name }}</h3>
                <p style="font-size:12px;color:#64748b;margin:4px 0">{{ $dept->description }}</p>
            </div>
            <span class="text-xs font-bold px-2 py-1 rounded" style="background:{{ $dept->is_active ? '#dcfce7' : '#fee2e2' }};color:{{ $dept->is_active ? '#16a34a' : '#dc2626' }}">
                {{ $dept->is_active ? 'Active' : 'Inactive' }}
            </span>
        </div>
        <div class="flex justify-between items-center mt-4 pt-3 border-t text-xs text-gray-500">
            <span>{{ $dept->agents_count ?? 0 }} Agents</span>
            <div class="flex gap-2">
                <a href="{{ route('departments.edit', $dept) }}" class="font-semibold" style="color:#3b82f6">Edit</a>
                <form method="POST" action="{{ route('departments.destroy', $dept) }}" onsubmit="return confirm('Delete this department?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="font-semibold" style="color:#dc2626">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection