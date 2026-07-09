@props(['name' => '', 'value' => '', 'label' => 'Select Image', 'type' => 'image'])

<div x-data="{ 
    val: '{{ $value }}',
    name: '{{ $name }}',
    selectMedia() {
        window.dispatchEvent(new CustomEvent('open-media-modal', {
            detail: {
                activeTab: 'library',
                onSelect: (media) => {
                    this.val = media.url;
                    this.$dispatch('input', media.url);
                    // Trigger change if there is a native input
                    if (this.$refs.input) {
                        this.$refs.input.value = media.url;
                        this.$refs.input.dispatchEvent(new Event('input'));
                        this.$refs.input.dispatchEvent(new Event('change'));
                    }
                }
            }
        }));
    }
}" class="media-picker-wrapper flex items-center gap-3">
    
    <input type="hidden" :name="name" :value="val" x-ref="input" @input="val = $event.target.value">
    
    <!-- Thumbnail Preview -->
    <div class="w-16 h-16 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 overflow-hidden flex items-center justify-center shrink-0">
        <template x-if="val">
            <img :src="val" class="object-cover w-full h-full">
        </template>
        <template x-if="!val">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
        </template>
    </div>

    <!-- Actions -->
    <div class="flex flex-col gap-1.5">
        <div class="flex gap-2">
            <button type="button" @click="selectMedia()" class="bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-semibold py-1.5 px-3 border border-slate-300 dark:border-slate-700 rounded-lg shadow-sm transition-colors cursor-pointer">
                {{ $label }}
            </button>
            <button type="button" x-show="val" @click="val = ''; $dispatch('input', ''); if($refs.input) { $refs.input.value = ''; $refs.input.dispatchEvent(new Event('input')); }" class="bg-red-50 hover:bg-red-100 dark:bg-red-950/20 dark:hover:bg-red-950/40 text-red-600 dark:text-red-400 text-xs font-semibold py-1.5 px-3 rounded-lg transition-colors cursor-pointer">
                Clear
            </button>
        </div>
        <div x-show="val" x-text="val.split('/').pop()" class="text-[10px] text-slate-400 dark:text-slate-500 font-medium truncate max-w-[150px]"></div>
    </div>
</div>
