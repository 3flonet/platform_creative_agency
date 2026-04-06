<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("entangle('{$getStatePath()}')") }},
            search: '',
            icons: {{ json_encode($getIcons()) }},
            show: false,
            get filteredIcons() {
                if (this.search.length < 2) {
                    return this.icons.slice(0, 150);
                }
                let s = this.search.toLowerCase();
                return this.icons.filter(i => i.toLowerCase().includes(s)).slice(0, 150);
            }
        }"
        x-on:click.away="show = false"
        class="relative"
    >
        <!-- Trigger Input -->
        <div 
            x-on:click="show = !show"
            class="flex items-center gap-2 px-3 py-1.5 border rounded-lg cursor-pointer bg-white dark:bg-gray-800 dark:border-gray-700 shadow-sm hover:border-gray-400 dark:hover:border-gray-500 transition-colors"
            style="display: flex; align-items: center; gap: 0.5rem; padding: 0.375rem 0.75rem; min-height: 38px;"
        >
            <div x-show="state" class="flex-shrink-0" style="display: flex; align-items: center;">
                <img :src="'/vendor/uicons/svg/' + state.replace('fi ', '') + '.svg'" 
                     class="w-5 h-5" 
                     style="width: 20px; height: 20px; filter: invert(24%) sepia(91%) saturate(6654%) hue-rotate(355deg) brightness(101%) contrast(105%);"
                />
            </div>
            <div x-show="!state" class="text-xs text-gray-400" style="font-size: 0.75rem; color: #9ca3af;">
                Select icon...
            </div>
            <div x-text="state ? state.replace('fi ', '') : ''" class="flex-grow font-mono text-[10px] uppercase opacity-60 truncate" style="flex-grow: 1; font-family: monospace; font-size: 10px; opacity: 0.6; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></div>
            <div class="text-gray-400" style="color: #9ca3af; display: flex;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
            </div>
        </div>

        <!-- Popover -->
        <div
            x-show="show"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            class="absolute left-0 z-50 w-full mt-2 bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl shadow-xl overflow-hidden"
            style="display: none; position: absolute; left: 0; z-index: 50; width: 100%; margin-top: 0.5rem; background: white; border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e5e7eb;"
        >
            <!-- Search -->
            <div class="p-2 border-b dark:border-gray-700 bg-gray-50/80 dark:bg-gray-900/50" style="padding: 0.5rem; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
                <div class="relative" style="position: relative;">
                    <input 
                        x-model="search"
                        x-on:click.stop
                        type="text" 
                        placeholder="Search icons..."
                        class="w-full pl-8 pr-3 py-1.5 text-xs border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-md focus:ring-primary-500 focus:border-primary-500 shadow-sm"
                        style="width: 100%; padding: 0.375rem 0.75rem 0.375rem 2rem; font-size: 0.75rem; border: 1px solid #e5e7eb; border-radius: 0.375rem;"
                    />
                    <div class="absolute left-2.5 top-2 text-gray-400" style="position: absolute; left: 0.625rem; top: 0.5rem; color: #9ca3af;">
                        <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>
            </div>

            <!-- Grid -->
            <div class="p-1 grid grid-cols-6 sm:grid-cols-8 md:grid-cols-10 gap-0.5 max-h-64 overflow-y-auto custom-scrollbar bg-gray-50/50 dark:bg-gray-900/20" 
                 style="display: grid; grid-template-columns: repeat(10, minmax(0, 1fr)); gap: 2px; padding: 0.25rem; max-height: 16rem; overflow-y: auto; background: #f9fafb;">
                <template x-for="icon in filteredIcons" :key="icon">
                    <button
                        type="button"
                        x-on:click="state = 'fi ' + icon; show = false; search = ''"
                        class="p-1.5 flex items-center justify-center rounded-md hover:bg-white dark:hover:bg-gray-700 hover:shadow-sm group transition-all border border-transparent hover:border-gray-200 dark:hover:border-gray-600"
                        style="padding: 0.375rem; display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; border: 1px solid transparent;"
                        :title="icon"
                    >
                        <img :src="'/vendor/uicons/svg/' + icon + '.svg'" 
                             class="w-5 h-5 group-hover:scale-110 transition-transform opacity-80 group-hover:opacity-100" 
                             style="width: 20px; height: 20px; filter: invert(24%) sepia(91%) saturate(6654%) hue-rotate(355deg) brightness(101%) contrast(105%); opacity: 0.8;"
                        />
                    </button>
                </template>
            </div>

            <!-- Footer -->
            <div class="p-2 border-t dark:border-gray-700 text-[10px] text-gray-400 text-center">
                Showing top results. Use search for more.
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.05);
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.1);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.2);
        }
    </style>
</x-dynamic-component>
