@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">Edit Product</h4>
                    </div>
                    <div class="card-body">

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Product Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" 
                                           value="{{ old('name', $product->name) }}" required>
                                </div>

                                <!-- SKU -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">SKU <span class="text-danger">*</span></label>
                                    <input type="text" name="sku" class="form-control" 
                                           value="{{ old('sku', $product->sku) }}" required>
                                </div>

                                <!-- Price -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Price (₹) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" step="0.01" class="form-control" 
                                           value="{{ old('price', $product->price) }}" required>
                                </div>

                                <!-- Sale Price -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Sale Price (₹)</label>
                                    <input type="number" name="sale_price" step="0.01" class="form-control" 
                                           value="{{ old('sale_price', $product->sale_price) }}">
                                </div>

                                <!-- Cost Price -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Cost Price (₹)</label>
                                    <input type="number" name="cost_price" step="0.01" class="form-control" 
                                           value="{{ old('cost_price', $product->cost_price) }}">
                                </div>

                                <!-- Stock Quantity -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="stock_quantity" class="form-control" 
                                           value="{{ old('stock_quantity', $product->stock_quantity) }}" required>
                                </div>

                                <!-- Category ID -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Category ID</label>
                                    <input type="number" name="category_id" class="form-control" 
                                           value="{{ old('category_id', $product->category_id) }}">
                                </div>

                                <!-- Existing Image Preview -->
                                @if($product->image)
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">Current Product Image</label>
                                        <div>
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="img-thumbnail" style="max-height: 150px;">
                                        </div>
                                    </div>
                                @endif

                                <!-- Image Upload -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Change Product Image</label>
                                    <input type="file" name="image" class="form-control" accept="image/*">
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                                </div>

                                <!-- Additional Images -->
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Additional Images (JSON - Optional)</label>
                                    <textarea name="additional_images" class="form-control" rows="2" 
                                        placeholder='["image1.jpg", "image2.jpg"]'>{{ old('additional_images', $product->additional_images) }}</textarea>
                                </div>

                                <!-- Is Active -->
                                <div class="col-md-12 mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_active" class="form-check-input" 
                                               value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                                        <label class="form-check-label">Is Active</label>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('products.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Update Product</button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection