<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Table;
use App\Models\Restaurant;
use App\Models\Booking; // Đảm bảo đã có Model này

class TableController extends Controller
{

    public function index(Request $request)
    {
        // Eager load nhà hàng để tránh lỗi N+1 Query
        $query = Table::with('restaurant');

        // Lọc theo restaurant_id nếu có
        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->restaurant_id);
        }

        $tables = $query->paginate(15)->appends($request->all()); // Giữ nguyên filter khi chuyển trang
        $restaurants = Restaurant::all(); // Lấy danh sách để đổ vào Filter và Modal Form

        return view('admin.tables.index', compact('tables', 'restaurants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name'          => 'required|string|max:255',
            'capacity'      => 'required|integer|min:1|max:50',
            'status'        => 'required|in:available,occupied,maintenance',
            'note'          => 'nullable|string'
        ]);

        Table::create($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Thêm bàn thành công!');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'name'          => 'required|string|max:255',
            'capacity'      => 'required|integer|min:1|max:50',
            'status'        => 'required|in:available,occupied,maintenance',
            'note'          => 'nullable|string'
        ]);

        $table = Table::findOrFail($id);
        $table->update($validated);

        return redirect()->route('admin.tables.index')->with('success', 'Cập nhật bàn thành công!');
    }

    public function destroy($id)
    {
        $table = Table::findOrFail($id);

        // Kiểm tra xem bàn có đơn đặt nào đang pending hoặc confirmed không
        $hasActiveBookings = Booking::where('table_id', $id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        if ($hasActiveBookings) {
            return redirect()->route('admin.tables.index')
                             ->with('error', 'Không thể xóa bàn đang có đơn đặt chờ xử lý!');
        }

        $table->delete();

        return redirect()->route('admin.tables.index')->with('success', 'Xóa bàn thành công!');
    }

    public function toggleStatus($id)
    {
        $table = Table::findOrFail($id);

        // Đổi trạng thái theo vòng lặp bằng biểu thức match (PHP 8+)
        $nextStatus = match($table->status) {
            'available'   => 'occupied',
            'occupied'    => 'maintenance',
            'maintenance' => 'available',
            default       => 'available',
        };

        $table->update(['status' => $nextStatus]);

        return redirect()->back()->with('success', 'Đổi trạng thái bàn thành công!');
    }
}