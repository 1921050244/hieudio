<?php

// Kết nối cơ sở dữ liệu
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($mysqli->connect_error) {
    die('Kết nối không thành công: ' . $mysqli->connect_error);
}

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] == 4) {
    echo "<script>alert('Bạn không có quyền truy cập.'); window.location.href='index.php';</script>";
    exit();
}

$id_truyen = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_truyen == 0) {
    echo "<script>alert('Truyện không hợp lệ.'); window.location.href='index.php';</script>";
    exit();
}

// Lấy tháng và năm hiện tại
$currentMonth = date('n'); // Tháng hiện tại (1-12)
$currentYear = date('Y');  // Năm hiện tại

// Truy vấn chi tiết các tháng và trạng thái của truyện
$query = "SELECT t.tieude, g.thang, g.nam, g.gold, g.status, g.id_gold_theothang
          FROM tbl_gold_theothang g
          JOIN tbl_truyen t ON g.id_truyen = t.id_truyen
          WHERE g.id_truyen = ? AND NOT (g.thang = ? AND g.nam = ?)
          ORDER BY g.nam, g.thang";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("iii", $id_truyen, $currentMonth, $currentYear);
$stmt->execute();
$result = $stmt->get_result();

$storyDetails = [];
$storyTitle = '';
while ($row = $result->fetch_assoc()) {
    $storyTitle = $row['tieude'];
    $storyDetails[] = [
        'thang' => $row['thang'],
        'nam' => $row['nam'],
        'gold' => $row['gold'],
        'status' => $row['status'],
        'id_gold_theothang' => $row['id_gold_theothang']
    ];
}

// Đóng kết nối
$mysqli->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết truyện</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Chi tiết truyện: <?php echo htmlspecialchars($storyTitle); ?></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Tháng</th>
                    <th>Năm</th>
                    <th>GOLD</th>

                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($storyDetails as $detail) {
                    $status = $detail['status'] == 0 ? 'Chưa thanh toán' : 'Đã thanh toán';
                    $action = $detail['status'] == 0
                        ? "<button class='btn btn-warning' onclick='thanhToan({$detail['id_gold_theothang']}, this)'>Thanh toán</button>"
                        : "<a href='' class='btn btn-info'>Thành công</a>";
                    echo "<tr>
                            <td>{$detail['thang']}</td>
                            <td>{$detail['nam']}</td>
                            <td>{$detail['gold']}</td>

                            <td><span class='badge " . ($detail['status'] == 0 ? 'badge-danger' : 'badge-success') . "' id='status-{$detail['id_gold_theothang']}'>{$status}</span></td>
                            <td id='action-{$detail['id_gold_theothang']}'>{$action}</td>
                        </tr>";
                }
                ?>
            </tbody>
        </table>
        <a href="index.php?action=quanlythanhtoan&query=lietke" class="btn btn-secondary">Quay lại</a>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function thanhToan(id_gold_theothang, button) {
            $.ajax({
                url: 'modules/quanlythanhtoan/process_payment.php',
                type: 'POST',
                data: { id: id_gold_theothang },
                success: function(response) {
                    response = JSON.parse(response);
                    alert(response.message);
                    if (response.success) {
                        // Update the status badge
                        $('#status-' + id_gold_theothang).removeClass('badge-danger').addClass('badge-success').text('Đã thanh toán');
                        // Update the action button
                        $('#action-' + id_gold_theothang).html("<a href='xemchitiet.php?id=" + id_gold_theothang + "' class='btn btn-info'>Xem chi tiết</a>");
                    }
                },
                error: function() {
                    alert('Đã xảy ra lỗi khi thanh toán.');
                }
            });
        }
    </script>
</body>
</html>
