

<?php

// Truy vấn CSDL để lấy đường dẫn ảnh từ bảng tbl_anhtrangbia
$sql = "SELECT hinhanh FROM tbl_anhtrangbia WHERE tinhtrang = 1 ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($sql);

// Kiểm tra và lấy đường dẫn ảnh
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagePath =  $row['hinhanh'];
} else {
    // Nếu không có dữ liệu, sử dụng ảnh mặc định hoặc đặt giá trị khác tùy ý
    $imagePath =  $row['hinhanh'];
}
?>

<!-- Thẻ img cho hiển thị ảnh -->
<img class="top-bg-op-box" src="<?php echo $imagePath; ?>" alt="Background Image">
<main>
<?php

    // Thực hiện câu truy vấn để lấy dữ liệu từ cơ sở dữ liệu
    $query = "SELECT 
    truyen.id_truyen,
    truyen.tieude,
    truyen.hinhanh,
    truyen.tomtat,
    truyen.ngaydang,
    truyen.tacgia,
    truyen.yeuthich,
    truyen.luotdoc,
    truyen.status_tt,
    truyen.decu,
    user.id_user,
    user.tenuser AS tentacgia,
    GROUP_CONCAT(DISTINCT theloai.tentheloai ORDER BY theloai.thutu ASC) AS danh_sach_theloai,
    COUNT(DISTINCT chuong.id_chuong) AS tongchuong
FROM
    tbl_truyen truyen
INNER JOIN
    tbl_user user ON truyen.id_admin = user.id_user
INNER JOIN
    tbl_truyen_theloai tt ON truyen.id_truyen = tt.id_truyen
INNER JOIN
    tbl_theloai theloai ON tt.id_theloai = theloai.id_theloai
LEFT JOIN 
    tbl_chuong chuong ON truyen.id_truyen = chuong.id_truyen
WHERE
    truyen.id_truyen = $id_truyen
GROUP BY
    truyen.id_truyen,
    truyen.tieude,
    truyen.hinhanh,
    truyen.tomtat,
    truyen.ngaydang,
    user.id_user,
    user.tenuser
ORDER BY
    truyen.ngaydang DESC;
";


    // Thực hiện truy vấn và lấy dữ liệu
    $result = $mysqli->query($query);

    // Kiểm tra và hiển thị dữ liệu
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
?>

            <div class="containers-acd">
                <div class="truyen-image-acd">
                    <img src="<?php echo $row['hinhanh']; ?>" alt="Ảnh truyện">
                </div>
                <div class="truyen-info-acd">
                    <h2 class="acd"><?php echo $row['tieude']; ?></h2>
                    <div class="status-theloai">
    <?php
    // Chia danh sách thể loại thành mảng
    $arrTheloai = explode(',', $row['danh_sach_theloai']);

    // Hiển thị từng thể loại trong box riêng
    
    foreach ($arrTheloai as $theloai) {
        echo '<a href="index.php?quanly=truyen&category[]=' . trim($theloai) . '" class="theloai231">';
        echo '<div class="theloai-box">' . trim($theloai) . '</div>';
        echo'</a>';
    }

    echo '<a href="index.php?quanly=truyen&truyen=';

    // Hiển thị trạng thái
    if ($row['status_tt'] == 1) {
        echo 'hoanthanh';
    } else {
        echo 'dangra';
    }
    
    echo '" class="theloai231">';
    echo '<div class="trangthai-box">';
    if ($row['status_tt'] == 1) {
        echo 'Hoàn thành';
    } else {
        echo 'Đang ra';
    }
    echo '</div>';
    echo '</a>';
    

    ?>
    
</div>


<?php

function formatNumber($number) {
    if ($number > 1000000) {
        // Nếu lớn hơn 1 triệu, chuyển thành đơn vị triệu
        return round($number / 1000000, 1) . 'M';
    } elseif ($number > 10000) {
        // Nếu lớn hơn 1 nghìn, chuyển thành đơn vị nghìn
        return round($number / 1000, 1) . 'k';
    } else {
        // Ngược lại, giữ nguyên giá trị
        return $number;
    }
}

?>

<ul class="acd-list">
    <li class="acd-item">
        <div class="acd-number"><?php echo formatNumber($row['tongchuong']); ?></div>
        <div class="acd-text">Chương</div>
    </li>
    <li class="acd-item">
        <div class="acd-number"><?php echo formatNumber($row['luotdoc']); ?></div>
        <div class="acd-text">Lượt đọc</div>
    </li>
    <?php 
// Lấy id_truyen từ dữ liệu hiện có
$id_truyen = $row['id_truyen']; 

// Truy vấn để lấy tổng số chữ của tất cả các chương của truyện với id_truyen
$sql = "SELECT SUM(CHAR_LENGTH(REGEXP_REPLACE(noidung, '[^a-zA-Z]', ''))) AS total_characters 
        FROM tbl_chuong 
        WHERE id_truyen = ?";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $id_truyen);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $rows = $result->fetch_assoc();
    $total_characters = $rows['total_characters'];
} else {
    $total_characters = 0;
}
?>

<li class="acd-item">
    <div class="acd-number"><?php echo formatNumber($total_characters); ?></div>
    <div class="acd-text">Số chữ</div>
</li>

    <li class="acd-item">
        <div class="acd-number"><?php echo formatNumber($row['decu']); ?></div>
        <div class="acd-text">Đề cử</div>
    </li>
</ul>


<div class="author-rating-container">
<a href="index.php?quanly=truyen&search=<?php echo urlencode($row['tacgia']); ?>" class="acd">
    <i class="fa-solid fa-user-pen"></i> <?php echo $row['tacgia']; ?>
</a>

    <?php

$sql_danhgia = "SELECT tinhcach, cottruyen, bocuc, chatluong, tongdiem
                FROM tbl_danhgia 
                WHERE id_truyen = $id_truyen";

$result_danhgia = $mysqli->query($sql_danhgia);

// Khởi tạo mảng đánh giá
$ratings = array();

// Lấy dữ liệu từ cơ sở dữ liệu và đưa vào mảng đánh giá
while ($row_danhgia = $result_danhgia->fetch_assoc()) {
    $ratings[] = $row_danhgia;
}

// Nếu có đánh giá
if (!empty($ratings)) {
    // Lấy trung bình của từng mục đánh giá
    $avg_tongdiem = round(array_sum(array_column($ratings, 'tongdiem')) / count($ratings), 2);

    // Số đánh giá
    $totalRatings = count($ratings);

    // Hiển thị số sao và điểm trung bình
    echo '<div class="star-container-123">';
    for ($i = 1; $i <= 5; $i++) {
        $starClass = ($i <= $avg_tongdiem) ? 'fa-star fa-solid' : 'fa-star fa-regular';
        echo '<i class="' . $starClass . '" style="color: #ffc000;"></i>';
    }
    echo '<span id="tongdiem-value-123">' . $avg_tongdiem . '</span>';
    echo '<span>/5 (' . $totalRatings . ' đánh giá)</span>';
    echo '</div>';
} else {
    // Nếu không có đánh giá, hiển thị 5 sao không màu
    echo '<div class="star-container-123">';
    for ($i = 1; $i <= 5; $i++) {
        echo '<i class="fa-star fa-regular" style="color: #ccc;"></i>';
    }
    echo '<span id="tongdiem-value-123">0</span>';
    echo '<span>/5 (0 đánh giá)</span>';
    echo '</div>';
}

?>

</div>


                    <div class="buttons-acd">

                    <?php
// ... (mã PHP hiện tại của bạn)

// Truy vấn lấy id_chuong cho truyện có sochuong=1
$queryChuongSo1 = "
    SELECT id_chuong
    FROM tbl_chuong
    WHERE id_truyen = $id_truyen AND sochuong = 1
    LIMIT 1;
";

// Thực hiện truy vấn và lấy dữ liệu
$resultChuongSo1 = $mysqli->query($queryChuongSo1);

// Kiểm tra và hiển thị nút "Đọc Từ Đầu"
if ($resultChuongSo1 && $resultChuongSo1->num_rows > 0) {
    $rowChuongSo1 = $resultChuongSo1->fetch_assoc();
    $id_chuong_so1 = $rowChuongSo1['id_chuong'];

    // Hiển thị nút "Đọc Từ Đầu" với id_chuong tương ứng
    echo '<a href="index.php?quanly=doc&id_truyen=' . $id_truyen . '&id_chuong=' . ($id_chuong_so1 ) . '" class="acd1"><i class="fa-solid fa-glasses"></i> Đọc Từ Đầu</a>';
} else {
    echo '<p>Không có chương nào</p>';
}

// ... (mã PHP hiện tại của bạn tiếp tục)
?>
<?php

// Kết nối đến CSDL (Giả sử bạn đã kết nối rồi)
// ...

if (isset($_SESSION['id_user'])) {
    $id_user = $_SESSION['id_user'];
    
    // Lấy thông tin từ CSDL về truyện đã đọc của người dùng
    $query_read_story = "SELECT 
                            truyen.id_truyen, 
                            truyen.tieude, 
                            truyen.hinhanh, 
                            MAX(chuong.sochuong) AS tong_so_chuong,
                            chuong_doc.id_chuong AS id_chuong_doc,
                            chuong_doc.sochuong AS so_chuong_doc
                        FROM 
                            tbl_truyen AS truyen
                        LEFT JOIN 
                            tbl_reading_status AS reading ON reading.id_truyen = truyen.id_truyen
                        LEFT JOIN 
                            tbl_chuong AS chuong ON truyen.id_truyen = chuong.id_truyen
                        LEFT JOIN 
                            tbl_chuong AS chuong_doc ON reading.id_chuong = chuong_doc.id_chuong
                        WHERE 
                            reading.id_user = $id_user AND truyen.id_truyen = $id_truyen
                        GROUP BY 
                            truyen.id_truyen, truyen.tieude, truyen.hinhanh, id_chuong_doc, so_chuong_doc
                        ";
    $result_read_story = mysqli_query($mysqli, $query_read_story);

    // Kiểm tra và hiển thị thông tin truyện đã đọc
    if ($result_read_story && mysqli_num_rows($result_read_story) > 0) {
        $row_story = mysqli_fetch_assoc($result_read_story);
?>
                    <a href="index.php?quanly=doc&id_truyen=<?php echo $row_story['id_truyen']; ?>&id_chuong=<?php echo $row_story['id_chuong_doc']; ?>" class="acd2">Đọc tiếp</a>
   
<?php
    }
} elseif (isset($_COOKIE['user_id'])) {
    // Code xử lý cookie tương tự như trên (nếu cần)
}
?>
<?php
// Check if there is an active session with id_user
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
?>
<button class="acd3" onclick="submitNomination('<?php echo $id_truyen; ?>');"><i class="fa-solid fa-heart"></i> Đề cử </button>
<script>
   function submitNomination(idTruyen) {
    if (confirm('Bạn có muốn đề cử truyện này không?')) {
        // Khởi tạo XMLHttpRequest
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'pages/decu.php', true); // Đường dẫn đến file PHP xử lý đề cử
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        // Cài đặt callback khi yêu cầu thành công
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                // Xử lý kết quả trả về ở đây
                if (xhr.responseText === 'success') {
                    alert('Cảm ơn bạn đã đề cử truyện!');
                    // Cập nhật thông tin trên UI nếu cần
                } else {
                    alert('Đã có lỗi xảy ra: ' + xhr.responseText);
                }
            } else {
                alert('Đã có lỗi xảy ra khi gửi yêu cầu');
            }
        };

        // Gửi yêu cầu với dữ liệu
        xhr.send('id_truyen=' + encodeURIComponent(idTruyen));
    } else {
        alert('Đã hủy đề cử.');
    }
}
</script>







                        <a href="#" class="acd4" onclick="toggleForm();"><i class="fa-solid fa-star"></i> Đánh giá</a>


              
                        <form id="danhGiaForm" action="index.php?quanly=danhgia&id_truyen=<?php echo $id_truyen; ?>" method="post" class="hidden-form">    <!-- Các trường đánh giá -->
    <div class="review-container">
    <button type="button" id="close-btn" onclick="toggleForm()">x</button>

        <h2>Đánh giá truyện</h2>
        <!-- Tính Cách Nhân Vật -->
        <label for="tinhcach">Tính Cách Nhân Vật:</label>
        <input type="range" class="danhgia123" name="tinhcach" min="0" max="5" step="0.5" value="0">
        <span id="tinhcach-value">0</span><br>

        <!-- Đánh giá nội dung cốt truyện -->
        <label for="cottruyen">Nội Dung Cốt Truyện:</label>
        <input type="range" class="danhgia123" name="cottruyen" min="0" max="5" step="0.5" value="0">
        <span id="cottruyen-value">0</span><br>

        <!-- Đánh giá bố cục thế giới -->
        <label for="bocuc">Bố Cục Thế Giới:</label>
        <input type="range" class="danhgia123" name="bocuc" min="0" max="5" step="0.5" value="0">
        <span id="bocuc-value">0</span><br>

        <!-- Đánh giá chất lượng bản dịch -->
        <label for="chatluong">Chất Lượng Bản Dịch:</label>
        <input type="range" class="danhgia123" name="chatluong" min="0" max="5" step="0.5" value="0">
        <span id="chatluong-value">0</span><br>

        <!-- Nội dung đánh giá -->
        <label for="noidungdg">Nội Dung Đánh Giá:</label>
        <textarea name="noidung" rows="4" cols="50" required></textarea><br>

        <button type="button" class="submit-button" onclick="kiemTraDangNhap()">Đánh Giá</button>    </div>
    
</form>
<button class="mmdskd123" onclick="shareContent()">Chia sẻ   <i class="fas fa-share-alt"></i></button>

<script>
function shareContent() {
    const url = window.location.href;
    const title = document.title;
    const text = 'Hãy xem trang này:';

    if (navigator.share) {
        navigator.share({
            title: title,
            text: text,
            url: url
        }).then(() => {
            console.log('Đã chia sẻ thành công');
        }).catch((error) => {
            console.error('Lỗi khi chia sẻ: ', error);
        });
    } else {
        // Sao chép vào clipboard nếu Web Share API không được hỗ trợ
        navigator.clipboard.writeText(url).then(() => {
            alert('Đường dẫn đã được sao chép!');
        }).catch(err => {
            console.error('Lỗi khi sao chép đường dẫn: ', err);
        });
    }
}
</script>

                  
<script>
function kiemTraDangNhap() {
    // Kiểm tra nếu có session id_user
    <?php if (isset($_SESSION['id_user'])) { ?>
        // Nếu đã đăng nhập, submit form
        document.getElementById("danhGiaForm").submit();
    <?php } else { ?>
        // Nếu chưa đăng nhập, hiển thị hộp thoại xác nhận
        var xacNhan = confirm('Bạn cần đăng nhập để đánh giá. Bạn có muốn đăng nhập không?');
        
        // Nếu người dùng ấn OK, chuyển hướng đến trang đăng nhập
        if (xacNhan) {
            window.location.href = 'index.php?quanly=dangnhap';
        }
        // Nếu người dùng ấn Cancel, không làm gì cả
    <?php } ?>
}
</script>



                    </div>
                </div>
                <div class="fkdkfdk2">
                <a href="index.php?quanly=yeuthich&id_truyen=<?php echo $id_truyen; ?>" class="hsdhdsh"><i class="fa-regular fa-heart"></i></a><br>
                <a href="#" class="hsdhdsh"><i class="fa-regular fa-bookmark"></i></a>


                </script>
                </div>
            </div>
            <?php
// Kết nối cơ sở dữ liệu


// Truy vấn số lượng chương
$result = $mysqli->query("SELECT COUNT(*) as total_chuong FROM tbl_chuong WHERE id_truyen = $id_truyen");
$row = $result->fetch_assoc();
$total_chuong = $row['total_chuong'];

// Truy vấn số lượng bình luận
$result = $mysqli->query("SELECT COUNT(*) as total_binhluan FROM tbl_binhluan WHERE id_truyen = $id_truyen");
$row = $result->fetch_assoc();
$total_binhluan = $row['total_binhluan'];

// Truy vấn số lượng đánh giá
$result = $mysqli->query("SELECT COUNT(*) as total_danhgia FROM tbl_danhgia WHERE id_truyen = $id_truyen");
$row = $result->fetch_assoc();
$total_danhgia = $row['total_danhgia'];

// Đóng kết nối
?>
            <div class="container-mt-4">
        <ul class="nav-tabs" id="myTabs">
            <li class="nav-item-axc">
                <a class="nav-link-axc" id="gioi-thieu-tab" href="#gioi-thieu" onclick="openTab('gioi-thieu')">Giới Thiệu</a>
            </li>
            <li class="nav-item-axc">
    <a class="nav-link-axc" id="chuong-tab" href="#chuong" onclick="openTab('chuong')">
        Chương <span class="badge"><?php echo $total_chuong; ?></span>
    </a>
</li>
<li class="nav-item-axc">
    <a class="nav-link-axc" id="binh-luan-tab" href="#binh-luan" onclick="openTab('binh-luan')">
        Bình Luận <span class="badge"><?php echo $total_binhluan; ?></span>
    </a>
</li>
<li class="nav-item-axc">
    <a class="nav-link-axc" id="danh-gia-tab" href="#danh-gia" onclick="openTab('danh-gia')">
        Đánh giá <span class="badge"><?php echo $total_danhgia; ?></span>
    </a>
</li>
        </ul>
            </div>
        <div class="tab-content">
<?php

            // Truy vấn lấy thông tin giới thiệu và số chương
            $queryInfoAndChapter = "SELECT 
            truyen.id_truyen,
            truyen.tieude,
            truyen.hinhanh,
            truyen.tomtat,
            truyen.ngaydang,
            user.id_user AS id_tacgia,
            user.tenuser AS tentacgia,
            theloai.tentheloai,
            COUNT(chuong.id_chuong) AS sochuong
        FROM
            tbl_truyen truyen
        INNER JOIN
            tbl_user user ON truyen.id_admin = user.id_user
        INNER JOIN
            tbl_truyen_theloai tt ON truyen.id_truyen = tt.id_truyen
        INNER JOIN
            tbl_theloai theloai ON tt.id_theloai = theloai.id_theloai
        LEFT JOIN
            tbl_chuong chuong ON truyen.id_truyen = chuong.id_truyen
        WHERE
            truyen.id_truyen = $id_truyen
        GROUP BY
            truyen.id_truyen,
            truyen.tieude,
            truyen.hinhanh,
            truyen.tomtat,
            truyen.ngaydang,
            user.id_user,
            user.tenuser,
            theloai.tentheloai
        ORDER BY
            truyen.ngaydang DESC
        LIMIT 1;
";


            // Thực hiện truy vấn và lấy dữ liệu
            $resultInfoAndChapter = $mysqli->query($queryInfoAndChapter);

            // Kiểm tra và hiển thị thông tin giới thiệu và số chương
            if ($resultInfoAndChapter && $resultInfoAndChapter->num_rows > 0) {
                $rowInfoAndChapter = $resultInfoAndChapter->fetch_assoc();

                // Hiển thị thông tin giới thiệu
                echo '<div class="tab-pane fade" id="gioi-thieu">';
                echo '<h3 class="h3-acd">Giới Thiệu Truyện</h3>';
                echo '<p class="acd">' .nl2br($rowInfoAndChapter['tomtat']) . '</p>';
                echo '</div>';

                // Hiển thị số chương
            }






            $queryChuong = "SELECT 
            chuong.sochuong,
            chuong.tenchuong,
            chuong.id_chuong,
            chuong.is_locked,
            chuong.chuong_gold
        FROM
            tbl_chuong chuong
        WHERE
            chuong.id_truyen = $id_truyen
        ORDER BY
            chuong.sochuong";
        
        $resultChuong = $mysqli->query($queryChuong);
        
        // Kiểm tra và hiển thị danh sách chương
        if ($resultChuong && $resultChuong->num_rows > 0) {
            echo '<div class="tab-pane fade" id="chuong">';
            echo '<h3 class="h3-acd">Danh Sách Chương</h3>';
            echo '<ul class="ul-acd">';
        
            // Lấy ID người dùng từ session nếu đã đăng nhập
            $userId = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
        
            while ($rowChuong = $resultChuong->fetch_assoc()) {
                $id_chuong = $rowChuong['id_chuong'];
                $isLocked = $rowChuong['is_locked'];
                $chuongGold = $rowChuong['chuong_gold'];
                $hasAccess = false;
                if ($isLocked == 1) {
                    if ($userId) {
                        // Truy vấn để kiểm tra xem người dùng đã mua quyền truy cập chương này chưa
                        $checkAccessQuery = "SELECT 1 FROM user_chapter_access WHERE user_id = ? AND id_chuong = ?";
                        $stmt = $mysqli->prepare($checkAccessQuery);
                        $stmt->bind_param("ii", $userId, $id_chuong);
                        $stmt->execute();
                        $stmt->store_result();
                        $hasAccess = $stmt->num_rows > 0;
                    }
        
                    if ($hasAccess) {
                        // Người dùng đã mua chương này, hiển thị liên kết để đọc
                        echo '<li class="li-acd"><a href="index.php?quanly=doc&id_truyen=' . $id_truyen . '&id_chuong=' . $id_chuong . '">' . 'Chương ' . $rowChuong['sochuong'] . ': ' . $rowChuong['tenchuong'] . '</a></li>';
                    } else {
                        // Người dùng chưa mua chương này
                        if ($chuongGold > 0) {
                            echo '<li class="li-acd"> <a href="#" onclick="confirmUnlockChapter(' . $id_chuong . ', ' . $chuongGold . '); return false;">' . '<p>Chương ' . $rowChuong['sochuong'] . ': ' . $rowChuong['tenchuong'] . '  </p>  <span class="gold-value"><i class="fa-solid fa-lock"></i> ' . $chuongGold . ' Gold</span></a></li>';
                        } else {
                            echo '<li class="li-acd"> <a href="#" onclick="alert(\'Không đủ GOLD để mở khóa chương này.\'); return false;">' . 'Chương ' . $rowChuong['sochuong'] . ': ' . $rowChuong['tenchuong'] . '    <span class="gold-value"><i class="fa-solid fa-lock"></i></span></a></li>';
                        }
                    }
                } else {
                    // Chương không bị khóa
                    echo '<li class="li-acd"><a href="index.php?quanly=doc&id_truyen=' . $id_truyen . '&id_chuong=' . $id_chuong . '">' . 'Chương ' . $rowChuong['sochuong'] . ': ' . $rowChuong['tenchuong'] . '</a></li>';
                }
            }
        
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<div class="tab-pane fade" id="chuong">';
            echo '<h3 class="h3-acd">Danh Sách Chương</h3>';
            echo '<p class="p-acd">Không có chương nào.</p>';
            echo '</div>';
        }
        
        // Đoạn mã JavaScript để xử lý mở khóa chương
// Phía server-side (PHP), bạn cần xác định người dùng đã đăng nhập chưa
$isUserLoggedIn = isset($_SESSION['id_user']); // Giả sử bạn lưu ID người dùng trong session
echo '<script type="text/javascript">
var isUserLoggedIn = ' . json_encode($isUserLoggedIn) . ';
function confirmUnlockChapter(chapterId, chuongGold) {
    if (!isUserLoggedIn) {
        alert("Bạn cần đăng nhập để mở khóa chương này.");
        window.location.href = "index.php?quanly=dangnhap"; // Chuyển đến trang đăng nhập
        return;
    }

    var userConfirmed = confirm("Cần " + chuongGold + " GOLD để mở khóa chương này!");

    if (userConfirmed) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "pages/unlock_chapter.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var response = this.responseText;
                if(response == "success") {
                    alert("Chương đã được mở khóa.");
                    location.reload();  // Tải lại trang để cập nhật thông tin
                } else {
                    var userChoice = confirm("Bạn không đủ GOLD. Bạn có muốn mua thêm không?");
                    if (userChoice) {
                        window.location.href = "index.php?quanly=muagold";  // Chuyển đến trang mua gold
                    }
                }
            }
        };
        xhr.send("id_chuong=" + chapterId);
    } else {
        console.log("User canceled the unlock operation.");
    }
}
</script>';
        

// Thêm mã JavaScript
echo '<div class="tab-pane fade" id="binh-luan">';

// Truy vấn lấy danh sách bình luận
echo '<h3>Bình Luận</h3>';
$result = $mysqli->query("SELECT COUNT(*) as total_binhluan FROM tbl_binhluan WHERE id_truyen = $id_truyen");
$row = $result->fetch_assoc();
$total_binhluan = $row['total_binhluan'];
$id_chuongs=1;
// Truy vấn lấy danh sách bình luận
echo '<form id="comment-form" method="post">
<label for="comment">Nhập bình luận của bạn:</label>
<textarea name="comment" id="comment" rows="4" required></textarea>
<input type="hidden" name="id_truyen" id="id_truyen" value="<?php echo $id_truyen; ?>">
<input type="hidden" name="id_chuong" id="id_chuong" value="<?php echo $id_chuongs; ?>">

<input type="button" id="submit-comment" value="Đăng Bình Luận">
</form>
<p id="login-prompt" style="display: none;">Vui lòng <a href="index.php?quanly=dangnhap">đăng nhập</a> để bình luận.</p>
<!-- Bên dưới div comment-list -->

<h3 class="theh">' . $row['total_binhluan'] . ' Thảo luận </h3>
<div id="comment-list"></div>
<button id="load-more-comments" type="button">Xem thêm</button>
<div id="pagination"></div></div>'; // Thêm div để hiển thị danh sách bình luận

// Hiển thị biểu mẫu đánh giá
// ... (mã PHP hiện tại của bạn)

 echo '<div class="tab-pane fade" id="danh-gia">';
?>







<form action="" method="post">
    <div class="review-container">

<!-- Hiển thị ngôi sao và điểm tương ứng -->
<div class="star-container">
    
<?php
// ID của truyện hoặc người dùng (thay đổi giá trị này tùy thuộc vào truyện hoặc người dùng cụ thể)

// Truy vấn SQL để lấy thông tin đánh giá
$sql_danhgia = "SELECT tinhcach, cottruyen, bocuc, chatluong, tongdiem
                FROM tbl_danhgia 
                WHERE id_truyen = $id_truyen";

$result_danhgia = $mysqli->query($sql_danhgia);

// Khởi tạo mảng đánh giá
$ratings = array();

// Lấy dữ liệu từ cơ sở dữ liệu và đưa vào mảng đánh giá
while ($row_danhgia = $result_danhgia->fetch_assoc()) {
    $ratings[] = $row_danhgia;
}
$sql_ten_truyen = "SELECT tieude FROM tbl_truyen WHERE id_truyen = $id_truyen";
$result_ten_truyen = $mysqli->query($sql_ten_truyen);

if ($result_ten_truyen->num_rows > 0) {
    $row_ten_truyen = $result_ten_truyen->fetch_assoc();
    $ten_truyen = $row_ten_truyen['tieude'];

    echo '<h2 class="acd">' . $ten_truyen . '</h2>';
} else {
    echo "Không có thông tin truyện.";
}
// Hiển thị thông tin đánh giá trong form
if (!empty($ratings)) {
    // Lấy trung bình của từng mục đánh giá
    $avg_tinhcach = round(array_sum(array_column($ratings, 'tinhcach')) / count($ratings), 2);
    $avg_cottruyen = round(array_sum(array_column($ratings, 'cottruyen')) / count($ratings), 2);
    $avg_bocuc = round(array_sum(array_column($ratings, 'bocuc')) / count($ratings), 2);
    $avg_chatluong = round(array_sum(array_column($ratings, 'chatluong')) / count($ratings), 2);
    $avg_tongdiem = round(array_sum(array_column($ratings, 'tongdiem')) / count($ratings), 2);

    // Hiển thị ngôi sao và điểm tương ứng cho từng mục đánh giá
    $maxStars = 5;

    echo '<div class="star-container">';
    echo '<p>Tính Cách Nhân Vật: <span id="tinhcach-value">' . $avg_tinhcach . '</span> sao</p>';
    $visibleStars = round($avg_tinhcach / $maxStars * $maxStars);
    for ($i = 1; $i <= $maxStars; $i++) {
        $starClass = ($i <= $visibleStars) ? 'fa-solid' : 'fa-regular';
        $starColor = ($i <= $visibleStars) ? '#ffc000' : '#ffebaf';
        echo '<i class="fa-star ' . $starClass . '" style="color: ' . $starColor . ';"></i>';
    }
    echo '</div>';

    echo '<div class="star-container">';
    echo '<p>Nội Dung Cốt Truyện: <span id="cottruyen-value">' . $avg_cottruyen . '</span> sao</p>';
    $visibleStars = round($avg_cottruyen / $maxStars * $maxStars);
    for ($i = 1; $i <= $maxStars; $i++) {
        $starClass = ($i <= $visibleStars) ? 'fa-solid' : 'fa-regular';
        $starColor = ($i <= $visibleStars) ? '#ffc000' : '#ffebaf';
        echo '<i class="fa-star ' . $starClass . '" style="color: ' . $starColor . ';"></i>';
    }
    echo '</div>';

    // Tiếp tục tương tự cho các mục đánh giá khác...
    // ... (Phần mã trước đó)

echo '<div class="star-container">';
echo '<p>Bố Cục Thế Giới: <span id="bocuc-value">' . $avg_bocuc . '</span> sao</p>';
$visibleStars = round($avg_bocuc / $maxStars * $maxStars);
for ($i = 1; $i <= $maxStars; $i++) {
    $starClass = ($i <= $visibleStars) ? 'fa-solid' : 'fa-regular';
    $starColor = ($i <= $visibleStars) ? '#ffc000' : '#ffebaf';
    echo '<i class="fa-star ' . $starClass . '" style="color: ' . $starColor . ';"></i>';
}
echo '</div>';

echo '<div class="star-container">';
echo '<p>Chất Lượng Bản Dịch: <span id="chatluong-value">' . $avg_chatluong . '</span> sao</p>';
$visibleStars = round($avg_chatluong / $maxStars * $maxStars);
for ($i = 1; $i <= $maxStars; $i++) {
    $starClass = ($i <= $visibleStars) ? 'fa-solid' : 'fa-regular';
    $starColor = ($i <= $visibleStars) ? '#ffc000' : '#ffebaf';
    echo '<i class="fa-star ' . $starClass . '" style="color: ' . $starColor . ';"></i>';
}
echo '</div>';

echo '<div class="star-container">';
echo '<p>Tổng Điểm: <span id="tongdiem-value">' . $avg_tongdiem . '</span> sao</p>';
$visibleStars = round($avg_tongdiem / $maxStars * $maxStars);
for ($i = 1; $i <= $maxStars; $i++) {
    $starClass = ($i <= $visibleStars) ? 'fa-solid' : 'fa-regular';
    $starColor = ($i <= $visibleStars) ? '#ffc000' : '#ffebaf';
    echo '<i class="fa-star ' . $starClass . '" style="color: ' . $starColor . ';"></i>';
}
echo '</div>';

// ... (Phần mã sau đó)

} else {
    echo "Không có đánh giá.";
}

// Đóng kết nối
?>



</div>

    </div>
    
</form>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var submitButton = document.getElementById('submit-comment');
    var commentInput = document.getElementById('comment');
    var loadMoreButton = document.getElementById('load-more-comments');
    var idTruyen = <?php echo json_encode($id_truyen); ?>;
    var idChuong = <?php echo json_encode($id_chuongs); ?>;

    var offset = 0; // Khởi tạo offset ban đầu là 0
    var limit = 5; // Số lượng bình luận cần tải mỗi lần

    submitButton.addEventListener('click', function() {
        var commentContent = commentInput.value.trim();

        if (commentContent !== '') {
            // Gửi yêu cầu Ajax để đăng bình luận
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'pages/submit_comment.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        loadComments(idTruyen, idChuong, 0); // Load lại từ đầu
                        commentInput.value = ''; // Xóa nội dung trong textarea
                        offset = 0; // Reset offset khi đăng bình luận mới
                    } else {
                        alert(response.message);
                    }
                }
            };
            // Thêm id_chuong vào yêu cầu gửi đi
            xhr.send('id_truyen=' + idTruyen + '&id_chuong=' + idChuong + '&comment=' + encodeURIComponent(commentContent));
        }
    });
    loadMoreButton.addEventListener('click', function() {
    loadComments(idTruyen, idChuong, offset);
});

function loadComments(idTruyen, idChuong, currentOffset) {
    var commentListContainer = document.getElementById('comment-list');
    if (currentOffset === 0) {
        commentListContainer.innerHTML = ''; // Clear comments if loading from start
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'pages/comment.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            var comments = JSON.parse(xhr.responseText);
            comments.forEach(function(comment) {
                var li = document.createElement('li');
                li.className = 'li-acfs';

                var divAvatar = document.createElement('div');
                divAvatar.className = 'comment-avatar';
                var img = document.createElement('img');

                // Kiểm tra xem comment.user_avatar có phải là một URL hay không
                if (/^(http|https):\/\/.+$/.test(comment.user_avatar)) {
                    // Nếu là URL, sử dụng đường dẫn trực tiếp
                    img.src = comment.user_avatar;
                } else {
                    // Nếu không phải là URL, sử dụng đường dẫn tương đối
                    img.src = './assets/image/' + comment.user_avatar;
                }

                img.alt = 'Avatar';
                img.className = 'rounded-circle';
                img.width = '30';
                divAvatar.appendChild(img);

                var divContent = document.createElement('div');
                divContent.className = 'comment-content';

                var pUser = document.createElement('p');
                pUser.className = 'comment-info';
                pUser.textContent = comment.user_tenuser;

                // Tạo một phần tử <span> mới để hiển thị thông tin chương
                var spanChapter = document.createElement('span');
                spanChapter.className = 'comment-chapter';
                spanChapter.textContent = ' Chương: ' + comment.id_chuong; // Hiển thị thông tin chương
                spanChapter.style.marginLeft = '5px'; // Tạo khoảng cách giữa tên người dùng và thông tin chương

                // Thêm thông tin chương sau tên người dùng
                pUser.appendChild(spanChapter);

                var pTime = document.createElement('p');
                pTime.className = 'comment-time';
                pTime.textContent = comment.binhluan_ngay;

                var pContent = document.createElement('p');
                pContent.className = 'comment-text';
                pContent.textContent = comment.binhluan_noidung;

                divContent.appendChild(pUser);
                divContent.appendChild(pTime);
                divContent.appendChild(pContent);

                li.appendChild(divAvatar);
                li.appendChild(divContent);

                commentListContainer.appendChild(li);
            });

            if (comments.length === limit) {
                offset += limit; // Only update offset if full set of comments was loaded
            }
        } else if (xhr.readyState === XMLHttpRequest.DONE) {
            alert('Đã có lỗi xảy ra khi tải danh sách bình luận.');
        }
    };
    // Thêm id_chuong vào yêu cầu gửi đi
    xhr.send('id_truyen=' + idTruyen + '&id_chuong=' + idChuong + '&offset=' + currentOffset + '&limit=' + limit);
}


    // Tải danh sách bình luận ban đầu khi trang được tải
    loadComments(idTruyen, idChuong, offset);
});
</script>
<script>
function toggleForm() {
    const reviewForm = document.querySelector('.hidden-form');
    reviewForm.classList.toggle('visible');
}

const rangeInputs = document.querySelectorAll('.danhgia123');
rangeInputs.forEach(input => {
    input.addEventListener('input', updateRangeValue);
});

function updateRangeValue(event) {
    const input = event.target;
    const span = document.getElementById(input.name + '-value');
    span.textContent = input.value;
}

// Thêm sự kiện click cho document
document.addEventListener('click', function(event) {
    const reviewForm = document.querySelector('.hidden-form');

    // Kiểm tra xem phần tử được click có nằm trong form hoặc là button đánh giá hay không
    if (!reviewForm.contains(event.target) && event.target.className !== 'acd4') {
        // Nếu không nằm trong form và không phải là button đánh giá, đóng form
        reviewForm.classList.remove('visible');
    }
});

</script>


<?php
$queryDanhGia = "SELECT 
                    danhgia.id_danhgia,
                    danhgia.id_truyen,
                    danhgia.id_user,
                    danhgia.tinhcach,
                    danhgia.cottruyen,
                    danhgia.bocuc,
                    danhgia.chatluong,
                    danhgia.tongdiem,
                    danhgia.noidung AS danhgia_noidung,
                    danhgia.ngaydanhgia AS danhgia_ngay,
                    users.id_user,
                    users.tenuser,
                    users.email,
                    users.matkhau,
                    users.avatar
                 FROM
                    tbl_danhgia danhgia
                 INNER JOIN
                    tbl_user users ON danhgia.id_user = users.id_user
                 WHERE
                    danhgia.id_truyen = $id_truyen
                 ORDER BY
                    danhgia.ngaydanhgia DESC";



// Thực hiện truy vấn và lấy dữ liệu
$resultDanhGia = $mysqli->query($queryDanhGia);

// Kiểm tra và hiển thị danh sách đánh giá
if ($resultDanhGia && $resultDanhGia->num_rows > 0) {
    ?>



    <?php
    echo '<h3 class="h3-ghj">Đánh Giá</h3>';
    echo '<ul class="ul-acfs">';
    date_default_timezone_set('UTC');
    while ($rowDanhGia = $resultDanhGia->fetch_assoc()) {
        $ngayBinhLuan = strtotime($rowDanhGia['danhgia_ngay']); // Giả sử 'danhgia_ngay' là UTC
        $timeDiff = time() - $ngayBinhLuan; // time() cũng sẽ là UTC
    
        if ($timeDiff < 0) {
            $timeAgo = "Vừa xong"; // Đối phó với trường hợp thời gian âm
        } elseif ($timeDiff < 60) {
            $timeAgo = $timeDiff . " giây trước";
        } elseif ($timeDiff < 3600) {
            $timeAgo = floor($timeDiff / 60) . " phút trước";
        } elseif ($timeDiff < 86400) {
            $timeAgo = floor($timeDiff / 3600) . " giờ trước";
        } else {
            $timeAgo = floor($timeDiff / 86400) . " ngày trước";
        }
        echo '<li class="li-acfs">';
        echo '<div class="comment-avatar">';
        if (filter_var($rowDanhGia['avatar'], FILTER_VALIDATE_URL)) {
            // Nếu là một URL, sử dụng đường dẫn trực tiếp
            echo '<img src="' . $rowDanhGia['avatar'] . '" alt="Avatar" class="rounded-circle" width="30">';
        } else {
            // Nếu không phải là URL, sử dụng đường dẫn tương đối
            echo '<img src="./assets/image/' . $rowDanhGia['avatar'] . '" alt="Avatar" class="rounded-circle" width="30">';
        }        echo '</div>';
        echo '<div class="comment-content">';
        echo '<p class="comment-info">' . htmlspecialchars($rowDanhGia['tenuser'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p class="comment-time">' . $timeAgo . '</p>';
        echo '<p class="comment-text">' . htmlspecialchars($rowDanhGia['danhgia_noidung'], ENT_QUOTES, 'UTF-8') . '</p>';
        echo '<p class="comment-time">Điểm: ' . htmlspecialchars($rowDanhGia['tongdiem'], ENT_QUOTES, 'UTF-8') . ' Sao</p><br>';
        echo '</div>';
        echo '</li>';
    }
    echo '</ul>';
    echo '</div>';
} else {
    ?>

    <?php
    echo '<div class="tab-pane fade" id="danh-gia">';
    echo '<h3 class="h3-ghj my-3">Đánh Giá</h3>';
    echo '<p class="p-ghj mb-4">Không có đánh giá nào.</p>';
    echo '</div>';
}


// ... (mã PHP hiện tại của bạn tiếp tục)

// ... (mã PHP hiện tại của bạn tiếp tục)

?>
        </div>
<?php
        }
    } else {
        echo "Không có dữ liệu.";
    }

    // Đóng kết nối
?>

</div>
    </div>
</main>
<style>
/* Tạo một class mới cho mục được chọn */
.nav-link-axc.active {
    color: #232323; /* Đổi màu chữ thành màu đỏ */
    background-color: #ccc;
}
</style>

<script>
function openTab(tabName) {
    var tabs = document.querySelectorAll('.nav-link-axc');
    tabs.forEach(function (tab) {
        tab.classList.remove('active');
    });

    document.getElementById(tabName + '-tab').classList.add('active');

    var tabContents = document.querySelectorAll('.tab-pane');
    tabContents.forEach(function (content) {
        content.classList.remove('active');
    });

    document.getElementById(tabName).classList.add('active');
}

// Mở tab giới thiệu khi trang web được tải
document.addEventListener('DOMContentLoaded', function () {
    openTab('gioi-thieu');
});
</script>

