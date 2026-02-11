<div class="modal-body">
    <div class="card">
        <div class="card-body table-border-style full-card">
            <div class="table-responsive">
                <style>
                    .product-image {
                        border: 2px solid transparent;
                        cursor: pointer;
                        transition: 0.3s;
                        height: 120px;
                        object-fit: cover;
                        margin-bottom: 10px;
                    }

                    .product-image.main {
                        border-color: #28a745;
                        box-shadow: 0 0 10px rgba(40, 167, 69, 0.5);
                    }

                    .no-image-text {
                        font-style: italic;
                        color: #888;
                        text-align: center;
                    }
                </style>

                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('Warehouse') }}</th>
                            <th>{{ __('Quantity') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                {{-- <td>{{ !empty($product->warehouse)?$product->warehouse->name:'-' }}</td> --}}
                                <td colspan="2">
                                    <div class="row">
                                        @forelse ($product->images as $image)
                                            <div class="col-4 col-md-3 text-center">
                                                <form method="POST"
                                                    action="{{ route('product.setMainImage', $image->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="image_id" value="{{ $image->id }}">
                                                    <img src="{{ asset('storage/app/public/' . $image->image) }}"
                                                        class="product-image {{ $image->status == '1' ? 'main' : '' }}"
                                                        onclick="this.closest('form').submit();"
                                                        title="Click to set as main image"
                                                        style="width: 100%; height: 120px; object-fit: cover; border: 2px solid {{ $image->status == '1' ? '#28a745' : 'transparent' }};">
                                                </form>
                                            </div>
                                        @empty
                                            <div class="col-12">
                                                <p class="no-image-text">No images available for this product.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center">{{ __('Product not selected in warehouse') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
