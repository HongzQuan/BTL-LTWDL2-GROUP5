@extends('layouts.admin')

@section('content')
<div class="card shadow-sm col-md-8 mx-auto">
    <div class="card-header bg-white fw-bold">Chỉnh sửa Danh mục</div>
    <div class="card-body">
        <form action="{{ route('categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $category->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" rows="4">{{ old('description', $category->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Hủy</a>
        </form>
    </div>
</div>
@endsection