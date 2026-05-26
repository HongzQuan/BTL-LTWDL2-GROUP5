<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    // Các loại món (đồng bộ với ENUM/giá trị trong DB)
    private const MENU_TYPES = ['khai_vi', 'mon_chinh', 'buffet', 'trang_mieng', 'do_uong'];

    // ─────────────────────────────────────────────────────────────────────
    // INDEX
    // ─────────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = MenuItem::with('restaurant')->latest();

        if ($request->filled('restaurant_id')) {
            $query->where('restaurant_id', $request->integer('restaurant_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $menuItems   = $query->paginate(20)->withQueryString();
        $restaurants = Restaurant::orderBy('name')->get(['id', 'name']);
        $types       = self::MENU_TYPES;

        return view('admin.menus.index', compact('menuItems', 'restaurants', 'types'));
    }

    // ─────────────────────────────────────────────────────────────────────
    // STORE
    // ─────────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $this->validateMenuItem($request);

        // Thay đổi: Lưu ảnh thẳng vào public/uploads/menus
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/menus'), $filename);
            $validated['image'] = 'uploads/menus/' . $filename;
        } else {
            $validated['image'] = null;
        }

        MenuItem::create($validated);

        return redirect()
            ->route('admin.menus.index', $this->filterParams($request))
            ->with('success', 'Đã thêm món ăn thành công.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $menuItem  = MenuItem::findOrFail($id);
        $validated = $this->validateMenuItem($request);

        // Thay đổi: Xóa ảnh cũ và up ảnh mới thẳng vào public/uploads/menus
        if ($request->hasFile('image')) {
            $this->deleteImage($menuItem->image);

            $file = $request->file('image');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/menus'), $filename);
            $validated['image'] = 'uploads/menus/' . $filename;
        }

        $menuItem->update($validated);

        return redirect()
            ->route('admin.menus.index', $this->filterParams($request))
            ->with('success', 'Đã cập nhật món ăn thành công.');
    }
    // ─────────────────────────────────────────────────────────────────────
    // DESTROY
    // ─────────────────────────────────────────────────────────────────────
    public function destroy($id)
    {
        $menuItem = MenuItem::findOrFail($id);

        $this->deleteImage($menuItem->image);
        $menuItem->delete();

        return redirect()
            ->back()
            ->with('success', 'Đã xóa món ăn thành công.');
    }

    // ─────────────────────────────────────────────────────────────────────
    // TOGGLE is_available  →  PUT /admin/menus/{id}/toggle
    // ─────────────────────────────────────────────────────────────────────
    public function toggle($id)
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->update([
            'is_available' => ! $menuItem->is_available,
        ]);

        return response()->json([
            'success'      => true,
            'is_available' => (int) $menuItem->is_available,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────

    /** Validation dùng chung cho store & update */
    private function validateMenuItem(Request $request): array
    {
        return $request->validate([
            'restaurant_id' => ['required', 'integer', 'exists:restaurants,id'],
            'name'          => ['required', 'string', 'max:255'],
            'price'         => ['required', 'numeric', 'min:0'],
            'type'          => ['required', Rule::in(self::MENU_TYPES)],
            'image'         => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'is_available'  => ['boolean'],
        ], [
            'name.required'          => 'Tên món ăn không được để trống.',
            'price.required'         => 'Giá món ăn không được để trống.',
            'price.numeric'          => 'Giá phải là số.',
            'type.in'                => 'Loại món không hợp lệ.',
            'restaurant_id.exists'   => 'Nhà hàng không tồn tại.',
            'image.image'            => 'File phải là hình ảnh.',
            'image.max'              => 'Ảnh không được vượt quá 2MB.',
        ]);
    }

    /** Xóa file ảnh cũ khỏi storage */
    private function deleteImage(?string $path): void
    {
        // Vì ảnh giờ nằm thẳng ở public, ta dùng file_exists và unlink của PHP
        if ($path && file_exists(public_path($path))) {
            unlink(public_path($path));
        }
    }

    /** Giữ lại filter params khi redirect */
    private function filterParams(Request $request): array
    {
        return $request->only(['restaurant_id', 'type']);
    }
}
