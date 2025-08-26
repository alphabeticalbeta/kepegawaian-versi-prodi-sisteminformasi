{{-- Header Section --}}
<div class="bg-gradient-to-r from-blue-600 to-indigo-700 border-b shadow-lg">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="py-6 flex flex-wrap gap-4 justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-white">
                    {{ $config['title'] }}
                </h1>
                <p class="mt-1 text-blue-100">
                    {{ $config['description'] }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ url()->previous() }}"
                   class="px-4 py-2 text-blue-700 bg-white border border-white rounded-lg hover:bg-blue-50 transition-colors shadow-sm">
                    <i data-lucide="arrow-left" class="w-4 h-4 inline mr-2"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</div>
