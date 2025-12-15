<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-white">
                {{ __('Voucher Management') }}
            </h2>
            <a href="{{ route('vouchers.create') }}" class="flex items-center gap-2 bg-white text-blue-600 hover:bg-blue-50 px-6 py-2.5 rounded-lg font-semibold shadow-lg hover:shadow-xl transition">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Create New Voucher
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center shadow-lg">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if($vouchers->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($vouchers as $voucher)
                        <div class="bg-white rounded-2xl shadow-xl overflow-hidden hover:shadow-2xl transition transform hover:-translate-y-1">
                            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-6 text-white">
                                <div class="flex items-center justify-between mb-3">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                                        <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                                    </svg>
                                    @if($voucher->ends_at && $voucher->ends_at > now())
                                        <span class="px-3 py-1 bg-green-500 text-white text-xs font-bold rounded-full">ACTIVE</span>
                                    @else
                                        <span class="px-3 py-1 bg-red-500 text-white text-xs font-bold rounded-full">EXPIRED</span>
                                    @endif
                                </div>
                                <div class="font-mono text-3xl font-bold tracking-wider mb-1">{{ $voucher->code }}</div>
                                <div class="text-blue-100 text-sm">Voucher Code</div>
                            </div>
                            
                            <div class="p-6">
                                <div class="space-y-3 mb-6">
                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-blue-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm text-gray-600">Discount</div>
                                            <div class="text-2xl font-bold text-blue-600">
                                                @if($voucher->type === 'percentage')
                                                    {{ $voucher->value }}%
                                                @else
                                                    Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <svg class="w-5 h-5 text-gray-400 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                        <div>
                                            <div class="text-sm text-gray-600">Valid Period</div>
                                            <div class="font-semibold text-gray-800">
                                                @if($voucher->starts_at || $voucher->ends_at)
                                                    {{ $voucher->starts_at ? $voucher->starts_at->format('d M Y') : '-' }} - {{ $voucher->ends_at ? $voucher->ends_at->format('d M Y') : '-' }}
                                                @else
                                                    No limit
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-3">
                                    <a href="{{ route('vouchers.edit', $voucher) }}" class="flex-1 text-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-semibold transition">
                                        Edit
                                    </a>
                                    <form action="{{ route('vouchers.destroy', $voucher) }}" method="POST" class="flex-1" onsubmit="return confirm('Delete this voucher?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2.5 rounded-lg font-semibold transition">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-8">
                    {{ $vouchers->links() }}
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-xl p-16 text-center">
                    <svg class="w-32 h-32 text-gray-300 mx-auto mb-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 5a3 3 0 015-2.236A3 3 0 0114.83 6H16a2 2 0 110 4h-5V9a1 1 0 10-2 0v1H4a2 2 0 110-4h1.17C5.06 5.687 5 5.35 5 5zm4 1V5a1 1 0 10-1 1h1zm3 0a1 1 0 10-1-1v1h1z" clip-rule="evenodd"/>
                        <path d="M9 11H3v5a2 2 0 002 2h4v-7zM11 18h4a2 2 0 002-2v-5h-6v7z"/>
                    </svg>
                    <p class="text-gray-500 text-2xl font-semibold mb-2">No vouchers yet</p>
                    <p class="text-gray-400 mb-8">Create your first voucher to offer discounts</p>
                    <a href="{{ route('vouchers.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-xl font-bold shadow-lg hover:shadow-xl transition transform hover:scale-105">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        Create First Voucher
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
