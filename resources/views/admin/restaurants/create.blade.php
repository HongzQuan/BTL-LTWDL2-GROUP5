@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.restaurants.store') }}" class="text-decoration-none">&larr; Quay lại danh sách</a>
        <h2 class="mt-2">Thêm Nhà Hàng Mới</h2>
    </div>

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('admin.restaurants.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên nhà hàng <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chọn danh mục --</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Thành phố <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giờ mở cửa <span class="text-danger">*</span></label>
                        <input type="time" name="open_time" class="form-control" value="{{ old('open_time', '08:00') }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giờ đóng cửa <span class="text-danger">*</span></label>
                        <input type="time" name="close_time" class="form-control"
                            value="{{ old('close_time', '22:00') }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giá tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price_min" class="form-control" value="{{ old('price_min') }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giá tối đa (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price_max" class="form-control" value="{{ old('price_max') }}"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ảnh Banner <span class="text-danger">*</span></label>
                    <input type="file" name="image" id="imageInput" class="form-control" accept="image/*" required>
                    <!-- Preview ảnh -->
                    <div class="mt-3 text-center">
                        <img id="imagePreview" src="" alt="Preview" class="img-fluid rounded d-none"
                            style="max-height: 250px; object-fit: cover;">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Lưu Nhà Hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JS hiển thị ảnh preview
document.getElementById('imageInput').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('imagePreview');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    } else {
        preview.classList.add('d-none');
        preview.src = '';
    }
});
</script>
@endsection