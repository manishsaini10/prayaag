<div x-data="adminMediaModal()"
     x-show="isOpen"
     x-cloak
     @open-media-modal.window="open($event.detail)"
     @keydown.escape.window="close()"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
     role="dialog"
     aria-modal="true"
     aria-label="Media Library Modal">

    <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl flex flex-col w-full max-w-6xl h-[85vh] overflow-hidden border border-slate-200 dark:border-slate-800"
         @click.away="close()">
        
        <!-- Modal Header -->
        <header class="flex justify-between items-center px-6 py-4 border-b border-slate-100 dark:border-slate-800 shrink-0">
            <h2 class="text-lg font-bold text-slate-800 dark:text-slate-100">Media Library</h2>
            <button @click="close()" class="p-1 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </header>

        <div class="flex flex-1 overflow-hidden">
            <!-- Sidebar: Tabs -->
            <nav class="w-48 bg-slate-50 dark:bg-slate-950 border-r border-slate-100 dark:border-slate-800 flex flex-col p-4 gap-1 shrink-0">
                <button @click="activeTab = 'upload'"
                        :class="activeTab === 'upload' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full text-left px-3 py-2.5 rounded-lg text-sm transition-colors">
                    Upload Files
                </button>
                <button @click="activeTab = 'library'"
                        :class="activeTab === 'library' ? 'bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-semibold' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800/50'"
                        class="w-full text-left px-3 py-2.5 rounded-lg text-sm transition-colors">
                    Media Library
                </button>
                <div class="mt-auto p-2 text-[11px] text-slate-400 dark:text-slate-500 border-t border-slate-100 dark:border-slate-800">
                    <span class="block font-medium">Max Upload Size:</span>
                    <span x-text="maxUploadSize" class="font-bold">20 MB</span>
                </div>
            </nav>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col overflow-hidden bg-white dark:bg-slate-900">
                
                <!-- Tab: Upload Files -->
                <div x-show="activeTab === 'upload'" class="flex-1 flex flex-col items-center justify-center p-8 overflow-y-auto" x-cloak>
                    <div @dragover.prevent="dragOver = true"
                         @dragleave.prevent="dragOver = false"
                         @drop.prevent="handleDrop($event)"
                         :class="dragOver ? 'border-indigo-500 bg-indigo-50/20 dark:bg-indigo-950/10' : 'border-slate-300 dark:border-slate-700'"
                         class="border-2 border-dashed rounded-xl p-12 text-center w-full max-w-lg transition-all flex flex-col items-center justify-center">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-indigo-500 mb-4 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                        </svg>
                        <h3 class="text-base font-bold text-slate-800 dark:text-slate-100 mb-1">Drag and drop files here</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">or</p>
                        <input type="file" id="mediaModalInput" multiple class="hidden" @change="handleFileInput($event)">
                        <button @click="document.getElementById('mediaModalInput').click()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg shadow-sm transition-colors">
                            Select Files
                        </button>
                        
                        <div class="mt-4 text-xs text-slate-400 dark:text-slate-500">
                            Supported: JPG, PNG, WebP, GIF, SVG (up to <span x-text="maxUploadSize"></span>)
                        </div>
                    </div>

                    <!-- Upload Queue / Progress -->
                    <div x-show="uploadsQueue.length > 0" class="w-full max-w-lg mt-6 border border-slate-100 dark:border-slate-800 rounded-lg p-4 bg-slate-50 dark:bg-slate-950/40">
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Uploading Queue</h4>
                        <div class="space-y-3">
                            <template x-for="up in uploadsQueue" :key="up.id">
                                <div>
                                    <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                        <span x-text="up.name" class="truncate max-w-[70%]"></span>
                                        <span x-text="up.error ? 'Error' : (up.progress + '%')"></span>
                                    </div>
                                    <div class="w-full bg-slate-200 dark:bg-slate-800 h-1.5 rounded-full overflow-hidden">
                                        <div :class="up.error ? 'bg-red-500' : 'bg-indigo-600'" :style="{ width: up.progress + '%' }" class="h-full rounded-full transition-all"></div>
                                    </div>
                                    <template x-if="up.error">
                                        <div x-text="up.error" class="text-[10px] text-red-500 mt-0.5"></div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Tab: Media Library -->
                <div x-show="activeTab === 'library'" class="flex-1 flex overflow-hidden" x-cloak>
                    <!-- Gallery Grid & Toolbar -->
                    <div class="flex-1 flex flex-col overflow-hidden">
                        
                        <!-- Toolbar -->
                        <div class="p-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/20 shrink-0 grid grid-cols-1 md:grid-cols-4 gap-3">
                            <!-- Search -->
                            <div class="relative">
                                <input type="text" x-model="filters.q" @input.debounce.300ms="fetchMedia(1)" placeholder="Search media..." class="w-full pl-9 pr-4 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-slate-400 absolute left-3 top-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </div>

                            <!-- Filter by Type -->
                            <select x-model="filters.type" @change="fetchMedia(1)" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500 transition-colors">
                                <option value="">All Types</option>
                                <option value="image">Images</option>
                                <option value="video">Videos</option>
                                <option value="document">Documents</option>
                            </select>

                            <!-- Filter by Size -->
                            <select x-model="filters.size" @change="fetchMedia(1)" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500 transition-colors">
                                <option value="">All Sizes</option>
                                <option value="small">Small (< 100 KB)</option>
                                <option value="medium">Medium (100 KB - 1 MB)</option>
                                <option value="large">Large (> 1 MB)</option>
                            </select>

                            <!-- Sort -->
                            <select x-model="filters.sort" @change="fetchMedia(1)" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-sm text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500 transition-colors">
                                <option value="date_desc">Newest First</option>
                                <option value="date_asc">Oldest First</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                                <option value="size_desc">Size (Largest)</option>
                            </select>
                        </div>

                        <!-- Grid -->
                        <div class="flex-1 overflow-y-auto p-4" @scroll="handleScroll($event)">
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                                <template x-for="item in items" :key="item.id">
                                    <div @click="selectItem(item)"
                                         :class="selectedItem && selectedItem.id === item.id ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50/10' : 'border-slate-200 dark:border-slate-800 hover:border-slate-400 dark:hover:border-slate-600'"
                                         class="border rounded-lg overflow-hidden cursor-pointer relative flex flex-col group bg-slate-50 dark:bg-slate-950/30 transition-all select-none">
                                        
                                        <!-- Thumbnail wrapper -->
                                        <div class="aspect-square w-full relative flex items-center justify-center overflow-hidden bg-slate-100 dark:bg-slate-950/50">
                                            <template x-if="item.mime_type.startsWith('image/')">
                                                <img :src="item.thumb_url" alt="" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-300" loading="lazy">
                                            </template>
                                            <template x-if="!item.mime_type.startsWith('image/')">
                                                <div class="flex flex-col items-center gap-1.5 p-3">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                                    <span x-text="item.extension" class="text-[10px] font-bold text-slate-500 uppercase tracking-widest bg-slate-200 dark:bg-slate-800 px-1.5 py-0.5 rounded"></span>
                                                </div>
                                            </template>
                                            
                                            <!-- Checkmark Overlay -->
                                            <div x-show="selectedItem && selectedItem.id === item.id" class="absolute top-1.5 right-1.5 bg-indigo-600 text-white rounded-full p-0.5 shadow-md">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                            </div>
                                        </div>
                                        
                                        <!-- Filename label -->
                                        <div class="p-2 border-t border-slate-100 dark:border-slate-800/80 bg-white dark:bg-slate-900 shrink-0 text-center">
                                            <div x-text="item.original_name" class="text-xs text-slate-700 dark:text-slate-300 font-medium truncate max-w-full"></div>
                                            <div x-show="item.width" x-text="item.width + ' × ' + item.height" class="text-[10px] text-slate-400 mt-0.5"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Empty / Loading States -->
                            <div x-show="items.length === 0 && !loading" class="text-center py-12 text-slate-500 dark:text-slate-400">
                                No media files found matching the criteria.
                            </div>
                            <div x-show="loading" class="text-center py-6 text-slate-500 dark:text-slate-400 flex items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Loading media library...
                            </div>
                        </div>
                    </div>

                    <!-- Details Sidebar Panel -->
                    <aside class="w-80 bg-slate-50 dark:bg-slate-950 border-l border-slate-100 dark:border-slate-800 flex flex-col overflow-y-auto shrink-0 select-none">
                        <template x-if="!selectedItem">
                            <div class="flex-1 flex items-center justify-center p-6 text-center text-slate-400 dark:text-slate-500 text-sm">
                                Select an item to view its details and edit metadata.
                            </div>
                        </template>

                        <template x-if="selectedItem">
                            <div class="p-5 flex flex-col gap-5 text-xs text-slate-600 dark:text-slate-400">
                                <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 uppercase tracking-wider border-b border-slate-200 dark:border-slate-800 pb-2">Attachment Details</h3>
                                
                                <!-- File Preview Card -->
                                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-2 flex gap-3 items-center">
                                    <div class="w-16 h-16 rounded bg-slate-100 dark:bg-slate-950 overflow-hidden flex-shrink-0 flex items-center justify-center border border-slate-100 dark:border-slate-800">
                                        <template x-if="selectedItem.mime_type.startsWith('image/')">
                                            <img :src="selectedItem.thumb_url" alt="" class="object-cover w-full h-full">
                                        </template>
                                        <template x-if="!selectedItem.mime_type.startsWith('image/')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                        </template>
                                    </div>
                                    <div class="min-w-0">
                                        <div x-text="selectedItem.original_name" class="font-bold text-slate-800 dark:text-slate-200 truncate" :title="selectedItem.original_name"></div>
                                        <div x-text="selectedItem.extension.toUpperCase() + ' File'" class="text-[10px] text-slate-400 uppercase tracking-wider mt-0.5"></div>
                                        <div x-text="formatBytes(selectedItem.size)" class="text-[10px] text-slate-500 font-semibold mt-0.5"></div>
                                    </div>
                                </div>

                                <!-- Technical Details List -->
                                <div class="space-y-1.5 border-b border-slate-200 dark:border-slate-800 pb-3">
                                    <div class="flex justify-between"><span class="font-medium text-slate-400">Uploaded on:</span><span x-text="formatDate(selectedItem.created_at)" class="font-semibold text-slate-700 dark:text-slate-300"></span></div>
                                    <template x-if="selectedItem.width">
                                        <div class="flex justify-between"><span class="font-medium text-slate-400">Dimensions:</span><span x-text="selectedItem.width + ' × ' + selectedItem.height + ' px'" class="font-semibold text-slate-700 dark:text-slate-300"></span></div>
                                    </template>
                                    <div class="flex justify-between truncate min-w-0">
                                        <span class="font-medium text-slate-400">File URL:</span>
                                        <a :href="selectedItem.url" target="_blank" class="font-semibold text-indigo-600 dark:text-indigo-400 hover:underline truncate max-w-[65%]">View Original</a>
                                    </div>
                                </div>

                                <!-- Edit Fields -->
                                <div class="space-y-4">
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Title</label>
                                        <input type="text" x-model="selectedItem.title" @input="isDirty = true" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Alternative Text (Alt)</label>
                                        <input type="text" x-model="selectedItem.alt" @input="isDirty = true" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Caption</label>
                                        <textarea x-model="selectedItem.caption" @input="isDirty = true" rows="2" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500 resize-none"></textarea>
                                    </div>
                                    <div>
                                        <label class="block font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">Description</label>
                                        <textarea x-model="selectedItem.description" @input="isDirty = true" rows="2" class="w-full px-3 py-2 border border-slate-300 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 outline-none focus:border-indigo-500 resize-none"></textarea>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <button @click="saveMetadata()" :disabled="!isDirty || saving" class="flex-1 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-semibold py-2 px-3 rounded-lg shadow-sm transition-colors text-center cursor-pointer">
                                            <span x-text="saving ? 'Saving...' : 'Save Metadata'"></span>
                                        </button>
                                        <button @click="document.getElementById('replaceInput').click()" :disabled="saving" class="bg-slate-200 dark:bg-slate-800 hover:bg-slate-300 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-semibold py-2 px-3 rounded-lg shadow-sm transition-colors cursor-pointer" title="Replace File">
                                            Replace
                                        </button>
                                        <input type="file" id="replaceInput" class="hidden" @change="replaceFile($event)">
                                    </div>
                                </div>

                                <!-- Danger Zone -->
                                <div class="border-t border-slate-200 dark:border-slate-800 pt-4 mt-1">
                                    <button @click="deleteItem()" class="w-full bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-600 dark:text-red-400 font-semibold py-2 px-3 rounded-lg transition-colors cursor-pointer text-center">
                                        Delete Permanently
                                    </button>
                                </div>
                            </div>
                        </template>
                    </aside>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="flex justify-between items-center px-6 py-4 border-t border-slate-100 dark:border-slate-800 shrink-0 bg-slate-50 dark:bg-slate-950/40">
            <div class="text-xs text-slate-500 dark:text-slate-400">
                <template x-if="selectedItem">
                    <span>Selected: <strong class="text-slate-700 dark:text-slate-300" x-text="selectedItem.original_name"></strong></span>
                </template>
                <template x-if="!selectedItem">
                    <span>No item selected</span>
                </template>
            </div>
            <div class="flex gap-2">
                <button @click="close()" class="bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold px-4 py-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                    Cancel
                </button>
                <button @click="insertSelected()" :disabled="!selectedItem" class="bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-semibold px-5 py-2 rounded-lg shadow-sm transition-colors">
                    Insert Media
                </button>
            </div>
        </footer>
    </div>
</div>

<script>
function adminMediaModal() {
    return {
        isOpen: false,
        activeTab: 'library',
        maxUploadSize: '20 MB',
        maxUploadSizeBytes: 20971520,
        dragOver: false,
        loading: false,
        saving: false,
        isDirty: false,
        onSelectCallback: null,
        
        // Search & Pagination state
        items: [],
        currentPage: 1,
        lastPage: 1,
        hasMore: false,
        
        filters: {
            q: '',
            type: '',
            size: '',
            sort: 'date_desc'
        },
        
        selectedItem: null,
        uploadsQueue: [],
        
        open(detail) {
            this.isOpen = true;
            this.onSelectCallback = detail.onSelect || null;
            this.selectedItem = null;
            this.activeTab = detail.activeTab || 'library';
            this.uploadsQueue = [];
            this.fetchMedia(1);
        },
        
        close() {
            this.isOpen = false;
        },
        
        async fetchMedia(page = 1) {
            this.loading = true;
            try {
                const params = new URLSearchParams({
                    page: page,
                    q: this.filters.q,
                    type: this.filters.type,
                    size: this.filters.size,
                    sort: this.filters.sort
                });
                const res = await fetch(`/admin/media-library/api?${params}`);
                const data = await res.json();
                
                if (page === 1) {
                    this.items = data.items;
                } else {
                    this.items = [...this.items, ...data.items];
                }
                
                this.currentPage = data.current_page;
                this.lastPage = data.last_page;
                this.hasMore = data.has_more;
                this.maxUploadSize = data.max_upload_size;
                this.maxUploadSizeBytes = data.max_upload_size_bytes;
            } catch (err) {
                console.error('Error fetching media:', err);
            }
            this.loading = false;
        },
        
        handleScroll(e) {
            const el = e.target;
            if (el.scrollHeight - el.scrollTop - el.clientHeight < 30 && this.hasMore && !this.loading) {
                this.fetchMedia(this.currentPage + 1);
            }
        },
        
        selectItem(item) {
            this.selectedItem = item;
            this.isDirty = false;
        },
        
        async saveMetadata() {
            if (!this.selectedItem) return;
            this.saving = true;
            try {
                const res = await fetch(`/admin/media-library/api/${this.selectedItem.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    },
                    body: JSON.stringify({
                        title: this.selectedItem.title,
                        alt: this.selectedItem.alt,
                        caption: this.selectedItem.caption,
                        description: this.selectedItem.description
                    })
                });
                const data = await res.json();
                if (data.success) {
                    this.isDirty = false;
                    // Update in main list
                    const idx = this.items.findIndex(i => i.id === this.selectedItem.id);
                    if (idx !== -1) {
                        this.items[idx] = data.media;
                    }
                }
            } catch (err) {
                console.error('Error saving metadata:', err);
            }
            this.saving = false;
        },
        
        async deleteItem() {
            if (!this.selectedItem) return;
            
            // Check usage first
            try {
                const usageRes = await fetch(`/admin/media-library/api/${this.selectedItem.id}/usage`);
                const usageData = await usageRes.json();
                
                if (usageData.in_use) {
                    let alertMsg = 'This image is currently in use in:\n';
                    if (usageData.usage.popups) alertMsg += `- Popups (${usageData.usage.popups.length} references)\n`;
                    if (usageData.usage.posts) alertMsg += `- Posts (${usageData.usage.posts.length} references)\n`;
                    if (usageData.usage.pages) alertMsg += `- Pages (${usageData.usage.pages.length} references)\n`;
                    if (usageData.usage.galleries) alertMsg += `- Galleries (${usageData.usage.galleries.length} references)\n`;
                    if (usageData.usage.sliders) alertMsg += `- Sliders (${usageData.usage.sliders.length} references)\n`;
                    if (usageData.usage.testimonials) alertMsg += `- Testimonials (${usageData.usage.testimonials.length} references)\n`;
                    if (usageData.usage.downloads) alertMsg += `- Downloads (${usageData.usage.downloads.length} references)\n`;
                    
                    alertMsg += '\nAre you sure you want to delete this media? This will break references!';
                    if (!confirm(alertMsg)) return;
                } else {
                    if (!confirm('Are you sure you want to delete this item permanently?')) return;
                }
                
                const res = await fetch(`/admin/media-library/api/${this.selectedItem.id}?force=1`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const deletedId = this.selectedItem.id;
                    this.selectedItem = null;
                    this.items = this.items.filter(i => i.id !== deletedId);
                }
            } catch (err) {
                console.error('Error deleting item:', err);
            }
        },
        
        async replaceFile(e) {
            const file = e.target.files[0];
            if (!file || !this.selectedItem) return;
            
            this.saving = true;
            const formData = new FormData();
            formData.append('file', file);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
            
            try {
                const res = await fetch(`/admin/media-library/api/${this.selectedItem.id}/replace`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    this.selectedItem = data.media;
                    const idx = this.items.findIndex(i => i.id === data.media.id);
                    if (idx !== -1) {
                        this.items[idx] = data.media;
                    }
                }
            } catch (err) {
                console.error('Error replacing file:', err);
            }
            this.saving = false;
            e.target.value = '';
        },
        
        insertSelected() {
            if (this.selectedItem && this.onSelectCallback) {
                this.onSelectCallback(this.selectedItem);
                this.close();
            }
        },
        
        // Upload Handlers
        handleDrop(e) {
            this.dragOver = false;
            const files = e.dataTransfer.files;
            if (files && files.length > 0) {
                this.uploadFiles(files);
            }
        },
        
        handleFileInput(e) {
            const files = e.target.files;
            if (files && files.length > 0) {
                this.uploadFiles(files);
            }
        },
        
        async uploadFiles(files) {
            this.activeTab = 'upload';
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.size > this.maxUploadSizeBytes) {
                    alert(`File ${file.name} exceeds max upload limit of ${this.maxUploadSize}`);
                    continue;
                }
                
                const queueId = 'up_' + Math.random().toString(36).substr(2, 9);
                this.uploadsQueue.push({
                    id: queueId,
                    name: file.name,
                    progress: 0,
                    error: null
                });
                
                this.performUpload(file, queueId);
            }
        },
        
        performUpload(file, queueId) {
            const xhr = new XMLHttpRequest();
            const formData = new FormData();
            formData.append('files[]', file); // API expects files array
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
            
            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percent = Math.round((e.loaded / e.total) * 100);
                    const qIdx = this.uploadsQueue.findIndex(q => q.id === queueId);
                    if (qIdx !== -1) {
                        this.uploadsQueue[qIdx].progress = percent;
                    }
                }
            });
            
            xhr.addEventListener('load', () => {
                const qIdx = this.uploadsQueue.findIndex(q => q.id === queueId);
                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        const res = JSON.parse(xhr.responseText);
                        if (res.success && res.media && res.media.length > 0) {
                            if (qIdx !== -1) {
                                this.uploadsQueue[qIdx].progress = 100;
                            }
                            // Auto select the newly uploaded file and switch to library tab
                            setTimeout(() => {
                                // Refresh library list and select this item
                                this.fetchMedia(1).then(() => {
                                    const uploadedItem = res.media[0];
                                    const found = this.items.find(i => i.id === uploadedItem.id);
                                    if (found) {
                                        this.selectItem(found);
                                    } else {
                                        this.selectItem(uploadedItem);
                                    }
                                    this.activeTab = 'library';
                                    // Remove from queue
                                    this.uploadsQueue = this.uploadsQueue.filter(q => q.id !== queueId);
                                });
                            }, 500);
                        } else {
                            if (qIdx !== -1) this.uploadsQueue[qIdx].error = 'Upload failed';
                        }
                    } catch (e) {
                        if (qIdx !== -1) this.uploadsQueue[qIdx].error = 'Parse error';
                    }
                } else {
                    if (qIdx !== -1) this.uploadsQueue[qIdx].error = `Error: ${xhr.status}`;
                }
            });
            
            xhr.addEventListener('error', () => {
                const qIdx = this.uploadsQueue.findIndex(q => q.id === queueId);
                if (qIdx !== -1) this.uploadsQueue[qIdx].error = 'Network error';
            });
            
            xhr.open('POST', '/admin/media-library/api/upload');
            xhr.send(formData);
        },
        
        // Utils
        formatBytes(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
        },
        
        formatDate(dateStr) {
            if (!dateStr) return '';
            const d = new Date(dateStr);
            return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        }
    };
}
</script>
