<?php
// Kiểm tra xem có dữ liệu được gửi từ POST không
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bao gồm file kết nối CSDL
    include('../admin/config/config.php');

    // Lấy dữ liệu từ POST
    $id_truyen = isset($_POST['id_truyen']) ? $_POST['id_truyen'] : null;
    $id_chuong = isset($_POST['id_chuong']) ? $_POST['id_chuong'] : null;
    $comment = isset($_POST['comment']) ? $_POST['comment'] : null;

    // Kiểm tra các giá trị cần thiết đã được gửi hay chưa
    if ($id_truyen === null || $id_chuong === null || $comment === null) {
        $response = [
            'status' => 'error',
            'message' => 'Thiếu thông tin cần thiết để đăng bình luận.'
        ];
        echo json_encode($response);
        exit();
    }

    // Kiểm tra xem người dùng đã đăng nhập hay chưa (ở đây là chỉ cần kiểm tra id_user được set trong session)
    session_start();
    if (!isset($_SESSION['id_user'])) {
        // Nếu chưa đăng nhập, trả về kết quả không thành công
        $response = [
            'status' => 'error',
            'message' => 'Bạn cần đăng nhập để đăng bình luận.'
        ];
        echo json_encode($response);
        exit();
    }

    // Lấy id_user từ session
    $id_user = $_SESSION['id_user'];

    // Thêm ngày bình luận hiện tại
    $ngayBinhLuan = date('Y-m-d H:i:s'); // Lấy ngày giờ hiện tại

    // Danh sách từ chửi bậy
    $bad_words = [
        // Tiếng Việt
        "đm", "vcl", "mẹ mày", "đụ má", "thằng chó", "con đĩ", "địt", "cặc", "lồn", "mịa", "chết tiệt", "khốn nạn", "ngu",
        "đồ ngu", "dm", "mả mẹ", "dcm", "đéo", "đít", "dâm đãng", "hấp diêm", "phò", "thổ tả",
        // Tiếng Anh
        "fuck", "shit", "bitch", "bastard", "asshole", "cunt", "damn", "dick", "pussy", "fucker", "motherfucker", "nigger", "retard",
        "whore", "slut", "douchebag", "prick", "cock", "twat", "wanker", "fag", "faggot", "cum", "cumshot", "spunk", "bollocks",
        "arse", "bugger", "wank", "sod off", "bloody", "tosser", "piss off", "minge", "minger", "piss", "shag", "slag"
    ];
    

    // Kiểm tra bình luận xem có chứa từ chửi bậy không
    foreach ($bad_words as $bad_word) {
        if (strpos($comment, $bad_word) !== false) {
            $response = [
                'status' => 'error',
                'message' => 'Bình luận chứa từ nóng, vui lòng bình luận lại.'
            ];
            echo json_encode($response);
            exit();
        }
    }

    // Thực hiện thêm bình luận vào CSDL với trường ngày bình luận
    $sql_insert_comment = "INSERT INTO tbl_binhluan (id_user, id_truyen, id_chuong, noidung, ngaybinhluan) 
                           VALUES ('$id_user', '$id_truyen', '$id_chuong', '$comment', '$ngayBinhLuan')";
    $result_insert_comment = mysqli_query($mysqli, $sql_insert_comment);

    // Kiểm tra kết quả thêm bình luận
    if ($result_insert_comment) {
        // Nếu thành công, trả về kết quả thành công
        $response = [
            'status' => 'success',
            'message' => 'Đăng bình luận thành công.'
        ];
        echo json_encode($response);
    } else {
        // Nếu thất bại, trả về thông báo lỗi
        $response = [
            'status' => 'error',
            'message' => 'Lỗi khi đăng bình luận: ' . mysqli_error($mysqli)
        ];
        echo json_encode($response);
    }

    // Đóng kết nối CSDL
    mysqli_close($mysqli);
} else {
    // Nếu không phải là yêu cầu POST, trả về thông báo lỗi
    $response = [
        'status' => 'error',
        'message' => 'Không có dữ liệu được gửi.'
    ];
    echo json_encode($response);
}
?>
