<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Đường dẫn tới Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <title>Thêm Gold Cho Người Dùng</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Thêm Gold Cho Người Dùng</h2>
        <form action="index.php?action=quanlygold&query=them" method="POST">
            <div class="form-group">
                <label for="ma_tk">Mã Tài Khoản:</label>
                <input type="text" class="form-control" id="ma_tk" name="ma_tk" required>
            </div>
            <button type="submit" class="btn btn-primary">Tìm Tài Khoản</button>
        </form>
    </div>
</body>
</html>