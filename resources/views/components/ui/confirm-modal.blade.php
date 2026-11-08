@props(['id' => 'confirm-modal'])

<div id="{{ $id }}" data-confirm-modal
     class="fixed inset-0 z-[80] hidden items-center justify-center p-4"
     aria-hidden="true">
    <div data-confirm-backdrop class="absolute inset-0 bg-ink/45 backdrop-blur-[2px]"></div>

    <div data-confirm-panel
         class="confirm-panel relative w-full max-w-md overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-float"
         role="dialog" aria-modal="true"
         aria-labelledby="{{ $id }}-title" aria-describedby="{{ $id }}-message"
         tabindex="-1">
        <div class="flex items-start gap-4 px-6 pt-6">
            <span data-confirm-icon class="confirm-icon confirm-icon--primary">
                <x-ui.icon name="alert" data-confirm-icon-alert class="h-5 w-5" />
                <x-ui.icon name="info" data-confirm-icon-info class="hidden h-5 w-5" />
            </span>

            <div class="min-w-0 flex-1">
                <h2 id="{{ $id }}-title" data-confirm-title class="text-base font-semibold text-gray-900">
                    Konfirmasi tindakan
                </h2>
                <p id="{{ $id }}-message" data-confirm-message class="mt-1.5 text-sm leading-6 text-gray-600"></p>
            </div>

            <button type="button" data-confirm-cancel
                    class="-mr-1 -mt-1 rounded-lg p-1.5 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500/40"
                    aria-label="Tutup dialog">
                <x-ui.icon name="x" class="h-4 w-4" />
            </button>
        </div>

        <div class="mt-6 flex flex-col gap-2 border-t border-gray-100 bg-gray-50/60 px-6 py-4 sm:flex-row sm:justify-end">
            <button type="button" data-confirm-cancel class="btn btn-secondary w-full sm:w-auto">
                Batal
            </button>
            <button type="button" data-confirm-accept class="btn btn-primary w-full sm:w-auto">
                Ya, lanjutkan
            </button>
        </div>
    </div>
</div>
