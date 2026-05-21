<div class="rating-distribution mt-4">
    <h4>Phân phối đánh giá</h4>
    @php $totalReviews = $restaurant->reviews->count() ?: 1; // Tránh lỗi chia cho 0 @endphp

    @foreach([5, 4, 3, 2, 1] as $star)
    @php
    $count = $ratingDistribution[$star];
    $percent = ($count / $totalReviews) * 100;
    @endphp
    <div class="d-flex align-items-center mb-2">
        <span class="me-2" style="width: 40px">{{ $star }} ⭐</span>
        <div class="progress flex-grow-1" style="height: 10px;">
            <div class="position-relative overflow-hidden rounded-top" style="aspect-ratio: 4/3;"> </div>
            <span class="ms-2 text-muted" style="width: 30px">{{ $count }}</span>
        </div>
        @endforeach
    </div>

    @if($canReview)
    <div class="alert alert-success mt-3">
        Bạn đã trải nghiệm nhà hàng này. <a href="#review-form">Viết đánh giá ngay!</a>
    </div>
    @else
    <div class="alert alert-secondary mt-3">
        Chỉ những khách hàng đã đặt bàn và hoàn tất bữa ăn mới có thể đánh giá.
    </div>
    @endif