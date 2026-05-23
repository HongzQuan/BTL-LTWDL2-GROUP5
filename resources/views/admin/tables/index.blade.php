@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Quản lý Bàn</h1>
        <!-- Nút mở Modal Thêm -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-circle"></i> + Thêm bàn
        </button>
    </div>

    <!-- Thông báo Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Form Filter Nhà Hàng -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <form action="{{ route('admin.tables.index') }}" method="GET" class="d-flex w-50">
                <select name="restaurant_id" class="form-select me-2">
                    <option value="">-- Tất cả nhà hàng --</option>
                    @foreach($restaurants as $res)
                    <option value="{{ $res->id }}" {{ request('restaurant_id') == $res->id ? 'selected' : '' }}>
                        {{ $res->name }}
                    </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">Lọc</button>
            </form>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">STT</th>
                            <th>Tên Bàn</th>
                            <th>Nhà Hàng</th>
                            <th>Sức Chứa</th>
                            <th>Trạng Thái</th>
                            <th>Ghi Chú</th>
                            <th width="20%">Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tables as $key => $table)
                        <tr>
                            <td>{{ $tables->firstItem() + $key }}</td>
                            <td><span class="fw-bold">{{ $table->name }}</span></td>
                            <td>{{ $table->restaurant->name ?? 'N/A' }}</td>
                            <td>{{ $table->capacity }} người</td>
                            <td>
                                @if($table->status == 'available')
                                <span class="badge bg-success">Trống</span>
                                @elseif($table->status == 'occupied')
                                <span class="badge bg-warning text-dark">Đang phục vụ</span>
                                @else
                                <span class="badge bg-danger">Bảo trì</span>
                                @endif
                            </td>
                            <td>{{ $table->note }}</td>
                            <td>
                                <div class="d-flex gap-1">
                                    <!-- Form Đổi trạng thái nhanh -->
                                    <form action="{{ route('admin.tables.toggleStatus', $table->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-info text-white" title="Đổi trạng thái">
                                            <i class="bi bi-arrow-repeat"></i>
                                        </button>
                                    </form>

                                    <!-- Nút Sửa -->
                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal{{ $table->id }}">
                                        Sửa
                                    </button>

                                    <!-- Nút Xóa (Kích hoạt Modal Xóa) -->
                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $table->id }}">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Sửa cho từng bàn -->
                        <div class="modal fade" id="editModal{{ $table->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.tables.update', $table->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Sửa thông tin bàn: {{ $table->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nhà Hàng</label>
                                                <select name="restaurant_id" class="form-select" required>
                                                    @foreach($restaurants as $res)
                                                    <option value="{{ $res->id }}" {{ $table->restaurant_id == $res->id ? 'selected' : '' }}>
                                                        {{ $res->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Tên Bàn</label>
                                                <input type="text" name="name" class="form-control" value="{{ $table->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Sức Chứa</label>
                                                <input type="number" name="capacity" class="form-control" min="1" max="50" value="{{ $table->capacity }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Trạng Thái</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="available" {{ $table->status == 'available' ? 'selected' : '' }}>Trống</option>
                                                    <option value="occupied" {{ $table->status == 'occupied' ? 'selected' : '' }}>Đang phục vụ</option>
                                                    <option value="maintenance" {{ $table->status == 'maintenance' ? 'selected' : '' }}>Bảo trì</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ghi Chú</label>
                                                <textarea name="note" class="form-control">{{ $table->note }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Xác Nhận Xóa -->
                        <div class="modal fade" id="deleteModal{{ $table->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title text-danger">Xác nhận xóa</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        Bạn có chắc chắn muốn xóa bàn <strong>{{ $table->name }}</strong> không? Hành động này không thể hoàn tác.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                        <form action="{{ route('admin.tables.destroy', $table->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">Đồng ý Xóa</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có dữ liệu bàn nào.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="mt-3">
                {{ $tables->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm Bàn (Duy nhất 1 form ở cuối trang) -->
<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.tables.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Thêm Bàn Mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nhà Hàng</label>
                        <select name="restaurant_id" class="form-select" required>
                            <option value="">-- Chọn nhà hàng --</option>
                            @foreach($restaurants as $res)
                            <option value="{{ $res->id }}">{{ $res->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tên Bàn (VD: Bàn 01, VIP 1)</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sức Chứa</label>
                        <input type="number" name="capacity" class="form-control" min="1" max="50" value="4" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng Thái</label>
                        <select name="status" class="form-select" required>
                            <option value="available" selected>Trống</option>
                            <option value="occupied">Đang phục vụ</option>
                            <option value="maintenance">Bảo trì</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ghi Chú</label>
                        <textarea name="note" class="form-control" placeholder="Tùy chọn..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm Bàn</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection