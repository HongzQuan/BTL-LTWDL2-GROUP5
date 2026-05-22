@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="mb-4">
        <a href="{{ route('admin.restaurants.index') }}" class="text-decoration-none">&larr; Quay lại danh sách</a>
        <h2 class="mt-2">Chỉnh Sửa Nhà Hàng: {{ $restaurant->name }}</h2>
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
            <form action="{{ route('admin.restaurants.update', $restaurant->id) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Tên nhà hàng <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $restaurant->name) }}"
                            required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ (old('category_id', $restaurant->category_id) == $cat->id) ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Trạng thái <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="1" {{ old('status', $restaurant->status) == 1 ? 'selected' : '' }}>Hoạt động
                            </option>
                            <option value="0" {{ old('status', $restaurant->status) == 0 ? 'selected' : '' }}>Ngừng HĐ
                            </option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control"
                            value="{{ old('address', $restaurant->address) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Thành phố <span class="text-danger">*</span></label>
                        <input type="text" name="city" class="form-control" value="{{ old('city', $restaurant->city) }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                        <input type="text" name="phone" class="form-control"
                            value="{{ old('phone', $restaurant->phone) }}" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giờ mở cửa <span class="text-danger">*</span></label>
                        <input type="time" name="open_time" class="form-control"
                            value="{{ old('open_time', \Carbon\Carbon::parse($restaurant->open_time)->format('H:i')) }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giờ đóng cửa <span class="text-danger">*</span></label>
                        <input type="time" name="close_time" class="form-control"
                            value="{{ old('close_time', \Carbon\Carbon::parse($restaurant->close_time)->format('H:i')) }}"
                            required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giá tối thiểu (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price_min" class="form-control"
                            value="{{ old('price_min', $restaurant->price_min) }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold">Giá tối đa (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price_max" class="form-control"
                            value="{{ old('price_max', $restaurant->price_max) }}" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">Ảnh Banner</label>
                    <input type="file" name="image" id="editImageInput" class="form-control" accept="image/*">
                    <small class="text-muted">Chỉ chọn ảnh mới nếu bạn muốn thay thế ảnh hiện tại.</small>

                    <div class="mt-3 text-center">
                        <!-- Hiển thị ảnh cũ hoặc ảnh vừa chọn -->
                        @if($restaurant->image)
                        <img id="editImagePreview" src="{{ asset('storage/' . $restaurant->image) }}" alt="Ảnh hiện tại"
                            class="img-fluid rounded border" style="max-height: 250px; object-fit: cover;">
                        @else
                        <img id="editImagePreview" src="" alt="Preview" class="img-fluid rounded border d-none"
                            style="max-height: 250px; object-fit: cover;">
                        @endif
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-warning px-4 fw-bold">Cập Nhật Nhà Hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// JS hiển thị ảnh preview
document.getElementById('editImageInput').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('editImagePreview');
    if (file) {
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('d-none');
    }
});
</script>
@endsection