<x-filament-widgets::widget>
    <x-filament::section>
        <div
            x-data="{
                time: new Date(),
                init() {
                    setInterval(() => {
                        this.time = new Date();
                    }, 1000);
                },
                get dayName() {
                    return this.time.toLocaleDateString('en-US', { weekday: 'long' });
                },
                get formattedDate() {
                    const day = this.time.getDate();
                    const month = this.time.getMonth() + 1;
                    const year = this.time.getFullYear();
                    const timeStr = this.time.toLocaleTimeString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true });
                    return `${day}/${month}/${year} ${timeStr}`;
                }
            }"
            class="flex flex-col items-center justify-center space-y-2 py-4"
        >
            {{-- Header with Icon and fixed "CURRENT DATE & TIME" label --}}
            <div class="flex items-center space-x-2 text-gray-500 dark:text-gray-400">
                <x-heroicon-o-clock class="w-5 h-5"/>
                <span class="text-sm font-medium uppercase tracking-wider">CURRENT DATE & TIME</span>
            </div>

            {{-- Dynamic Day of the Week --}}
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400" x-text="dayName"></div>

            {{-- Dynamic Formatted Date & Time --}}
            <div class="text-3xl font-bold text-primary-600 dark:text-primary-400" x-text="formattedDate"></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
