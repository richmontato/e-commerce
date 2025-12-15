<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8">
                    <!-- Product Image -->
                    <div>
                        @if($product->image_url)
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full rounded-lg">
                        @else
                            <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                                <span class="text-gray-400 text-xl">No Image</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div>
                        <h1 class="text-3xl font-bold mb-4">{{ $product->name }}</h1>
                        
                        @if($product->category)
                            <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm text-gray-700 mb-4">
                                {{ $product->category }}
                            </span>
                        @endif

                        <div class="text-4xl font-bold text-indigo-600 mb-4">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>

                        @if($product->reviewsCount() > 0)
                            <div class="flex items-center mb-4">
                                <div class="flex text-yellow-400 text-xl">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $product->averageRating())
                                            ★
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-gray-600">
                                    {{ number_format($product->averageRating(), 1) }} ({{ $product->reviewsCount() }} reviews)
                                </span>
                            </div>
                        @endif

                        <div class="mb-6">
                            <h3 class="font-semibold mb-2">Description:</h3>
                            <p class="text-gray-700">{{ $product->description }}</p>
                        </div>

                        <div class="mb-6">
                            <p class="text-gray-600">Stock: <span class="font-semibold">{{ $product->stock }}</span></p>
                            <p class="text-gray-600">Seller: <span class="font-semibold">{{ $product->seller->name }}</span></p>
                        </div>

                        @auth
                            @if(auth()->user()?->isAdmin())
                                <p class="text-gray-600">Administrators manage the catalog and cannot purchase items.</p>
                            @elseif($product->stock > 0)
                                <form action="{{ route('cart.store') }}" method="POST" class="space-y-4">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    
                                    <div class="flex items-center space-x-4">
                                        <label class="font-semibold">Quantity:</label>
                                        <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}" 
                                               class="border-gray-300 rounded-md w-20">
                                    </div>

                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-8 py-3 rounded-lg">
                                        Add to Cart
                                    </button>
                                </form>
                            @else
                                <p class="text-red-600 font-semibold">Out of Stock</p>
                            @endif
                        @else
                            <p class="text-gray-600">Please <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">login</a> to add to cart</p>
                        @endauth
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="border-t p-8">
                    <h2 class="text-2xl font-bold mb-6">Customer Reviews</h2>

                    @auth
                        @php
                            $userReview = $product->reviews->where('user_id', auth()->id())->first();
                        @endphp

                        @if(!$userReview)
                            <form action="{{ route('reviews.store', $product) }}" method="POST" class="bg-gray-50 p-4 rounded-lg mb-6">
                                @csrf
                                <h3 class="font-semibold mb-3">Write a Review</h3>
                                
                                <div class="mb-3">
                                    <label class="block mb-1">Rating:</label>
                                    <select name="rating" required class="border-gray-300 rounded-md">
                                        <option value="">Select rating</option>
                                        <option value="5">5 - Excellent</option>
                                        <option value="4">4 - Good</option>
                                        <option value="3">3 - Average</option>
                                        <option value="2">2 - Poor</option>
                                        <option value="1">1 - Terrible</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="block mb-1">Comment (optional):</label>
                                    <textarea name="comment" rows="3" class="border-gray-300 rounded-md w-full"></textarea>
                                </div>

                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                                    Submit Review
                                </button>
                            </form>
                        @endif
                    @endauth

                    <!-- Reviews List -->
                    <div class="space-y-4">
                        @forelse($product->reviews as $review)
                            <div class="border-b pb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <div>
                                        <span class="font-semibold">{{ $review->user->name }}</span>
                                        <div class="flex text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                {{ $i <= $review->rating ? '★' : '☆' }}
                                            @endfor
                                        </div>
                                    </div>
                                    <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                @endif

                                @if(auth()->id() === $review->user_id)
                                    <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 text-sm hover:underline">Delete Review</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500">No reviews yet. Be the first to review!</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Related Products -->
            @if($relatedProducts->count() > 0)
                <div class="mt-8">
                    <h2 class="text-2xl font-bold mb-4">Related Products</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        @foreach($relatedProducts as $related)
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                                @if($related->image_url)
                                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-gray-200"></div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold truncate">{{ $related->name }}</h3>
                                    <p class="text-indigo-600 font-bold">Rp {{ number_format($related->price, 0, ',', '.') }}</p>
                                    <a href="{{ route('products.show', $related->slug) }}" class="text-indigo-600 text-sm hover:underline">View Details</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
