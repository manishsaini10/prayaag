@extends('admin.layout')
@section('title', isset($department) ? 'Edit Department' : 'New Department')
@section('content')
<div style="max-width:600px;margin:0 auto">
    <h1 style="font-size:22px;font-weight:800;color:#0e2f5e;margin-bottom:24px">
        {{ isset($department) ? 'Edit Department' : 'New Department' }}
    </h1>
    <form method="POST" action="{{ isset($department) ? route('departments.update', $department) : route('departments.store') }}" class="premium-card p-6">
        @csrf
        @if(isset($department)) @method('PUT') @endif
        <div class="space-y-4">
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Name</label>
                <input name="name" value="{{ old('name', $department->name ?? '') }}" required class="w-full px-3 py-2 border rounded-lg text-sm" style="border-color:#e2e8f0">
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Description</label>
                <textarea name="description" rows="3" class="w-full px-3 py-2 border rounded-lg text-sm" style="border-color:#e2e8f0">{{ old('description', $department->description ?? '') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Color</label>
                    <input type="color" name="color" value="{{ old('color', $department->color ?? '#6366f1') }}" class="w-full h-10 rounded-lg border" style="border-color:#e2e8f0">
                </div>
                <div>
                    <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Priority</label>
                    <select name="priority" class="w-full px-3 py-2 border rounded-lg text-sm" style="border-color:#e2e8f0">
                        @foreach(['low','medium','high'] as $p)
                            <option value="{{ $p }}" {{ (old('priority', $department->priority ?? 'medium') === $p) ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="text-xs font-bold uppercase tracking-wider text-gray-500">Email (for notifications)</label>
                <input type="email" name="email" value="{{ old('email', $department->email ?? '') }}" class="w-full px-3 py-2 border rounded-lg text-sm" style="border-color:#e2e8f0">
            </div>
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $department->is_active ?? true) ? 'checked' : '' }} class="w-4 h-4">
                <label class="text-sm font-medium text-gray-700">Active</label>
            </div>
        </div>
        <div class="flex gap-3 mt-6 pt-4 border-t">
            <button type="submit" class="px-6 py-2 rounded-lg text-sm font-bold text-white" style="background:#0b2545">{{ isset($department) ? 'Update' : 'Create' }}</button>
            <a href="{{ route('departments.index') }}" class="px-6 py-2 rounded-lg text-sm font-bold text-gray-600 border" style="border-color:#e2e8f0">Cancel</a>
        </div>
    </form>
</div>
@endsection