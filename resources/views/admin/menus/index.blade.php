@extends('layouts.admin')

@section('title', 'Quản lý Menu')

@php
// Bộ từ điển để hiển thị tiếng Việt có dấu cho loại món ăn
$typeLabels = [
'khai_vi' => 'Khai vị',
'mon_chinh' => 'Món chính',
'trang_mieng' => 'Tráng miệng',
'do_uong' => 'Đồ uống'
];
@endphp

@push('styles')
<style>
    /* ── Toggle Switch ── */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 48px;
        height: 26px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #ccc;
        border-radius: 26px;
        transition: .3s;
    }

    .toggle-slider:before {
        content: "";
        position: absolute;
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background: white;
        border-radius: 50%;
        transition: .3s;
    }

    input:checked+.toggle-slider {
        background-color: #28a745;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(22px);
    }

    .toggle-switch.loading .toggle-slider {
        opacity: .5;
        pointer-events: none;
    }

    /* ── Image preview ── */
    #imagePreview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
        display: none;
    }

    #imagePreview.show {
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">

    {{-- ── Page Header ──────────────────────────────────────────────────── --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0 fw-bold">
            <i class="bi bi-menu-button-wide me-2 text-primary"></i>Quản lý Menu
        </h4>
        <button class="btn btn-primary shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#menuModal"
            onclick="openCreateModal()">
            <i class="bi bi-plus-lg me-1"></i>Thêm món ăn
        </button>
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────────────── --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- ── Filter Form ──────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.menus.index') }}"
                class="row g-3 align-items-end">

                {{-- Chọn nhà hàng --}}
                <div class="col-md-4">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-building me-1"></i>Nhà hàng
                    </label>
                    <select name="restaurant_id" class="form-select form-select-sm">
                        <option value="">-- Tất cả nhà hàng --</option>
                        @foreach($restaurants as $restaurant)
                        <option value="{{ $restaurant->id }}"
                            {{ request('restaurant_id') == $restaurant->id ? 'selected' : '' }}>
                            {{ $restaurant->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Chọn loại món --}}
                <div class="col-md-3">
                    <label class="form-label small fw-semibold text-muted mb-1">
                        <i class="bi bi-tag me-1"></i>Loại món
                    </label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">-- Tất cả loại --</option>
                        @foreach($types as $type)
                        <option value="{{ $type }}"
                            {{ request('type') === $type ? 'selected' : '' }}>
                            {{ $typeLabels[$type] ?? ucfirst($type) }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-sm btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Lọc dữ liệu
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.menus.index') }}"
                        class="btn btn-sm btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i>Xóa lọc
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Data Table ───────────────────────────────────────────────────── --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="60" class="ps-3">STT</th>
                            <th width="60">Ảnh</th>
                            <th>Tên món</th>
                            <th>Nhà hàng</th>
                            <th width="120">Loại</th>
                            <th width="120">Giá</th>
                            <th width="110" class="text-center">Còn phục vụ</th>
                            <th width="120" class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menuItems as $item)
                        <tr>
                            {{-- STT --}}
                            <td class="text-muted ps-3 small">
                                {{ $menuItems->firstItem() + $loop->index }}
                            </td>

                            {{-- Ảnh thumbnail --}}
                            <td>
                                @if($item->image)
                                <img src="{{ Storage::url($item->image) }}"
                                    alt="{{ $item->name }}"
                                    width="40" height="40"
                                    class="rounded object-fit-cover border">
                                @else
                                <div class="bg-light rounded d-flex align-items-center
                                                justify-content-center border"
                                    style="width:40px;height:40px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                                @endif
                            </td>

                            {{-- Tên --}}
                            <td class="fw-semibold">{{ $item->name }}</td>

                            {{-- Nhà hàng --}}
                            <td class="text-muted small">
                                {{ $item->restaurant->name ?? '—' }}
                            </td>

                            {{-- Loại --}}
                            <td>
                                <span class="badge px-2 py-1
                                    @switch($item->type)
                                        @case('khai_vi')     bg-success  @break
                                        @case('mon_chinh')   bg-danger   @break
                                        @case('trang_mieng') bg-warning text-dark @break
                                        @case('do_uong')     bg-info     @break
                                        @default             bg-secondary
                                    @endswitch">
                                    {{ $typeLabels[$item->type] ?? ucfirst($item->type) }}
                                </span>
                            </td>

                            {{-- Giá --}}
                            <td class="fw-semibold text-danger">
                                {{ number_format($item->price, 0, ',', '.') }}₫
                            </td>

                            {{-- Toggle is_available --}}
                            <td class="text-center">
                                <label class="toggle-switch" id="toggle-wrap-{{ $item->id }}">
                                    <input type="checkbox"
                                        {{ $item->is_available ? 'checked' : '' }}
                                        onchange="toggleAvailable({{ $item->id }}, this)">
                                    <span class="toggle-slider"></span>
                                </label>
                            </td>

                            {{-- Actions --}}
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    {{-- Nút Sửa --}}
                                    <button class="btn btn-sm btn-outline-warning px-2 py-1"
                                        title="Chỉnh sửa"
                                        data-bs-toggle="modal"
                                        data-bs-target="#menuModal"
                                        onclick="openEditModal({{ $item->id }})">
                                        <i class="bi bi-pencil"></i>
                                    </button>

                                    {{-- Nút Xóa --}}
                                    <button class="btn btn-sm btn-outline-danger px-2 py-1"
                                        title="Xóa"
                                        onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->name) }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>

                                {{-- Hidden delete form --}}
                                <form id="delete-form-{{ $item->id }}"
                                    action="{{ route('admin.menus.destroy', $item->id) }}"
                                    method="POST" class="d-none">
                                    @csrf @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                                Không tìm thấy món ăn nào.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($menuItems->hasPages())
        <div class="card-footer bg-white py-3 border-top-0">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Hiển thị {{ $menuItems->firstItem() }}–{{ $menuItems->lastItem() }} / Tổng {{ $menuItems->total() }} món
                </small>
                {{ $menuItems->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════════════════
     MODAL: Thêm / Sửa món ăn
════════════════════════════════════════════════════════════════════════ --}}
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="menuModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>
                    <span id="modalTitleText">Thêm món ăn mới</span>
                </h5>
                <button type="button" class="btn-close btn-close-white"
                    data-bs-dismiss="modal"></button>
            </div>

            <form id="menuForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-body">
                    <div class="row g-3">

                        {{-- Nhà hàng --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Nhà hàng <span class="text-danger">*</span>
                            </label>
                            <select name="restaurant_id" id="f_restaurant_id"
                                class="form-select" required>
                                <option value="">-- Chọn nhà hàng --</option>
                                @foreach($restaurants as $restaurant)
                                <option value="{{ $restaurant->id }}">
                                    {{ $restaurant->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Loại món --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Loại món <span class="text-danger">*</span>
                            </label>
                            <select name="type" id="f_type" class="form-select" required>
                                <option value="">-- Chọn loại món --</option>
                                @foreach($types as $type)
                                <option value="{{ $type }}">{{ $typeLabels[$type] ?? ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tên món --}}
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">
                                Tên món <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="f_name"
                                class="form-control" placeholder="VD: Bò lúc lắc" required>
                        </div>

                        {{-- Giá --}}
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">
                                Giá (VNĐ) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="price" id="f_price"
                                class="form-control" min="0" step="1000"
                                placeholder="85000" required>
                        </div>

                        {{-- Ảnh --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">Ảnh món ăn</label>
                            <input type="file" name="image" id="f_image"
                                class="form-control"
                                accept="image/jpeg,image/png,image/jpg,image/webp"
                                onchange="previewImage(event)">
                            <div class="form-text">Tối đa 2MB. Định dạng: JPEG, PNG, WebP.</div>

                            {{-- Preview ảnh mới chọn --}}
                            <div class="mt-2 d-flex align-items-center gap-3">
                                <img id="imagePreview" src="#" alt="Preview">
                                <img id="currentImagePreview" src="#" alt="Ảnh hiện tại"
                                    width="80" height="80"
                                    class="rounded border object-fit-cover d-none"
                                    title="Ảnh hiện tại">
                                <small id="currentImageLabel"
                                    class="text-muted d-none">Ảnh hiện tại</small>
                            </div>
                        </div>

                        {{-- Trạng thái --}}
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                    name="is_available" id="f_is_available"
                                    value="1" checked>
                                <label class="form-check-label fw-semibold" for="f_is_available">
                                    Cho phép đặt món (Còn phục vụ)
                                </label>
                            </div>
                        </div>

                    </div>{{-- /row --}}
                </div>{{-- /modal-body --}}

                <div class="modal-footer border-top-0 bg-light">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="bi bi-save me-1"></i>
                        <span id="submitBtnText">Thêm món</span>
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection

{{-- ════════════════════════════════════════════════════════════════════ --}}
@push('scripts')

{{-- BƯỚC 1: XỬ LÝ DỮ LIỆU PHP Ở NGOÀI THẺ SCRIPT ĐỂ TRÁNH LỖI VS CODE --}}
@php
$formattedMenuItems = $menuItems->map(fn($i) => [
'id' => $i->id,
'name' => $i->name,
'price' => $i->price,
'type' => $i->type,
'restaurant_id' => $i->restaurant_id,
'is_available' => $i->is_available,
'image_url' => $i->image ? Storage::url($i->image) : null,
]);
@endphp

<script>
    // BƯỚC 2: TRUYỀN DỮ LIỆU ĐÃ XỬ LÝ SANG JS GỌN GÀNG
    const menuItemsData = @json($formattedMenuItems);

    // Lookup nhanh theo id
    const itemsById = Object.fromEntries(menuItemsData.map(i => [i.id, i]));

    // Route bases
    const routes = {
        store: "{{ route('admin.menus.store') }}",
        update: (id) => `/admin/menus/${id}`,
    };

    // ── Mở modal CREATE ───────────────────────────────────────────────────
    function openCreateModal() {
        const form = document.getElementById('menuForm');
        form.reset();
        form.action = routes.store;

        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitleText').textContent = 'Thêm món ăn mới';
        document.getElementById('submitBtnText').textContent = 'Thêm món';
        document.getElementById('f_is_available').checked = true;

        // Ẩn ảnh preview
        document.getElementById('imagePreview').classList.remove('show');
        document.getElementById('currentImagePreview').classList.add('d-none');
        document.getElementById('currentImageLabel').classList.add('d-none');
    }

    // ── Mở modal EDIT ─────────────────────────────────────────────────────
    function openEditModal(id) {
        const item = itemsById[id];
        if (!item) return;

        const form = document.getElementById('menuForm');
        form.action = routes.update(id);

        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitleText').textContent = 'Chỉnh sửa món ăn';
        document.getElementById('submitBtnText').textContent = 'Lưu thay đổi';

        // Điền dữ liệu
        document.getElementById('f_name').value = item.name;
        document.getElementById('f_price').value = item.price;
        document.getElementById('f_type').value = item.type;
        document.getElementById('f_restaurant_id').value = item.restaurant_id;
        document.getElementById('f_is_available').checked = !!item.is_available;

        // Reset file input & preview mới
        document.getElementById('f_image').value = '';
        document.getElementById('imagePreview').classList.remove('show');

        // Hiện ảnh hiện tại nếu có
        const currentImg = document.getElementById('currentImagePreview');
        const currentLabel = document.getElementById('currentImageLabel');
        if (item.image_url) {
            currentImg.src = item.image_url;
            currentImg.classList.remove('d-none');
            currentLabel.classList.remove('d-none');
        } else {
            currentImg.classList.add('d-none');
            currentLabel.classList.add('d-none');
        }
    }

    // ── Preview ảnh trước khi upload (FileReader) ─────────────────────────
    function previewImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('imagePreview');

        if (!file) {
            preview.classList.remove('show');
            return;
        }

        // Kiểm tra loại file client-side
        if (!file.type.match(/^image\/(jpeg|png|jpg|webp)$/)) {
            alert('Chỉ chấp nhận ảnh JPEG, PNG, WebP!');
            event.target.value = '';
            preview.classList.remove('show');
            return;
        }

        // Kiểm tra dung lượng (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('Ảnh không được vượt quá 2MB!');
            event.target.value = '';
            preview.classList.remove('show');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.classList.add('show');
        };
        reader.readAsDataURL(file);
    }

    // ── Toggle is_available qua AJAX ─────────────────────────────────────
    async function toggleAvailable(id, checkbox) {
        const wrapper = document.getElementById(`toggle-wrap-${id}`);
        if (wrapper) wrapper.classList.add('loading');

        try {
            const response = await fetch(`/admin/menus/${id}/toggle`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) throw new Error('Server error');

            const data = await response.json();
            if (data.success) {
                // Đồng bộ lại trạng thái checkbox với giá trị từ server
                checkbox.checked = data.is_available == 1;

                // Cập nhật dữ liệu local để modal edit không bị lệch
                if (itemsById[id]) {
                    itemsById[id].is_available = data.is_available;
                }
            }
        } catch (err) {
            console.error(err);
            // Hoàn tác toggle nếu lỗi
            checkbox.checked = !checkbox.checked;
            alert('Có lỗi xảy ra, vui lòng kiểm tra lại kết nối!');
        } finally {
            if (wrapper) wrapper.classList.remove('loading');
        }
    }

    // ── Xác nhận xóa ─────────────────────────────────────────────────────
    function confirmDelete(id, name) {
        if (confirm(`Bạn có chắc chắn muốn xóa món "${name}"?\nHành động này không thể hoàn tác!`)) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    }

    // ── Reset modal khi đóng ──────────────────────────────────────────────
    document.getElementById('menuModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('menuForm').reset();
        document.getElementById('imagePreview').classList.remove('show');
        document.getElementById('currentImagePreview').classList.add('d-none');
        document.getElementById('currentImageLabel').classList.add('d-none');
    });
</script>
@endpush