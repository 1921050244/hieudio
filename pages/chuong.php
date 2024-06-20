<?php
if (!isset($_SESSION['read_truyen'])) {
    $_SESSION['read_truyen'] = [];
}

if (isset($_GET['id_truyen'])) {
    // Lấy giá trị id_truyen từ URL
    $id_truyen = $_GET['id_truyen'];

    // Kiểm tra xem id_truyen đã có trong session chưa
    if (!in_array($id_truyen, $_SESSION['read_truyen'])) {
        // Cập nhật lượt đọc
        $queryUpdateLuotDoc = "UPDATE tbl_truyen SET luotdoc = luotdoc + 1 WHERE id_truyen = $id_truyen";
        $resultUpdateLuotDoc = $mysqli->query($queryUpdateLuotDoc);

        // Thêm id_truyen vào session để đánh dấu đã đọc
        $_SESSION['read_truyen'][] = $id_truyen;
    }
}
// Kết nối đến CSDL (Giả sử bạn đã kết nối rồi)
// ...

// Lấy thông tin từ form hoặc các biến khác
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
$id_truyen = isset($_GET['id_truyen']) ? $_GET['id_truyen'] : null;
$id_chuong = isset($_GET['id_chuong']) ? $_GET['id_chuong'] : null;

// Kiểm tra xem đã có bản ghi cho người dùng, truyện và chưa
if ($id_user !== null && $id_truyen !== null && $id_chuong !== null) {
    $query_check = "SELECT id FROM tbl_reading_status WHERE id_user = $id_user AND id_truyen = $id_truyen";
    $result_check = mysqli_query($mysqli, $query_check);

    if ($result_check && mysqli_num_rows($result_check) > 0) {
        // Nếu đã tồn tại, cập nhật chỉ id_chuong
        $query_update = "UPDATE tbl_reading_status SET id_chuong = $id_chuong WHERE id_user = $id_user AND id_truyen = $id_truyen";
        mysqli_query($mysqli, $query_update);
    } else {
        // Nếu chưa tồn tại, thêm mới bản ghi
        $query_insert = "INSERT INTO tbl_reading_status (id_user, id_truyen, id_chuong) VALUES ($id_user, $id_truyen, $id_chuong)";
        mysqli_query($mysqli, $query_insert);
    }
}

// Lưu vào cookie nếu có id_user
if ($id_user !== null) {
    $cookie_name = "user_id";
    $cookie_value = $id_user;
    $expire = time() + (30 * 24 * 60 * 60); // Hết hạn sau 30 ngày
    setcookie($cookie_name, $cookie_value, $expire, "/");
}

?>


<?php
if (isset($_GET['id_truyen']) && isset($_GET['id_chuong'])) {
    // Lấy giá trị id_truyen và id_chuong từ URL
    $id_truyen = $_GET['id_truyen'];
    $id_chuong = $_GET['id_chuong'];
}


// Truy vấn lấy thông tin về truyện và chương
$query = "
    SELECT 
        truyen.id_truyen,
        truyen.tieude,
        chuong.id_chuong,
        chuong.tenchuong,
        chuong.thoigian,

        chuong.noidung,
        chuong.is_locked,
        chuong.chuong_gold,


        chuong.sochuong
    FROM
        tbl_truyen truyen
    INNER JOIN
        tbl_chuong chuong ON truyen.id_truyen = chuong.id_truyen
    WHERE
        truyen.id_truyen = $id_truyen AND chuong.id_chuong = $id_chuong
    LIMIT 1;
";

// Thực hiện truy vấn và lấy dữ liệu
$result = $mysqli->query($query);

// Bắt đầu thẻ div chứa toàn bộ nội dung
echo '<div class="content-container">';
echo '<div class="contents-container">';

echo '<div id="settingsPanel" class="settings-panel">';
echo '<button id="closeBtn" class="close-btn">&times;</button>';
echo '<h3>Cài đặt tùy chỉnh</h3>';

// Trường cài đặt cỡ chữ
echo '<label for="fontsize">Cỡ chữ:</label>';
echo '<select id="fontsize">';
echo '<option value="12">12</option>';
echo '<option value="14">14</option>';
echo '<option value="16">16</option>';
echo '<option value="18">18</option>';
echo '<option value="20">20</option>';
echo '<option value="22">22</option>';
echo '<option value="24">24</option>';
echo '<option value="26">26</option>';
echo '<option value="28">28</option>';
echo '<option value="30">30</option>';
echo '</select>';

// Trường cài đặt font chữ
echo '<label for="fontfamily">Font chữ:</label>';
echo '<select id="fontfamily">';
echo '<option value="Arial, sans-serif">Arial</option>';
echo '<option value="Times New Roman, serif">Times New Roman</option>';
echo '<option value="Courier New, monospace">Courier New</option>';
echo '<option value="Oswald, sans-serif">Oswald</option>';
echo '<option value="Inter, sans-serif">Inter</option>';
echo '<option value="Georgia, serif">Georgia</option>';
echo '<option value="Verdana, sans-serif">Verdana</option>';
echo '<option value="Tahoma, sans-serif">Tahoma</option>';
echo '<option value="Roboto, sans-serif">Roboto</option>';
echo '<option value="Helvetica, sans-serif">Helvetica</option>';
echo '</select>';

// Trường cài đặt độ dài dòng
echo '<label for="lineheight">Độ dài dòng:</label>';
echo '<select id="lineheight">';
echo '<option value="1.0">100%</option>';
echo '<option value="1.2">120%</option>';
echo '<option value="1.4">140%</option>';
echo '<option value="1.6">160%</option>';
echo '<option value="1.8">180%</option>';
echo '<option value="2.0">200%</option>';
echo '<option value="2.2">220%</option>';
echo '<option value="2.4">240%</option>';
echo '</select>';

// Trường cài đặt màu nền
echo '<label for="backgroundcolor">Màu nền:</label>';
echo '<select id="backgroundcolor">';
echo '<option value="lightblue" style="background-color: lightblue; color: black;">Xanh nhạt</option>';
echo '<option value="lightyellow" style="background-color: lightyellow; color: black;">Vàng nhạt</option>';
echo '<option value="lightgray" style="background-color: lightgray; color: black;">Xám nhạt</option>';
echo '<option value="#232323" style="background-color: #232323; color: #ccc;">Đen</option>';
echo '<option value="white" style="background-color: white; color: black;">Trắng</option>';
echo '<option value="lavender" style="background-color: lavender; color: black;">Hoa oải hương</option>';
echo '<option value="#d0ffdcad" style="background-color: #d0ffdcad; color: black;">Xanh lá nhạt</option>';
echo '<option value="peachpuff" style="background-color: peachpuff; color: black;">Màu đào</option>';
echo '<option value="mintcream" style="background-color: mintcream; color: black;">Kem bạc hà</option>';
echo '<option value="aliceblue" style="background-color: aliceblue; color: black;">Xanh Alice</option>';
echo '</select>';

// Đóng thẻ div của settingsPanel
echo '</div>';

// Hiển thị thông tin về truyện và chương
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Thẻ div chứa thông tin truyện và chương
    echo '<div class="chapter-info">';
    
    // Hiển thị chương trước
    echo '<div class="prev-chapter">';
    if ($row['sochuong'] > 1) {
        // Nếu không phải là chương đầu tiên, lấy chương trước
        $prevChapterNumber = $row['sochuong'] - 1;
    
        // Truy vấn để lấy id_chuong của chương trước
        $queryPrevChapter = "
            SELECT id_chuong
            FROM tbl_chuong
            WHERE id_truyen = {$row['id_truyen']} AND sochuong = $prevChapterNumber
        ";
    
        $resultPrevChapter = $mysqli->query($queryPrevChapter);
        $rowPrevChapter = $resultPrevChapter->fetch_assoc();
    
        if ($rowPrevChapter) {
            echo "<a href='index.php?quanly=doc&id_truyen={$row['id_truyen']}&id_chuong={$rowPrevChapter['id_chuong']}'><i class='fas fa-arrow-left'></i> Trước</a>";
        } else {
            // Nếu không có chương trước, hiển thị liên kết với lớp disabled
            echo "<a class='disabled'><i class='fas fa-arrow-left'></i> Trước</a>";
        }
    }
    echo '</div>';
    
    // Hiển thị chương sau
    echo '<div class="next-chapter">';
    $nextChapterNumber = $row['sochuong'] + 1;
    
    // Truy vấn để lấy id_chuong của chương sau
    $queryNextChapter = "
        SELECT id_chuong
        FROM tbl_chuong
        WHERE id_truyen = {$row['id_truyen']} AND sochuong = $nextChapterNumber
    ";
    
    $resultNextChapter = $mysqli->query($queryNextChapter);
    $rowNextChapter = $resultNextChapter->fetch_assoc();
    
    if ($rowNextChapter) {
        echo "<a href='index.php?quanly=doc&id_truyen={$row['id_truyen']}&id_chuong={$rowNextChapter['id_chuong']}'>Sau <i class='fas fa-arrow-right'></i></a>";
    } else {
        // Nếu không có chương sau, hiển thị liên kết với lớp disabled
        echo "<a class='disabled'>Sau <i class='fas fa-arrow-right'></i></a>";
    }
    echo '</div>';
    
    
    // Hiển thị dropdown danh sách chương

    echo '<div class="settings-menu">';
    echo '<div id="settingsIcon" class="settings-icon" onclick="toggleSettingsPanel()">';
    echo '<i class="fa-solid fa-gear"></i> Tùy chỉnh';
    echo '</div>';
    
    echo '<div id="chapterIcon" class="chapter-icon" onclick="toggleChapterModal()">';
    echo '<i class="fa-solid fa-list-ul"></i> Mục lục';
    echo '</div>';
    
  
    if (isset($_SESSION['id_user'])) {
        $id_truyen = $row['id_truyen'];
        $id_chuong = $row['id_chuong'];
        $id_user = $_SESSION['id_user'];
    
        // Prepare the SELECT statement to check if the user has already bookmarked this chapter
        $stmt = $mysqli->prepare("SELECT * FROM tbl_danhdau WHERE id_truyen = ? AND id_chuong = ? AND id_user = ?");
        $stmt->bind_param("iii", $id_truyen, $id_chuong, $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            // User has already bookmarked this chapter
            echo '<div id="bookmarkIcon" class="bookmark-icon" onclick="removeBookmark(' . $id_truyen . ', ' . $id_chuong . ', ' . $id_user . ')">';
            echo '<i class="fa-solid fa-check"></i> Đã đánh dấu';
            echo '</div>';
        } else {
            // User has not bookmarked this chapter yet
            echo '<div id="bookmarkIcon" class="bookmark-icon" onclick="bookmarkChapter(' . $id_truyen . ', ' . $id_chuong . ', ' . $id_user . ')">';
            echo '<i class="fa-solid fa-bookmark"></i> Đánh dấu';
            echo '</div>';
        }
    
        $stmt->close();
    } else {
        // If the user is not logged in, prompt to log in
        echo '<div id="bookmarkIcon" class="bookmark-icon" onclick="alert(\'Vui lòng đăng nhập để đánh dấu.\');">';
        echo '<i class="fa-solid fa-bookmark"></i> Đánh dấu';
        echo '</div>';
    }
    
    
    echo '</div>'; // Kết thúc .settings-menu
    echo '<h2><a class="tentieude" href="index.php?quanly=thongtintruyen&id_truyen=' . $row['id_truyen'] . '">' . $row['tieude'] . '</a></h2>';
    echo '<h3 class="chuong_ten">Chương ' . $row['sochuong'] . ': ' . $row['tenchuong'] . '</h3>';
    echo '<i class="chuong_ten">Ngày cập nhật : ' . $row['thoigian'] .  '</i>';

    // Modal chứa danh sách chương
    echo '<div id="chapterModal" class="chapter-modal" style="display: none;">';
    echo '<div class="chapter-modal-content">';
    echo '<span class="close" onclick="toggleChapterModal()">&times;</span>'; // Nút đóng modal
    
    echo '<h2><a class="tentieude" href="index.php?quanly=thongtintruyen&id_truyen=' . $row['id_truyen'] . '">' . $row['tieude'] . '</a></h2>';
    echo '<h3 class="chuong_ten">Chương ' . $row['sochuong'] . ': ' . $row['tenchuong'] . '</h3>';
    
    // Thêm biểu tượng để chọn thứ tự chương
    echo '<div class="chapter-order-icons">';
    echo '<span class="order-icon" onclick="loadChapters(\'asc\')">&#x25B2;</span>'; // Icon lớn nhất
    echo '<span class="order-icon" onclick="loadChapters(\'desc\')">&#x25BC;</span>'; // Icon nhỏ nhất
    echo '</div>';
    
    // Container cho danh sách chương
    echo '<div id="chapterListContainer">';
    echo '<script>
    function loadChapters(order) {
        var data = {
            "id_truyen": '. $row['id_truyen'] .',
            "order": order,
            "current_chuong": '. $row['id_chuong'] .'
        };
    
        // Gửi yêu cầu AJAX để lấy danh sách chương
        $.ajax({
            url: "pages/getChapters.php", // Tệp PHP để lấy danh sách chương
            type: "post",
            data: data,
            success: function(response) {
                $("#chapterListContainer").html(response);
            },
            error: function(xhr, status, error) {
                alert("Đã xảy ra lỗi khi tải danh sách chương.");
            }
        });
    }
    
    // Tải danh sách chương ban đầu với thứ tự mặc định (nhỏ nhất)
    loadChapters("desc");
    </script>';    
    $id_truyen = intval($row['id_truyen']); // Sử dụng id_truyen từ $row
    $order = 'ASC'; // Mặc định sắp xếp từ mới nhất đến cũ nhất
    $current_chuong = intval($row['id_chuong']); // Sử dụng id_chuong từ $row
    
    // Truy vấn lấy danh sách chương
    $queryChapters = "
        SELECT id_chuong, tenchuong, sochuong
        FROM tbl_chuong
        WHERE id_truyen = $id_truyen
        ORDER BY sochuong $order;
    ";
    
    $resultChapters = $mysqli->query($queryChapters);
    
    if ($resultChapters && $resultChapters->num_rows > 0) {
        echo '<ul class="chapter-list">';
        while ($chapter = $resultChapters->fetch_assoc()) {
            $isCurrentChapter = ($chapter['id_chuong'] == $current_chuong);
            $chapterClass = $isCurrentChapter ? 'current-chapter' : 'chapter-item';
            echo '<li class="' . $chapterClass . '">';
            echo '<a href="index.php?quanly=doc&id_truyen=' . $id_truyen . '&id_chuong=' . $chapter['id_chuong'] . '">Chương ' . $chapter['sochuong'] . ': ' . $chapter['tenchuong'] . '</a>';
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<p>Chưa có chương nào.</p>';
    }
    
    echo '</div>'; // Kết thúc chapterListContainer
    
    echo '</div>'; // Kết thúc .chapter-modal-content
    echo '</div>'; // Kết thúc #chapterModal
    
    // JavaScript
    echo '<script>
    window.onload = function() {
        // Tải danh sách chương ban đầu với thứ tự mặc định (nhỏ nhất)
        loadChapters("desc");
    
        // Lấy vị trí của chương đang đọc
        var currentChapterPosition = document.querySelector(".current-chapter").offsetTop;
        // Cuộn đến vị trí của chương đang đọc
        document.querySelector("#chapterListContainer").scrollTop = currentChapterPosition;
    }
    </script>';
    
    // Thẻ div chứa nội dung chương
// Thẻ div chứa nội dung chương
echo '<div class="chapter-content">';
// ... các chỉ dẫn kết nối CSDL và khai báo biến

// Giả sử bạn đã lấy thông tin của chương (bao gồm trạng thái khóa và nội dung) và lưu trong $row
$id_chuong = $row['id_chuong'];
$is_locked = $row['is_locked'];
$noidung = $row['noidung'];
$chuongGold = $row['chuong_gold'];

$userHasAccess = false;

// Kiểm tra nếu người dùng đã đăng nhập
$user_id = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;

if ($is_locked && $user_id) {
    // Chương này bị khóa, kiểm tra xem người dùng đã mua truy cập chưa
    $queryAccess = "SELECT 1 FROM user_chapter_access WHERE user_id = ? AND id_chuong = ?";
    if ($stmt = $mysqli->prepare($queryAccess)) {
        $stmt->bind_param('ii', $user_id, $id_chuong);
        $stmt->execute();
        $stmt->store_result();
        $userHasAccess = ($stmt->num_rows > 0);
        $stmt->free_result();
        $stmt->close();
    }
}

// Nếu chương không bị khóa hoặc người dùng đã mua truy cập thì hiển thị nội dung
if (!$is_locked || $userHasAccess) {
    // Nếu chương không bị khóa hoặc người dùng đã mua truy cập
    echo '<div class="content-text">' . nl2br($noidung) . '</div>';
} else {
    // Nếu chương bị khóa và người dùng đã đăng nhập
    if ($user_id) {
        echo '<div class="content-text">Chương này đã bị khóa. ';
        if ($chuongGold > 0) {
            // Nếu có chi phí mở khóa (chuongGold > 0), hiển thị nút mở khóa
            echo '<a href="#" onclick="confirmUnlockChapter(' . $id_chuong . ', ' . $chuongGold . '); return false;">Click vào đây để mở khóa (' . $chuongGold . ' Gold)</a>';
        } else {
            // Nếu không có chi phí mở khóa, thông báo không đủ GOLD
            echo 'Không đủ GOLD để mở khóa chương này.</div>';
        }
    } else {
        // Nếu người dùng chưa đăng nhập, yêu cầu đăng nhập để mở khóa
        echo '<div class="content-text">Vui lòng <a href="index.php?quanly=dangnhap">đăng nhập</a> để mở khóa chương này.</div>';
    }
}


// ... tiếp tục với phần còn lại của mãecho '</div>';
echo '</div>';
echo '<script type="text/javascript">
function confirmUnlockChapter(chapterId, chuongGold) {
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

    
    // Hiển thị chương trước
   // Thẻ div chứa thông tin truyện và chương
    
   // Hiển thị chương trước
   echo '<div class="chapter-info">';
    
   // Hiển thị chương trước
   echo '<div class="prev-chapter">';
   if ($row['sochuong'] > 1) {
       // Nếu không phải là chương đầu tiên, lấy chương trước
       $prevChapterNumber = $row['sochuong'] - 1;
   
       // Truy vấn để lấy id_chuong của chương trước
       $queryPrevChapter = "
           SELECT id_chuong
           FROM tbl_chuong
           WHERE id_truyen = {$row['id_truyen']} AND sochuong = $prevChapterNumber
       ";
   
       $resultPrevChapter = $mysqli->query($queryPrevChapter);
       $rowPrevChapter = $resultPrevChapter->fetch_assoc();
   
       if ($rowPrevChapter) {
           echo "<a href='index.php?quanly=doc&id_truyen={$row['id_truyen']}&id_chuong={$rowPrevChapter['id_chuong']}'><i class='fas fa-arrow-left'></i> Trước</a>";
       } else {
           // Nếu không có chương trước, hiển thị liên kết với lớp disabled
           echo "<a class='disabled'><i class='fas fa-arrow-left'></i> Trước</a>";
       }
   }
   echo '</div>';
   
   // Hiển thị chương sau
   echo '<div class="next-chapter">';
   $nextChapterNumber = $row['sochuong'] + 1;
   
   // Truy vấn để lấy id_chuong của chương sau
   $queryNextChapter = "
       SELECT id_chuong
       FROM tbl_chuong
       WHERE id_truyen = {$row['id_truyen']} AND sochuong = $nextChapterNumber
   ";
   
   $resultNextChapter = $mysqli->query($queryNextChapter);
   $rowNextChapter = $resultNextChapter->fetch_assoc();
   
   if ($rowNextChapter) {
       echo "<a href='index.php?quanly=doc&id_truyen={$row['id_truyen']}&id_chuong={$rowNextChapter['id_chuong']}'>Sau <i class='fas fa-arrow-right'></i></a>";
   } else {
       // Nếu không có chương sau, hiển thị liên kết với lớp disabled
       echo "<a class='disabled'>Sau <i class='fas fa-arrow-right'></i></a>";
   }
   echo '</div>';
   

   echo '</div>';
    // Thẻ div chứa các phần: chương trước, chương sau, bình luận
    echo '<div class="chapter-navigation">';

    // Hiển thị phần bình luận
    echo '<div class="comments-section">';
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

} else {
    echo "Không có dữ liệu.";
}

echo '</div>'; // Đóng thẻ div contents-container
echo '</div>'; // Đóng thẻ div content-container

?>
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
                pContent.className = 'chapter-content';
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
function bookmarkChapter(id_truyen, chapterId, userId) {
    var data = {
        'id_truyen': id_truyen,
        'id_chuong': chapterId,
        'id_user': userId
    };

    // Send AJAX request to server to add bookmark
    $.ajax({
        url: 'pages/danhdau.php', // Your PHP script for adding bookmark
        type: 'post',
        data: data,
        success: function(response) {
            if (response === 'success') {
                // Change the icon to a checkmark and update the onclick event to removeBookmark
                $('#bookmarkIcon').html('<i class="fa-solid fa-circle-check"></i> Đã đánh dấu').attr('onclick', 'removeBookmark(' + id_truyen + ', ' + chapterId + ', ' + userId + ')');
                alert('Đánh dấu thành công.');
            } else {
                alert('Đánh dấu không thành công.');
            }
        },
        error: function(xhr, status, error) {
            alert('Đã xảy ra lỗi khi đánh dấu.');
        }
    });
}

function removeBookmark(id_truyen, chapterId, userId) {
    var data = {
        'id_truyen': id_truyen,
        'id_chuong': chapterId,
        'id_user': userId,
        'action': 'remove'
    };

    // Send AJAX request to server to remove bookmark
    $.ajax({
        url: 'pages/huydanhdau.php', // Your PHP script for removing bookmark
        type: 'post',
        data: data,
        success: function(response) {
            if (response === 'success') {
                // Change the icon back to a bookmark and update the onclick event to bookmarkChapter
                $('#bookmarkIcon').html('<i class="fa-solid fa-bookmark"></i> Đánh dấu').attr('onclick', 'bookmarkChapter(' + id_truyen + ', ' + chapterId + ', ' + userId + ')');
                alert('Đánh dấu đã được hủy.');
            } else {
                alert('Không thể hủy đánh dấu.');
            }
        },
        error: function(xhr, status, error) {
            alert('Lỗi khi gửi yêu cầu hủy đánh dấu.');
        }
    });
}
</script>
<script>
function toggleChapterModal() {
    var chapterModal = document.getElementById("chapterModal");
    if (chapterModal.style.display === "none" || chapterModal.style.display === "") {
        chapterModal.style.display = "block";
        document.body.style.overflow = "hidden"; // Khóa cuộn trang khi modal hiển thị
    } else {
        chapterModal.style.display = "none";
        document.body.style.overflow = ""; // Mở lại cuộn trang khi modal ẩn đi
    }
}
</script>




<!-- Thêm vào trang HTML hoặc file template -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<script>
    function navigateToChapter() {
        // Lấy id_truyen từ URL
        const urlParams = new URLSearchParams(window.location.search);
        const idTruyen = urlParams.get('id_truyen');

        // Lấy giá trị chương từ dropdown
        const selectedChapterId = document.getElementById('chapter-list').value;

        // Lưu số chương đang đọc vào local storage
        localStorage.setItem('sochuong_dang_doc', selectedChapterId);

        // Chuyển hướng đến trang chương
        window.location.href = 'index.php?quanly=doc&id_truyen=' + idTruyen + '&id_chuong=' + selectedChapterId;
    }

    // Gọi hàm navigateToChapter khi trang được tải
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var settingsPanel = document.getElementById('settingsPanel');
    var closeBtn = document.getElementById('closeBtn'); // Lấy nút "X"
    var isOpen = false;

    function closeSettingsPanel() {
        settingsPanel.style.display = 'none';
        isOpen = false;
    }

    function toggleSettingsPanel() {
        // Kiểm tra xem settingsPanel đã hiển thị hay chưa
        if (isOpen) {
            closeSettingsPanel();
        } else {
            settingsPanel.style.display = 'block';
            isOpen = true;
        }
    }

    // Gọi hàm closeSettingsPanel khi click ra ngoài settingsPanel
    document.addEventListener('click', function (event) {
        var isClickInside = settingsPanel.contains(event.target);
        if (!isClickInside && isOpen) {
            closeSettingsPanel();
        }
    });

    // Gọi hàm closeSettingsPanel khi nhấn phím Esc
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && isOpen) {
            closeSettingsPanel();
        }
    });

    // Gọi hàm toggleSettingsPanel khi click vào settingsIcon
    var settingsIcon = document.getElementById('settingsIcon');
    if (settingsIcon) { // Kiểm tra nếu settingsIcon tồn tại
        settingsIcon.addEventListener('click', function (event) {
            event.stopPropagation(); // Ngăn chặn sự kiện click bị lan truyền lên
            toggleSettingsPanel();
        });
    }

    closeBtn.addEventListener('click', function () {
        closeSettingsPanel();
    });

    // Đảm bảo settingsPanel ở trạng thái tắt khi trang tải xong
    // Bỏ qua việc gọi closeSettingsPanel() ở đây vì chúng ta không muốn nó bật lên rồi tắt ngay khi trang tải xong
    // closeSettingsPanel();
});
</script>

<script>
    // Hàm giải mã nội dung đã mã hóa
function decryptContent(encryptedContent) {
    // Giải mã từ base64
    var decodedContent = atob(encryptedContent);
    // Hiển thị nội dung
    document.getElementById('encrypted-content').innerHTML = decodedContent;
}

// Giả sử 'encryptedContent' chứa nội dung đã được mã hóa trên server
var encryptedContent = "<?php echo base64_encode($noidung); ?>";

// Gọi hàm giải mã khi trang web tải xong
window.onload = function() {
    decryptContent(encryptedContent);
};
</script>
<script>
$(document).ready(function() {
    // Function to set a cookie
    function setCookie(name, value, days) {
        let expires = "";
        if (days) {
            let date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = `${name}=${encodeURIComponent(value) || ""}${expires}; path=/; secure; samesite=strict`;
    }

    // Function to get a cookie
    function getCookie(name) {
        const nameEQ = `${name}=`;
        const ca = document.cookie.split(';');
        for(let i = 0; i < ca.length; i++) {
            let c = ca[i].trim();
            if (c.startsWith(nameEQ)) {
                return decodeURIComponent(c.substring(nameEQ.length));
            }
        }
        return null;
    }

    // Function to apply settings from cookies
    function applySettings() {
        const fontSize = getCookie('fontSize');
        const fontFamily = getCookie('fontFamily');
        const lineHeight = getCookie('lineHeight');
        const backgroundColor = getCookie('backgroundColor');

        if (fontSize) {
            $('.content-text').css('font-size', fontSize + 'px');
        }

        if (fontFamily) {
            $('.chapter-content').css('font-family', fontFamily);
        }

        if (lineHeight) {
            $('.chapter-content').css('line-height', lineHeight);
        }

        if (backgroundColor) {
            $('.chapter-info').css('background-color', backgroundColor); 
            if (backgroundColor === '#232323') { // Check for the custom black color
                $('.chapter-info').css('color', '#ccc'); 
            } else {
                $('.chapter-info').css('color', ''); 
            }
        }
    }

    applySettings();

    // Event listeners for setting changes
    $('#fontsize').change(function() {
        const fontSize = $(this).val();
        $('.content-text').css('font-size', fontSize + 'px');
        setCookie('fontSize', fontSize, 30);
    });

    $('#fontfamily').change(function() {
        const fontFamily = $(this).val();
        $('.chapter-content').css('font-family', fontFamily);
        setCookie('fontFamily', fontFamily, 30);
    });

    $('#lineheight').change(function() {
        const lineHeight = $(this).val();
        $('.chapter-content').css('line-height', lineHeight);
        setCookie('lineHeight', lineHeight, 30);
    });

    $('#backgroundcolor').change(function() {
        const backgroundColor = $(this).val();
        $('.chapter-info').css('background-color', backgroundColor); 
        setCookie('backgroundColor', backgroundColor, 30);
        if (backgroundColor === '#232323') { // Apply white text color for custom black background
            $('.chapter-info').css('color', 'white'); 
        } else {
            $('.chapter-info').css('color', ''); 
        }
    });
});
</script>
