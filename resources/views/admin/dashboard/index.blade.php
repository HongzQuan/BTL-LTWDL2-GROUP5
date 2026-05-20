@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <!-- Metric Cards -->
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Tổng nhà hàng</h5>
                <h2 class="mb-0">{{ $totalRestaurants }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Đơn đặt hôm nay</h5>
                <h2 class="mb-0">{{ $todayBookings }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-dark bg-warning mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Chờ xác nhận</h5>
                <h2 class="mb-0">{{ $pendingBookings }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">Khách hàng</h5>
                <h2 class="mb-0">{{ $totalUsers }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Biểu đồ Chart.js -->
    <div class="col-md-8 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                Biểu đồ đơn đặt 7 ngày qua
            </div>
            <div class="card-body">
                <canvas id="bookingChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 Nhà Hàng -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-bold">
                Top nhà hàng (7 ngày qua)
            </div>
            <ul class="list-group list-group-flush">
                @forelse($topRestaurants as $restaurant)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $restaurant->name }}
                        <span class="badge bg-primary rounded-pill">{{ $restaurant->bookings_count }} đơn</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">Chưa có dữ liệu</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<!-- 10 Đơn đặt mới nhất -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-bold">
        10 đơn đặt bàn mới nhất
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã</th>
                        <th>Khách hàng</th>
                        <th>Nhà hàng</th>
                        <th>Bàn</th>
                        <th>Thời gian</th>
                        <th>Số lượng</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBookings as $booking)
                        <tr>
                            <td>#{{ $booking->id }}</td>
                            <td>{{ $booking->user->name ?? 'N/A' }}</td>
                            <td>{{ $booking->restaurant->name ?? 'N/A' }}</td>
                            <td>{{ $booking->table->name ?? 'N/A' }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }} 
                                <br> <small class="text-muted">{{ $booking->booking_time }}</small>
                            </td>
                            <td>{{ $booking->guests }} người</td>
                            <td>
                                @php
                                    $badgeClass = [
                                        'pending' => 'bg-warning text-dark',
                                        'confirmed' => 'bg-primary',
                                        'completed' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                    ][$booking->status] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($booking->status) }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-3 text-muted">Chưa có đơn đặt bàn nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('bookingChart').getContext('2d');
        const chartData = JSON.parse('{!! json_encode($chartData) !!}');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [{
                    label: 'Số lượng đơn đặt',
                    data: chartData.data,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3 // Làm cong đường line cho mượt
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 } // Hiển thị số nguyên
                    }
                }
            }
        });
    });
</script>
@endpush