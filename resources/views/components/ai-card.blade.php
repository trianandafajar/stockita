@props([
    'id',
    'endpoint',
    'title',
    'subtitle' => '',
    'icon'     => 'sparkles',
    'accent'   => 'green',
])

@php
    $accentRing = match ($accent) {
        'amber'  => 'border-amber-200',
        'sky'    => 'border-sky-200',
        'rose'   => 'border-rose-200',
        default  => 'border-primary-200',
    };

    $iconGradient = match ($accent) {
        'amber'  => 'from-amber-500 to-orange-500',
        'sky'    => 'from-sky-500 to-blue-600',
        'rose'   => 'from-rose-500 to-pink-600',
        default  => 'from-primary-500 to-primary-600',
    };
@endphp

<section id="card-{{ $id }}"
         data-ai-card="{{ $id }}"
         data-endpoint="{{ $endpoint }}"
         class="bg-white rounded-2xl border {{ $accentRing }} shadow-sm overflow-hidden">

    <header class="flex items-center justify-between gap-3 px-6 py-4 border-b border-gray-100">
        <div class="flex items-center gap-3">
            <div class="size-10 rounded-xl bg-gradient-to-br {{ $iconGradient }} text-white flex items-center justify-center">
                <i data-lucide="{{ $icon }}" class="size-5"></i>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-800">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="text-xs text-gray-500">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span data-stamp class="text-xs text-gray-400 hidden md:inline"></span>

            <button type="button"
                    data-action="refresh"
                    title="Regenerate (bypass storage)"
                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-600 disabled:opacity-50 transition">
                <i data-lucide="refresh-cw" data-card-refresh class="size-4"></i>
            </button>
        </div>
    </header>

    <div data-output class="p-6 min-h-[200px]">
        <div class="flex flex-col items-center justify-center text-center text-gray-400 py-8">
            <div class="size-10 rounded-full border-4 border-gray-200 border-t-primary-500 animate-spin mb-3"></div>
            <p class="text-sm">Loading insights…</p>
        </div>
    </div>
</section>
