@extends('admin.layout')

@php $isEdit = $mode === 'edit'; @endphp

@section('title', $isEdit ? 'Edit form' : 'New form')
@section('subtitle', $isEdit ? $form->title : 'Build an admission / enquiry form')

@section('actions')
    <a href="{{ url('/admin/forms') }}" class="btn">← All forms</a>
    @if ($isEdit)<a href="{{ url('/forms/'.$form->slug) }}" target="_blank" rel="noopener" class="btn">View live</a>@endif
@endsection

@section('content')

<style>
    .fb{max-width:860px}
    .fb .fld{display:flex;flex-direction:column;gap:5px;margin-bottom:14px}
    .fb label{font-size:12px;font-weight:600;color:var(--text-soft)}
    .fb input[type=text],.fb input[type=number],.fb textarea,.fb select{padding:9px 12px;border:1px solid var(--border-strong);border-radius:10px;background:var(--surface);color:var(--text);font:inherit;font-size:14px;width:100%}
    .fb input:focus,.fb textarea:focus,.fb select:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
    .fb textarea{min-height:70px;resize:vertical}
    .frow{border:1px solid var(--border);border-radius:12px;padding:14px;margin-bottom:12px;background:var(--surface-2)}
    .frow .grid3{display:grid;grid-template-columns:2fr 1fr auto;gap:10px;align-items:end}
    @media(max-width:620px){.frow .grid3{grid-template-columns:1fr}}
    .chk{display:flex;align-items:center;gap:8px;font-size:13.5px;color:var(--text)}
    .chk input{width:17px;height:17px;accent-color:var(--primary)}
</style>

@if ($errors->any())
    <div class="card" style="border-color:var(--danger);background:var(--danger-soft);color:var(--danger);padding:12px 16px;margin-bottom:16px;font-size:13.5px">Please check the highlighted fields.</div>
@endif
@if (session('status'))
    <div class="card" style="border-color:var(--success);background:var(--success-soft);color:var(--success);padding:12px 16px;margin-bottom:16px;font-size:13.5px;font-weight:500">{{ session('status') }}</div>
@endif

<form class="fb" method="POST" action="{{ $isEdit ? url('/admin/forms/'.$form->id) : url('/admin/forms') }}"
      x-data="{ fields: @js(collect($form->fields ?? [])->map(fn($f) => ['label'=>$f['label']??'','type'=>$f['type']??'text','required'=>$f['required']??false,'placeholder'=>$f['placeholder']??'','options'=>implode(', ', $f['options']??[])])->values()),
                addField(){ this.fields.push({label:'',type:'text',required:false,placeholder:'',options:''}) },
                removeField(i){ this.fields.splice(i,1) } }">
    @csrf
    @if ($isEdit) @method('PUT') @endif

    <div class="card" style="padding:20px;margin-bottom:16px">
        <div class="fld"><label>Form title</label><input type="text" name="title" value="{{ old('title', $form->title) }}" placeholder="Admission Enquiry 2026-27" required>@error('title')<span style="color:var(--danger);font-size:12px">{{ $message }}</span>@enderror</div>
        <div class="fld"><label>Description <span class="muted" style="font-weight:400">(optional, shown above the form)</span></label><textarea name="description">{{ old('description', $form->description) }}</textarea></div>
        <div class="fld"><label>Success message</label><input type="text" name="success_message" value="{{ old('success_message', $form->success_message) }}" placeholder="Thank you! We'll be in touch shortly."></div>
        <label class="chk"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $isEdit ? $form->is_published : true))> Published</label>
    </div>

    <div class="card" style="padding:20px;margin-bottom:16px">
        <div class="flex items-center justify-between mb-3">
            <span class="font-semibold" style="color:var(--text)">Fields</span>
            <button type="button" class="btn-sm primary" @click="addField()"><x-admin.icon name="plus"/> Add field</button>
        </div>

        <template x-for="(field, i) in fields" :key="i">
            <div class="frow">
                <div class="grid3">
                    <div class="fld" style="margin:0"><label>Label</label><input type="text" :name="'fields['+i+'][label]'" x-model="field.label" placeholder="Student name"></div>
                    <div class="fld" style="margin:0"><label>Type</label>
                        <select :name="'fields['+i+'][type]'" x-model="field.type">
                            <option value="text">Text</option>
                            <option value="email">Email</option>
                            <option value="tel">Phone</option>
                            <option value="number">Number</option>
                            <option value="textarea">Paragraph</option>
                            <option value="select">Dropdown</option>
                            <option value="date">Date</option>
                        </select>
                    </div>
                    <button type="button" class="btn-sm" @click="removeField(i)" style="color:var(--danger);height:38px">Remove</button>
                </div>
                <div style="display:flex;gap:16px;margin-top:10px;flex-wrap:wrap;align-items:center">
                    <label class="chk"><input type="hidden" :name="'fields['+i+'][required]'" value="0"><input type="checkbox" :name="'fields['+i+'][required]'" value="1" x-model="field.required"> Required</label>
                    <input type="text" :name="'fields['+i+'][placeholder]'" x-model="field.placeholder" placeholder="Placeholder (optional)" style="flex:1;min-width:160px">
                </div>
                <div class="fld" style="margin:10px 0 0" x-show="field.type==='select'" x-cloak>
                    <label>Dropdown options <span class="muted" style="font-weight:400">(comma-separated)</span></label>
                    <input type="text" :name="'fields['+i+'][options]'" x-model="field.options" placeholder="Day Boarding, Full Boarding, Transport">
                </div>
            </div>
        </template>

        <div x-show="fields.length===0" class="empty">No fields yet — click “Add field”.</div>
    </div>

    <button type="submit" class="btn primary">{{ $isEdit ? 'Save form' : 'Create form' }}</button>
</form>

@endsection
