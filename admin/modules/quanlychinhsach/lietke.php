<?php
// Kết nối cơ sở dữ liệu

// Xử lý các thao tác CRUD

        $result = $mysqli->query("SELECT * FROM tbl_chinhsach ");
        $policy = $result->fetch_assoc();



// Lấy danh sách chính sách từ cơ sở dữ liệu
$result = $mysqli->query("SELECT * FROM tbl_chinhsach");

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Chính sách</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2>Quản lý Chính sách</h2>
    <a href="index.php?action=quanlychinhsach&query=them" class="btn btn-primary mb-2">Thêm Chính sách mới</a>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên Chính sách</th>
            
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id_chinhsach']; ?></td>
                        <td><?php echo htmlspecialchars($row['tenchinhsach']); ?></td>
                        <td><?php echo $row['status'] == 1 ? 'Kích hoạt' : 'Không kích hoạt'; ?></td>
                        <td>
                            <a href="index.php?action=quanlychinhsach&query=sua&id_chinhsach=<?php echo $row['id_chinhsach']; ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="index.php?action=quanlychinhsach&query=xuly&trangthai=delete&id_chinhsach=<?php echo $row['id_chinhsach']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

</div>
</body>
</html>
