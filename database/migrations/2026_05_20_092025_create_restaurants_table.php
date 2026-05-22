<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::create('restaurants', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Tên là bắt buộc
        $table->string('slug'); 
        $table->foreignId('category_id'); // Danh mục bắt buộc
        $table->string('address'); // Địa chỉ bắt buộc
        $table->string('city'); // Thành phố bắt buộc
        
        // CÁC CỘT DƯỚI ĐÂY NÊN CHO PHÉP TRỐNG (Thêm ->nullable())
        $table->string('district')->nullable();
        $table->text('description')->nullable();
        $table->string('price_range')->nullable(); // Thêm dòng này để fix lỗi hiện tại
        $table->string('phone')->nullable();
        $table->time('open_time')->nullable();
        $table->time('close_time')->nullable();
        $table->string('image')->nullable();
        $table->boolean('status')->default(1);
        
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::dropIfExists('restaurants');
    }
};