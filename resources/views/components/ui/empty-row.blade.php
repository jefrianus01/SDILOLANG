@props(['colspan' => 6, 'message' => 'Belum ada data.', 'description' => null])

<tr>
    <td colspan="{{ $colspan }}" class="px-4 py-12 text-center">
        <div class="mx-auto flex max-w-sm flex-col items-center">
            <span class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-100 text-gray-400">
                <x-ui.icon name="search" class="h-4 w-4" />
            </span>
            <p class="mt-3 text-sm font-medium text-gray-700">{{ $message }}</p>
            @if ($description)
                <p class="mt-1 text-xs text-gray-500">{{ $description }}</p>
            @endif
        </div>
    </td>
</tr>
