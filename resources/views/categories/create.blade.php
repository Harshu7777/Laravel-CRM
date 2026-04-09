@extends('layouts.app')

@section('title', 'Add New Category')

@section('content')
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">

                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4>Add New Category</h4>
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

                        <form action="{{ route('categories.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label">Category Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control"
                                       value="{{ old('name') }}" required>
                            </div>

                            {{-- Slug --}}
                            <div class="mb-3">
                                <label class="form-label">Slug <span class="text-muted">(optional)</span></label>
                                <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                                <small class="text-muted">Leave empty to auto-generate from name</small>
                            </div>

                            {{-- ✅ Parent Category — FIXED --}}
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">Parent Category</label>
                                <select name="parent_id" id="parent_id" class="form-select">

                                    {{-- No parent = root/main category --}}
                                    <option value="">— No Parent (Main Category) —</option>

                                    @foreach($parentCategories as $parent)
                                        {{-- ✅ $category nahi hai yahan, sirf old() use karo --}}
                                        <option value="{{ $parent->id }}"
                                            {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                            @if($parent->parent)
                                                (under: {{ $parent->parent->name }})
                                            @endif
                                        </option>
                                    @endforeach

                                </select>
                                <small class="text-muted">
                                    Leave empty if this is a top-level category
                                </small>
                            </div>

                            {{-- Position --}}
                            <div class="mb-3">
                                <label class="form-label">Position</label>
                                <input type="number" name="position" class="form-control"
                                       value="{{ old('position', 0) }}" min="0">
                                <small class="text-muted">Lower number = appears first</small>
                            </div>

                            {{-- Description --}}
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control"
                                          rows="3">{{ old('description') }}</textarea>
                            </div>

                            {{-- Image --}}
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>

                            {{-- Is Active --}}
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_active" id="is_active"
                                       class="form-check-input" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <a href="{{ route('categories.index') }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary">Save Category</button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection