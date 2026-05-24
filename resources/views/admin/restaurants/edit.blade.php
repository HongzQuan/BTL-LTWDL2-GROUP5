@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold m-0">Sửa thông tin: {{ $restaurant->name }}</h2>
        <a href="{{ route('admin.restaurants.index') }}" class="btn btn-secondary">
            ← Quay lại
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <!-- Lưu ý form sửa phải có route update và thêm tham số $restaurant->id -->
            <form action="{{ route('admin.restaurants.update', $restaurant->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') <!-- Bắt buộc phải có dòng này để báo cho Laravel biết đây là lệnh Cập nhật -->

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên nhà hàng <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $restaurant->name }}" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $restaurant->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Số điện thoại</label>
                                <input type="text" name="phone" class="form-control" value="{{ $restaurant->phone }}">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Thành phố <span class="text-danger">*</span></label>
                                <input type="text" name="city" class="form-control" value="{{ $restaurant->city }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Quận/Huyện</label>
                                <input type="text" name="district" class="form-control" value="{{ $restaurant->district }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Địa chỉ chi tiết <span class="text-danger">*</span></label>
                            <input type="text" name="address" class="form-control" value="{{ $restaurant->address }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4">{{ $restaurant->description }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Giờ hoạt động</h6>
                                <div class="mb-3">
                                    <label class="form-label small">Giờ mở cửa</label>
                                    <input type="time" name="open_time" class="form-control" value="{{ $restaurant->open_time }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small">Giờ đóng cửa</label>
                                    <input type="time" name="close_time" class="form-control" value="{{ $restaurant->close_time }}">
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-3">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">Khoảng giá (VNĐ)</h6>
                                <div class="mb-3">
                                    <label class="form-label small">Giá tối thiểu</label>
                                    <input type="number" name="price_min" class="form-control" value="{{ $restaurant->price_min }}">
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small">Giá tối đa</label>
                                    <input type="number" name="price_max" class="form-control" value="{{ $restaurant->price_max }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ảnh đại diện (Mới)</label>
                            <input type="file" name="image" class="form-control mb-2" accept="image/*">
                            @if($restaurant->image_url)
                            <small class="text-muted d-block mb-1">Ảnh hiện tại:</small>
                            <img src="{{ asset($restaurant->image_url) }}" alt="" class="rounded" style="height: 60px;">
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-5 fw-bold">Cập nhật Nhà Hàng</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection