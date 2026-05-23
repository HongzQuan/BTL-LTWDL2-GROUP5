<?php

namespace App\Services;

use App\Models\Table;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\ValidationException;

class BookingService
{
    /**
     * Kiểm tra và trả về danh sách bàn còn trống
     */
    public function checkAvailability(int $restaurantId, string $date, string $time, int $guests): Collection
    {
        // 1. Tìm các ID bàn đã được đặt tại cùng ngày, giờ và đang ở trạng thái pending/confirmed
        $bookedTableIds = Booking::where('booking_date', $date)
            ->where('booking_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('table_id');

        // 2. Lấy danh sách bàn thuộc nhà hàng, trừ đi các bàn đã đặt, đủ sức chứa và đang available
        return Table::where('restaurant_id', $restaurantId)
            ->whereNotIn('id', $bookedTableIds)
            ->where('capacity', '>=', $guests)
            ->where('status', 'available')
            ->get();
    }

    /**
     * Tạo mới một Booking
     */
    public function createBooking(array $data, int $userId): Booking
    {
        // Gọi lại checkAvailability để verify lần cuối
        $availableTables = $this->checkAvailability(
            $data['restaurant_id'],
            $data['booking_date'],
            $data['booking_time'],
            $data['guests']
        );

        // Kiểm tra xem table_id khách chọn có nằm trong danh sách bàn trống không
        if (!$availableTables->contains('id', $data['table_id'])) {
            throw ValidationException::withMessages([
                'table_id' => 'Rất tiếc, bàn này vừa được đặt hoặc không đủ sức chứa. Vui lòng chọn bàn khác.'
            ]);
        }

        // Gắn thêm user_id và set status mặc định là pending
        $data['user_id'] = $userId;
        $data['status'] = 'pending';

        return Booking::create($data);
    }
}