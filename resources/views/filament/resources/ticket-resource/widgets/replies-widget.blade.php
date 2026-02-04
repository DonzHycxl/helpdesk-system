<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Replies
        </x-slot>

        <div class="space-y-4">
            @forelse($record->replies()->latest()->get() as $reply)
                <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                    <div class="flex justify-between items-start mb-2">
                        <div class="font-semibold text-sm text-gray-900 dark:text-gray-100">
                            {{ $reply->user->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $reply->created_at->diffForHumans() }}
                        </div>
                    </div>
                    <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                        {{ $reply->content }}
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 dark:text-gray-400 py-8">
                    No replies yet
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
