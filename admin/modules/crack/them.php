<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- jQuery (Full Version) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <title>Crack</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Crack từ truyenfull.vn</h2>
        <form id="truyenForm">
            <div class="form-group">
                <label for="duong_dan">Đường dẫn:</label>
                <input type="text" class="form-control" id="duong_dan" name="duong_dan" required>
            </div>
            <button type="submit" name="themtruyen" class="btn btn-primary">Thêm truyện</button>
        </form>
        <div id="result"></div>
    </div>

    <script>
        $(document).ready(function(){
            $('#truyenForm').submit(function(event){
                event.preventDefault();
                var duongDan = $('#duong_dan').val();

                $.ajax({
                    url: 'modules/crack/process_truyen.php',
                    type: 'POST',
                    data: {
                        duong_dan: duongDan
                    },
                    success: function(response) {
                        $('#result').html('<div class="alert alert-success" role="alert">' + response + '</div>');
                    },
                    error: function() {
                        $('#result').html('<div class="alert alert-danger" role="alert">Có lỗi xảy ra khi thêm truyện.</div>');
                    }
                });
            });
        });
    </script>
</body>
</html>
