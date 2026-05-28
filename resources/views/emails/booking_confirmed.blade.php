<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
        }

        .header {
            background-color: #dc3545;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .content {
            padding: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .table th,
        .table td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            text-align: left;
        }

        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Cảm ơn bạn đã đặt bàn tại TableGo!</h2>
        </div>
        <div class="content">
            <p>Chào <strong>{{ $booking->user->name }}</strong>,</p>
            <p>Hệ thống đã nhận được khoản thanh toán và xác nhận giữ chỗ cho bạn. Dưới đây là thông tin chi tiết:</p>

            <table class="table">
                <tr>
                    <th>Mã đơn hàng:</th>
                    <td>#{{ $booking->id }}</td>
                </tr>
                <tr>
                    <th>Nhà hàng:</th>
                    <td>{{ $booking->restaurant->name }}</td>
                </tr>
                <tr>
                    <th>Ngày đến:</th>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Giờ đến:</th>
                    <td>{{ \Carbon\Carbon::parse($booking->booking_time)->format('H:i') }}</td>
                </tr>
                <tr>
                    <th>Số khách:</th>
                    <td>{{ $booking->guests }} người</td>
                </tr>
            </table>

            <p>Vui lòng đến đúng giờ để có trải nghiệm tốt nhất nhé!</p>
        </div>
        <div class="footer">
            <p>Đây là email tự động từ hệ thống TableGo. Vui lòng không trả lời email này.</p>
        </div>
    </div>
</body>

</html>