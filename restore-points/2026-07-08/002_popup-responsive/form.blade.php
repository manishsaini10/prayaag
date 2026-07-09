@extends('admin.layout')

@section('title', isset($popup) ? 'Edit Popup: ' . $popup->title : 'Create Popup')

@section('actions')
    <a href="{{ url('/admin/popup-builder') }}" class="btn-sm" style="border:1px solid var(--border)">← Back to Popups</a>
    @if(isset($popup))
        <a href="{{ url('/admin/popup-builder/' . $popup->id . '/analytics') }}" class="btn-sm" style="border:1px solid var(--border)">Analytics</a>
        <a href="{{ url('/admin/popup-builder/' . $popup->id . '/leads') }}" class="btn-sm" style="border:1px solid var(--border)">Leads</a>
    @endif
@endsection

@section('content')
<div x-data="popupEditor({{ json_encode(isset($popup) ? [
    'id' => $popup->id,
    'title' => $popup->title,
    'type' => $popup->type,
    'category_id' => $popup->category_id,
    'status' => $popup->status,
    'frequency_type' => $popup->frequency_type ?? 'once_per_session',
    'custom_css' => $popup->custom_css ?? '',
    'custom_js' => $popup->custom_js ?? '',
    'settings' => $popup->settings ?? [],
    'design' => $popup->design ?? [],
    'blocks' => $popup->structure['blocks'] ?? [],
] : []) }})" class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Left: Form --}}
    <div class="xl:col-span-2 space-y-6">
        <form method="POST" action="{{ isset($popup) ? url('/admin/popup-builder/' . $popup->id) : url('/admin/popup-builder') }}" class="space-y-6">
            @csrf
            @if(isset($popup)) @method('PUT') @endif

            @if(session('success'))
                <div class="px-4 py-3 rounded-xl text-sm" style="background:#dcfce7;color:#166534">{{ session('success') }}</div>
            @endif

            <div class="card space-y-5">
                {{-- Title --}}
                <div>
                    <label class="form-label">Popup Title</label>
                    <input type="text" name="title" x-model="data.title" required class="form-input" placeholder="e.g. Summer Admission Discount">
                    @error('title')<div class="form-err">{{ $message }}</div>@enderror
                </div>

                {{-- Type, Category, Status --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label">Type</label>
                        <select name="type" x-model="data.type" class="form-input">
                            @foreach(['modal' => 'Modal', 'floating_bar' => 'Floating Bar', 'announcement_bar' => 'Announcement Bar', 'slide_in' => 'Slide In', 'fullscreen' => 'Fullscreen'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-input">
                            <option value="">— No category —</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (old('category_id', $popup->category_id ?? '') == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" x-model="data.status" class="form-input">
                            @foreach(['draft' => 'Draft', 'active' => 'Active', 'inactive' => 'Inactive'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Content Blocks Builder --}}
            <div class="card">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-sm" style="color:var(--text)">Content Blocks</h3>
                    <div class="flex items-center gap-1.5">
                        <button @click="addBlock('text')" type="button" class="btn-sm" style="border:1px solid var(--border)">+ Text</button>
                        <button @click="addBlock('image')" type="button" class="btn-sm" style="border:1px solid var(--border)">+ Image</button>
                        <button @click="addBlock('button')" type="button" class="btn-sm" style="border:1px solid var(--border)">+ Button</button>
                        <button @click="addBlock('html')" type="button" class="btn-sm" style="border:1px solid var(--border)">+ HTML</button>
                        <button @click="addBlock('divider')" type="button" class="btn-sm" style="border:1px solid var(--border)">+ Divider</button>
                    </div>
                </div>

                <template x-if="data.blocks.length === 0">
                    <div class="empty py-8">No content blocks yet. Click "+ Text" or "+ Image" to add content.</div>
                </template>

                <div class="space-y-3">
                    <template x-for="(block, idx) in data.blocks" :key="idx">
                        <div class="block-card" :class="{ 'border-primary': dragIdx === idx }" draggable="false">
                            {{-- Block header --}}
                            <div class="block-header">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono" style="color:var(--text-muted)">#<span x-text="idx + 1"></span></span>
                                    <span class="text-xs font-semibold uppercase tracking-wider" x-text="block.type" style="color:var(--primary)"></span>
                                </div>
                                <div class="flex items-center gap-1">
                                    <button @click="moveBlock(idx, -1)" type="button" class="btn-sm" style="border:none;background:transparent" :disabled="idx === 0">↑</button>
                                    <button @click="moveBlock(idx, 1)" type="button" class="btn-sm" style="border:none;background:transparent" :disabled="idx === data.blocks.length - 1">↓</button>
                                    <button @click="removeBlock(idx)" type="button" class="btn-sm" style="border:none;background:transparent;color:var(--danger)">✕</button>
                                </div>
                            </div>

                            {{-- Text block --}}
                            <template x-if="block.type === 'text'">
                                <div class="block-body">
                                    <div class="grid grid-cols-3 gap-3 mb-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Heading</label>
                                            <input type="text" x-model="block.heading" class="form-input text-sm" placeholder="Optional heading">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Heading Size</label>
                                            <select x-model="block.heading_size" class="form-input text-sm">
                                                <option value="h2">Large (h2)</option>
                                                <option value="h3">Medium (h3)</option>
                                                <option value="h4">Small (h4)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Text Align</label>
                                            <select x-model="block.align" class="form-input text-sm">
                                                <option value="left">Left</option>
                                                <option value="center">Center</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="text-xs font-medium" style="color:var(--text-muted)">Content</label>
                                        <textarea x-model="block.content" rows="3" class="form-input text-sm" placeholder="Enter text content..."></textarea>
                                    </div>
                                </div>
                            </template>

                            {{-- Image block --}}
                            <template x-if="block.type === 'image'">
                                <div class="block-body">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Image URL</label>
                                            <div class="flex gap-2">
                                                <input type="text" x-model="block.src" class="form-input text-sm flex-1" placeholder="https://... or upload">
                                                <button @click="openUploader(block, 'image')" type="button" class="btn-sm" style="border:1px solid var(--border)">Upload</button>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Alt Text</label>
                                            <input type="text" x-model="block.alt" class="form-input text-sm" placeholder="Describe the image">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3 mt-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Width</label>
                                            <input type="text" x-model="block.width" class="form-input text-sm" placeholder="100%">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Max Width</label>
                                            <input type="text" x-model="block.max_width" class="form-input text-sm" placeholder="600px">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Border Radius</label>
                                            <input type="number" x-model="block.border_radius" class="form-input text-sm" placeholder="8">
                                        </div>
                                    </div>
                                    <template x-if="block.src">
                                        <div class="mt-3">
                                            <img :src="block.src" :alt="block.alt" style="max-height:120px;border-radius:8px" class="border">
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Video block --}}
                            <template x-if="block.type === 'video'">
                                <div class="block-body">
                                    <div>
                                        <label class="text-xs font-medium" style="color:var(--text-muted)">Video URL (YouTube, Vimeo, or direct MP4)</label>
                                        <div class="flex gap-2">
                                            <input type="text" x-model="block.src" class="form-input text-sm flex-1" placeholder="https://www.youtube.com/watch?v=...">
                                            <button @click="openUploader(block, 'video')" type="button" class="btn-sm" style="border:1px solid var(--border)">Upload</button>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 mt-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Max Width</label>
                                            <input type="text" x-model="block.max_width" class="form-input text-sm" placeholder="100%">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Autoplay</label>
                                            <select x-model="block.autoplay" class="form-input text-sm">
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- Button block --}}
                            <template x-if="block.type === 'button'">
                                <div class="block-body">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Button Text</label>
                                            <input type="text" x-model="block.text" class="form-input text-sm" placeholder="Click Here">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">URL</label>
                                            <input type="text" x-model="block.url" class="form-input text-sm" placeholder="https:// or /path">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-3 gap-3 mt-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Background</label>
                                            <input type="color" x-model="block.bg_color" class="form-input h-9 w-full">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Text Color</label>
                                            <input type="color" x-model="block.text_color" class="form-input h-9 w-full">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Full Width</label>
                                            <select x-model="block.full_width" class="form-input text-sm">
                                                <option value="0">No</option>
                                                <option value="1">Yes (Mobile)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- HTML block --}}
                            <template x-if="block.type === 'html'">
                                <div class="block-body">
                                    <label class="text-xs font-medium" style="color:var(--text-muted)">Custom HTML</label>
                                    <textarea x-model="block.html" rows="4" class="form-input text-sm font-mono" placeholder="<div>Your custom HTML here</div>"></textarea>
                                </div>
                            </template>

                            {{-- Divider block --}}
                            <template x-if="block.type === 'divider'">
                                <div class="block-body">
                                    <div class="grid grid-cols-3 gap-3">
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Color</label>
                                            <input type="color" x-model="block.color" class="form-input h-9 w-full">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Thickness (px)</label>
                                            <input type="number" x-model="block.thickness" class="form-input text-sm" placeholder="1">
                                        </div>
                                        <div>
                                            <label class="text-xs font-medium" style="color:var(--text-muted)">Margin (px)</label>
                                            <input type="number" x-model="block.margin" class="form-input text-sm" placeholder="16">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Settings --}}
            <div class="card">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--text)">Display Settings</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="form-label">Animation</label>
                        <select x-model="data.settings.animation" name="settings[animation]" class="form-input">
                            @foreach(['fade','zoom','slide_up','slide_down','bounce','pulse','none'] as $anim)
                                <option value="{{ $anim }}">{{ ucfirst(str_replace('_', ' ', $anim)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Position</label>
                        <select x-model="data.settings.position" name="settings[position]" class="form-input">
                            @foreach(['center-center' => 'Center','top-left' => 'Top Left','top-center' => 'Top Center','top-right' => 'Top Right','bottom-left' => 'Bottom Left','bottom-center' => 'Bottom Center','bottom-right' => 'Bottom Right'] as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Max Width (px)</label>
                        <input type="number" x-model.number="data.settings.width" name="settings[width]" class="form-input" min="300" max="1200">
                    </div>
                    <div>
                        <label class="form-label">Delay (seconds)</label>
                        <input type="number" x-model.number="data.settings.delay" name="settings[delay]" class="form-input" min="0" max="120" step="0.5">
                    </div>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label class="form-label">Frequency</label>
                        <select name="frequency_type" x-model="data.frequency_type" class="form-input">
                            @foreach(['once_per_session' => 'Once per session','once_per_day' => 'Once per day','weekly' => 'Weekly','monthly' => 'Monthly','once_only' => 'Once only','always' => 'Always show'] as $val => $lbl)
                                <option value="{{ $val }}">{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Overlay Opacity</label>
                        <input type="range" x-model.number="data.settings.overlay_opacity" name="settings[overlay_opacity]" min="0" max="1" step="0.1" class="w-full">
                    </div>
                    <div>
                        <label class="form-label">Background Color</label>
                        <input type="color" x-model="data.design.background" name="design[background]" class="form-input h-10 w-full">
                    </div>
                    <div>
                        <label class="form-label">Border Radius (px)</label>
                        <input type="number" x-model.number="data.design.borderRadius" name="design[borderRadius]" class="form-input" min="0" max="50">
                    </div>
                </div>
            </div>

            {{-- Custom CSS/JS --}}
            <div class="card">
                <h3 class="font-semibold text-sm mb-4" style="color:var(--text)">Custom Code</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Custom CSS</label>
                        <textarea x-model="data.custom_css" name="custom_css" rows="4" class="form-input font-mono text-xs" placeholder=".popup-content { background: red; }"></textarea>
                    </div>
                    <div>
                        <label class="form-label">Custom JavaScript</label>
                        <textarea x-model="data.custom_js" name="custom_js" rows="4" class="form-input font-mono text-xs" placeholder="console.log('Popup shown');"></textarea>
                    </div>
                </div>
            </div>

            {{-- Hidden fields --}}
            <input type="hidden" name="blocks" x-model="blocksJson">
            <input type="hidden" name="structure" x-model="structureJson">

            <div class="flex items-center gap-3 pt-4" style="border-top:1px solid var(--border)">
                <button type="submit" class="btn-primary px-8 py-3 text-base">{{ isset($popup) ? 'Save Changes' : 'Save Popup' }}</button>
                @if(isset($popup))
                    <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id . '/duplicate') }}" class="inline">
                        @csrf
                        <button type="submit" class="btn-sm" style="border:1px solid var(--border)">Duplicate</button>
                    </form>
                    @if($popup->status === 'draft')
                        <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id . '/publish') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-sm" style="border:1px solid var(--border);color:var(--primary)">Publish</button>
                        </form>
                    @else
                        <form method="POST" action="{{ url('/admin/popup-builder/' . $popup->id . '/unpublish') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-sm" style="border:1px solid var(--border);color:#f59e0b">Unpublish</button>
                        </form>
                    @endif
                @endif
            </div>
        </form>
    </div>

    {{-- Right: Live Preview --}}
    <div class="space-y-4">
        <div class="card">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-sm" style="color:var(--text)">Live Preview</h3>
                <div class="flex items-center gap-1 bg-surface-2 rounded-lg p-0.5">
                    <button @click="previewView='desktop'" :class="previewView==='desktop'?'bg-white shadow-sm':''" class="px-2.5 py-1.5 rounded-md text-xs font-medium transition" style="border:none;cursor:pointer">🖥 Desktop</button>
                    <button @click="previewView='tablet'" :class="previewView==='tablet'?'bg-white shadow-sm':''" class="px-2.5 py-1.5 rounded-md text-xs font-medium transition" style="border:none;cursor:pointer">📱 Tablet</button>
                    <button @click="previewView='mobile'" :class="previewView==='mobile'?'bg-white shadow-sm':''" class="px-2.5 py-1.5 rounded-md text-xs font-medium transition" style="border:none;cursor:pointer">📲 Mobile</button>
                </div>
            </div>
            <div class="preview-container" style="background:#f1f5f9;border-radius:12px;overflow:hidden;min-height:400px;position:relative;transition:all .3s">
                <div :style="previewView==='desktop'?'width:100%':previewView==='tablet'?'width:768px;margin:0 auto':'width:375px;margin:0 auto'" style="background:white;min-height:400px;transition:all .3s;max-width:100%;overflow-y:auto">
                    <div class="p-6" style="font-family:system-ui,sans-serif">
                        <template x-if="data.blocks.length === 0">
                            <div style="color:#94a3b8;font-size:13px;text-align:center;padding:40px 0">
                                Add content blocks to see preview
                            </div>
                        </template>
                        <template x-for="block in data.blocks" :key="block._key">
                            {{-- Text block preview --}}
                            <div x-show="block.type === 'text'" :style="{ textAlign: block.align || 'left' }" class="mb-4">
                                <template x-if="block.heading">
                                    <div :style="{ fontSize: block.heading_size === 'h2' ? '24px' : block.heading_size === 'h3' ? '20px' : '18px', fontWeight: '700', marginBottom: '8px', color: '#1e293b' }" x-text="block.heading"></div>
                                </template>
                                <template x-if="block.content">
                                    <div style="font-size:15px;line-height:1.7;color:#475569" x-text="block.content"></div>
                                </template>
                            </div>
                            {{-- Image block preview --}}
                            <div x-show="block.type === 'image'" class="mb-4" :style="{ textAlign: block.align || 'center' }">
                                <img :src="block.src || '/admin/images/placeholder.svg'" :alt="block.alt" :style="{ maxWidth: block.max_width || '100%', width: block.width || 'auto', borderRadius: (block.border_radius || 8) + 'px' }" style="height:auto">
                            </div>
                            {{-- Video block preview --}}
                            <div x-show="block.type === 'video'" class="mb-4" style="text-align:center">
                                <div x-show="block.src && block.src.includes('youtube')" style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;max-width:100%">
                                    <iframe :src="'https://www.youtube.com/embed/' + (block.src.match(/(?:v=|\/)([\w-]{11})/)?.[1] || '')" style="position:absolute;top:0;left:0;width:100%;height:100%;border:0;border-radius:8px" allowfullscreen></iframe>
                                </div>
                                <div x-show="block.src && !block.src.includes('youtube')" style="padding:20px;background:#f8fafc;border-radius:8px;color:#94a3b8;font-size:13px">
                                    🎬 Video: <span x-text="block.src"></span>
                                </div>
                            </div>
                            {{-- Button block preview --}}
                            <div x-show="block.type === 'button'" class="mb-4" style="text-align:center">
                                <a :href="block.url || '#'" :style="{ background: block.bg_color || '#6366f1', color: block.text_color || '#fff', display: block.full_width == '1' ? 'block' : 'inline-block', padding: '12px 28px', borderRadius: '8px', fontWeight: 600, fontSize: '15px', textDecoration: 'none', transition: 'all .2s' }" x-text="block.text || 'Button'"></a>
                            </div>
                            {{-- HTML block preview --}}
                            <div x-show="block.type === 'html'" class="mb-4" x-html="block.html || ''"></div>
                            {{-- Divider block preview --}}
                            <div x-show="block.type === 'divider'" class="mb-4">
                                <hr :style="{ border: 'none', borderTop: (block.thickness || 1) + 'px solid ' + (block.color || '#e5e7eb'), margin: (block.margin || 16) + 'px 0' }">
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($popup))
        <div class="card text-sm">
            <div class="font-semibold mb-2" style="color:var(--text)">Popup Stats</div>
            <div class="space-y-1.5" style="color:var(--text-muted)">
                <div class="flex justify-between"><span>Views</span><span style="color:var(--text)">{{ number_format($popup->view_count) }}</span></div>
                <div class="flex justify-between"><span>Impressions</span><span style="color:var(--text)">{{ number_format($popup->impression_count) }}</span></div>
                <div class="flex justify-between"><span>Clicks</span><span style="color:var(--text)">{{ number_format($popup->click_count) }}</span></div>
                <div class="flex justify-between"><span>Conversions</span><span style="color:var(--text)">{{ number_format($popup->conversion_count) }}</span></div>
                <div class="flex justify-between"><span>Conv. Rate</span><span style="color:var(--text)">{{ $popup->conversion_rate }}%</span></div>
            </div>
        </div>
        @endif

        {{-- Media Uploader Modal --}}
        <div x-show="uploaderOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background:rgba(0,0,0,.5)">
            <div @click.away="uploaderOpen = false" class="bg-white rounded-xl p-6 w-full max-w-md shadow-xl">
                <h3 class="font-semibold mb-4">Upload <span x-text="uploadType"></span></h3>
                <div class="border-2 border-dashed rounded-xl p-8 text-center" style="border-color:#e2e8f0">
                    <input type="file" id="uploadInput" accept="image/*,video/*" class="hidden" @change="handleUpload">
                    <button @click="document.getElementById('uploadInput').click()" type="button" class="btn-primary text-sm">Choose File</button>
                    <div class="mt-2 text-xs" style="color:var(--text-muted)">Max 10MB. JPG, PNG, WebP, GIF, SVG, MP4, WebM</div>
                </div>
                <template x-if="uploading">
                    <div class="mt-3 text-sm" style="color:var(--text-muted)">Uploading...</div>
                </template>
                <template x-if="uploadError">
                    <div class="mt-3 text-sm" style="color:var(--danger)" x-text="uploadError"></div>
                </template>
                <div class="flex justify-end mt-4">
                    <button @click="uploaderOpen = false" type="button" class="btn-sm" style="border:1px solid var(--border)">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function popupEditor(initial) {
    // Ensure settings/design have defaults
    if (!initial.settings) initial.settings = {};
    if (!initial.design) initial.design = {};
    if (!initial.blocks) initial.blocks = [];
    initial.settings = {
        animation: initial.settings.animation || 'fade',
        position: initial.settings.position || 'center-center',
        width: initial.settings.width || 700,
        delay: initial.settings.delay || 0,
        overlay_opacity: initial.settings.overlay_opacity ?? 0.5,
        z_index: initial.settings.z_index || 999999,
        close_button: initial.settings.close_button ?? true,
    };
    initial.design = {
        background: initial.design.background || '#ffffff',
        borderRadius: initial.design.borderRadius || 12,
    };

    let keyCounter = Date.now();
    const addKey = (b) => { if (!b._key) b._key = 'blk_' + (keyCounter++); return b; };

    return {
        data: initial,
        dragIdx: null,
        uploaderOpen: false,
        uploadType: 'image',
        uploadTarget: null,
        uploading: false,
        uploadError: null,
        previewView: 'desktop',

        get blocksJson() {
            return JSON.stringify(this.data.blocks);
        },
        get structureJson() {
            return JSON.stringify({ blocks: this.data.blocks });
        },
        get previewBlocks() {
            return this.data.blocks;
        },

        addBlock(type) {
            const blocks = {
                text: { _key: 'blk_' + keyCounter++, type: 'text', heading: '', heading_size: 'h2', content: '', align: 'left' },
                image: { _key: 'blk_' + keyCounter++, type: 'image', src: '', alt: '', width: '100%', max_width: '100%', border_radius: 8, align: 'center' },
                video: { _key: 'blk_' + keyCounter++, type: 'video', src: '', max_width: '100%', autoplay: '0' },
                button: { _key: 'blk_' + keyCounter++, type: 'button', text: 'Click Here', url: '#', bg_color: '#6366f1', text_color: '#ffffff', full_width: '0' },
                html: { _key: 'blk_' + keyCounter++, type: 'html', html: '' },
                divider: { _key: 'blk_' + keyCounter++, type: 'divider', color: '#e5e7eb', thickness: 1, margin: 16 },
            };
            if (blocks[type]) this.data.blocks.push(blocks[type]);
        },

        removeBlock(idx) {
            this.data.blocks.splice(idx, 1);
        },

        moveBlock(idx, dir) {
            const target = idx + dir;
            if (target < 0 || target >= this.data.blocks.length) return;
            const temp = this.data.blocks[idx];
            this.data.blocks[idx] = this.data.blocks[target];
            this.data.blocks[target] = temp;
        },

        openUploader(block, type) {
            this.uploadTarget = block;
            this.uploadType = type;
            this.uploadError = null;
            this.uploaderOpen = true;
            this.uploading = false;
        },

        async handleUpload(e) {
            const file = e.target.files[0];
            if (!file) return;
            this.uploading = true;
            this.uploadError = null;

            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', this.uploadType);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');

            try {
                const res = await fetch('{{ url("/admin/popup-builder/upload") }}', {
                    method: 'POST',
                    body: formData,
                });
                const json = await res.json();
                if (json.url) {
                    if (this.uploadTarget) {
                        if (this.uploadType === 'video') {
                            this.uploadTarget.src = json.url;
                        } else {
                            this.uploadTarget.src = json.url;
                        }
                    }
                    this.uploaderOpen = false;
                } else {
                    this.uploadError = 'Upload failed';
                }
            } catch (err) {
                this.uploadError = 'Upload error: ' + err.message;
            }
            this.uploading = false;
            e.target.value = '';
        },
    };
}
</script>

<style>
.form-label{display:block;font-size:13px;font-weight:600;margin-bottom:6px;color:var(--text)}
.form-input{width:100%;padding:9px 12px;border:1px solid var(--border);border-radius:8px;background:var(--surface);color:var(--text);font-size:14px;outline:none;transition:border-color .2s}
.form-input:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(99,102,241,.15)}
.form-err{font-size:12px;color:var(--danger);margin-top:4px}
select.form-input{appearance:auto}
textarea.form-input{resize:vertical;min-height:60px}
.block-card{border:1px solid var(--border);border-radius:10px;overflow:hidden;transition:border-color .2s}
.block-card:hover{border-color:var(--primary)}
.block-header{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;background:var(--surface-2);border-bottom:1px solid var(--border)}
.block-body{padding:12px}
</style>
@endsection
