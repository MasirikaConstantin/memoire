<div>
    @if($show)
        <div 
            x-data="{ show: true }" 
            x-init="
                $el.addEventListener('notification-shown', () => show = true);
                $el.addEventListener('start-hide-timeout', () => {
                    setTimeout(() => show = false, 3000);
                });
            "
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-4"
            @click.away="show = false"
            class="fixed bottom-4 right-4 z-50"
        >
            <div class="{{ $type === 'success' ? 'bg-green-500' : 'bg-red-500' }} text-white px-4 py-3 rounded shadow-lg">
                {{ $message }}
            </div>
        </div>
    @endif
</div>