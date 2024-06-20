<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phieu vu</title>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9357006487999643"
     crossorigin="anonymous"></script>

</head>

<?php
include('./admin/config/config.php');

// Truy vấn CSDL để lấy đường dẫn ảnh từ bảng tbl_anhtrangbia
$sql = "SELECT hinhanh FROM tbl_anhtrangbia WHERE tinhtrang = 1 ORDER BY RAND() LIMIT 1";
$result = $mysqli->query($sql);

// Kiểm tra và lấy đường dẫn ảnh
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $imagePath = $row['hinhanh'];
} else {
    // Nếu không có dữ liệu, sử dụng ảnh mặc định hoặc đặt giá trị khác tùy ý
    $imagePath = '';
}
?>

<!-- Thẻ img cho hiển thị ảnh -->
<img class="top-bg-op-box" src="<?php echo $imagePath; ?>" alt="Background Image">
<main>
<?php
$sql = "SELECT
truyen.id_truyen,
truyen.tieude,
truyen.tomtat,
truyen.hinhanh,
truyen.tacgia,
user.tenuser AS nguoidang,
MAX(theloai.tentheloai) AS theloai
FROM
tbl_truyen truyen
INNER JOIN tbl_user user ON truyen.id_admin = user.id_user
INNER JOIN tbl_truyen_theloai tt ON truyen.id_truyen = tt.id_truyen
INNER JOIN tbl_theloai theloai ON tt.id_theloai = theloai.id_theloai
WHERE
truyen.truyen_status = 1
GROUP BY
truyen.id_truyen
ORDER BY
truyen.id_truyen DESC
LIMIT 8;";

$result = $mysqli->query($sql);

?>
    <div class="container">
    <div class="main-content">
    <div class="column-80">
        <div class="fsdfs433">
    <h3 class="thehh">Truyện Mới Cập Nhật</h3>
    <a href="index.php?quanly=truyen&decu=all"><i class="fa-solid fa-angles-right"></i></a>

    </div>

    <div class="recommended-stories">
        <div class="story-row">
            <?php while ($row = $result->fetch_assoc()) : ?>
                <div class="story-item">
                    <div class="story-thumbnail">
                        <!-- Ảnh truyện (thay thế bằng đường dẫn đến hình ảnh từ cơ sở dữ liệu) -->
                        <img src="<?php echo $row['hinhanh']; ?>" alt="Truyện <?php echo $row['tieude'];; ?>">
                    </div>
                    <div class="story-details">
                        <!-- Thông tin truyện -->
                        <a class="tieude" href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row['id_truyen']; ?>"><?php echo $row['tieude']; ?></a>
                        <p class="tomtat_v1"><?php echo $row['tomtat']; ?></p>
                        <div class="jjskks11">
                        <p class="tacgia321"><i class="fa-solid fa-user-pen"></i> <?php echo $row['tacgia']; ?></p>
                        <a href="index.php?quanly=truyen&category[]=<?php echo  $row['theloai']; ?>" class="theloai321"> <?php echo $row['theloai']; ?></a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>
<?php

// Kiểm tra xem người dùng đã đăng nhập bằng SESSION hoặc COOKIE chưa
if (isset($_SESSION['id_user']) || isset($_COOKIE['user_id'])) {
    $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : $_COOKIE['user_id'];

    // Lấy thông tin truyện đã đọc từ CSDL
    $query_read_stories = "SELECT 
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
        reading.id_user = $id_user
    GROUP BY 
        truyen.id_truyen, truyen.tieude, truyen.hinhanh, id_chuong_doc, so_chuong_doc
    LIMIT 6";

    $result_read_stories = mysqli_query($mysqli, $query_read_stories);

    // Kiểm tra và hiển thị danh sách truyện đã đọc
    if ($result_read_stories && mysqli_num_rows($result_read_stories) > 0) {
        echo '<div class="column-20">';
        echo '<div class="fsdfs433">';
        echo '<h3 class="thehh">Đang đọc</h3>';
        echo '<a href="index.php?quanly=tutruyen"><i class="fa-solid fa-angles-right"></i></a>';
        echo '</div>';
        echo '<div class="story-row">';

        while ($row_story = mysqli_fetch_assoc($result_read_stories)) {
            echo '<div class="story-items">';
            echo '<div class="story-thumbnailss">';
            echo '<img style="width:35px;" src="' . $row_story['hinhanh'] . '" alt="' . $row_story['tieude'] . '">';
            echo '</div>';
            echo '<div class="story-details">';
            echo '<a class="tieude" href="index.php?quanly=thongtintruyen&id_truyen=' . $row_story['id_truyen'] . '">' . $row_story['tieude'] . '</a>';
            echo '<a href="index.php?quanly=doc&id_truyen=' . $row_story['id_truyen'] . '&id_chuong=' . $row_story['id_chuong_doc'] . '" class="read-more">Đọc tiếp</a>';
            echo '<p>Đã Đọc ' . $row_story['so_chuong_doc'] . '/' . $row_story['tong_so_chuong'] . '</p>';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>'; // Kết thúc column-20
    }
}
?>

    </div>

    <div class="new-stories-abc">
    <h3 class="theh">Chương Mới Cập Nhật</h3>
    <div class="story-list-abc">
    <?php
// Hàm chuyển đổi thời gian thành chuỗi "vừa xong", "x phút/giờ/ngày/trước"
function time_elapsed_string($datetime) {
// Tạo đối tượng DateTimeZone cho múi giờ Hồ Chí Minh
$timezone = new DateTimeZone('Asia/Ho_Chi_Minh');

// Tạo đối tượng DateTime với múi giờ đã thiết lập
$now = new DateTime('now', $timezone);

// Hiển thị thời gian hiện tại ở Hồ Chí Minh

    $ago = new DateTime($datetime);

    $diff = $now->diff($ago);

    $diff_str = [
        'y' => 'năm',
        'm' => 'tháng',
        'd' => 'ngày',
        'h' => 'giờ',
        'i' => 'phút',
        's' => 'giây',
    ];

    foreach ($diff_str as $key => &$value) {
        if ($diff->$key) {
            $value = $diff->$key . ' ' . $value . ($diff->$key > 1 ? '' : '');
            return ($key == 's') ? 'vừa xong' : $value . ' trước';
        }
    }

    return 'vừa xong';
}

// Thực hiện câu truy vấn để lấy dữ liệu từ cơ sở dữ liệu
$query = "SELECT
    truyen.id_truyen,
    MAX(theloai.tentheloai) AS tentheloai,
    truyen.tieude AS tentruyen,
    MAX(chuong.id_chuong) AS id_chuong,
    MAX(chuong.sochuong) AS sochuong,
    MAX(chuong.tenchuong) AS tenchuong,
    MAX(truyen.tacgia) AS tacgia,
    MAX(chuong.thoigian) AS thoigian
FROM
    tbl_chuong chuong
INNER JOIN
    tbl_truyen truyen ON chuong.id_truyen = truyen.id_truyen
INNER JOIN
tbl_user user ON truyen.id_admin = user.id_user
INNER JOIN
    tbl_truyen_theloai tt ON truyen.id_truyen = tt.id_truyen
INNER JOIN
    tbl_theloai theloai ON tt.id_theloai = theloai.id_theloai
WHERE
    truyen.truyen_status = 1
GROUP BY
    truyen.id_truyen
ORDER BY
    MAX(chuong.thoigian) DESC
LIMIT 10";



// Thực hiện truy vấn và lấy dữ liệu
$result = $mysqli->query($query);

// Kiểm tra và hiển thị dữ liệu
if ($result && $result->num_rows > 0) {
    ?>
    <table class="story-list-abc">
        <?php
        while ($row = $result->fetch_assoc()) {
            ?>
            <tr class="maytinh">
                <td><p><?php echo $row['tentheloai']; ?></p></td>
                <td><a class="truyenmoicapnhat" href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row['id_truyen']; ?>&id_chuong=<?php echo $row['id_chuong']; ?>"><?php echo $row['tentruyen']; ?></a></td>
                <td><p><a href="index.php?quanly=doc&id_truyen=<?php echo $row['id_truyen']; ?>&id_chuong=<?php echo $row['id_chuong']; ?>">Chương <?php echo $row['sochuong']; ?>: <?php echo $row['tenchuong']; ?></a></p></td>
                <td><p><?php echo $row['tacgia']; ?></p></td>
                <td><p><?php echo time_elapsed_string($row['thoigian']); ?></p></td>
            </tr>
            <tr class="story-rows">
    <td colspan="3">
        <div class="story-left">
            <div class="story-title"><a href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $row['id_truyen']; ?>&id_chuong=<?php echo $row['id_chuong']; ?>"><?php echo $row['tentruyen']; ?></a></div>
            <div class="story-detailss">
                <div class="story-author"><i class="fa-solid fa-user-pen"></i> <?php echo $row['tacgia']; ?></div>
                <div class="story-genre"><i class="fa-solid fa-book"></i> <?php echo $row['tentheloai']; ?></div>
            </div>
        </div>
        <div class="story-right">
            <div class="story-chapter"><a href="index.php?quanly=doc&id_truyen=<?php echo $row['id_truyen']; ?>&id_chuong=<?php echo $row['id_chuong']; ?>">C.<?php echo $row['sochuong']; ?></a></div>
        </div>
    </td>
</tr>
            <?php
        }
        ?>

    </table>
<?php
} else {
    echo "Không có dữ liệu.";
}
?>


</div>
</div>


<div class="weekly-stories">
    <div class="column">
    <h3 class="theh">Đọc Nhiều</h3>
    <div class="weekly-list">


    <?php
// Kết nối đến cơ sở dữ liệu

// Lấy tháng và năm hiện tại
$currentMonth = date('m');
$currentYear = date('Y');

// Truy vấn lấy danh sách truyện đọc nhiều trong tháng hiện tại
$queryDocNhieuThang = "SELECT t.id_truyen, t.tieude, t.luotdoc, t.hinhanh, t.id_admin, a.tenuser,t.tacgia
FROM tbl_truyen t
INNER JOIN tbl_user a ON t.id_admin = a.id_user
ORDER BY t.luotdoc DESC
LIMIT 10;



";



// Thực hiện truy vấn và lấy dữ liệu
$resultDocNhieuThang = $mysqli->query($queryDocNhieuThang);

function formatLuotDoc($luotdoc) {
    if ($luotdoc >= 1000000) {
        return round($luotdoc / 1000000, 1) . 'M';
    } elseif ($luotdoc >= 1000) {
        return number_format($luotdoc);
    } else {
        return $luotdoc;
    }
}

// Kiểm tra và xử lý kết quả
if ($resultDocNhieuThang->num_rows > 0) {
    // Duyệt qua từng dòng dữ iu liệu
    $count = 1;
    while ($rowTruyen = $resultDocNhieuThang->fetch_assoc()) {
        // Hiển thị thông tin truyện
        ?>
        <div class="weekly-item">
            <?php if ($count == 1) : ?>
                <div class="weekly-lists">
                    <i class="fa-solid fa-trophy"></i>
                    <div class="top-1-info">
                        <a class="tieudetruyen" href="index.php?quanly=thongtintruyen&id_truyen=<?= $rowTruyen['id_truyen'] ?>"><?= $rowTruyen['tieude'] ?></a>
                        <div class="tenuser"><i class="fas fa-user-edit"></i> <?= $rowTruyen['tacgia'] ?></div>
                        <div class="luotdoc"><i class="fa-solid fa-glasses"></i> <?= formatLuotDoc($rowTruyen['luotdoc']) ?></div>
                    </div>
                </div>
            <?php else : ?>
                <div class="weekly-lists">
                    <p><?= $count ?></p>
                    <a class="tieudetruyena" href="index.php?quanly=thongtintruyen&id_truyen=<?= $rowTruyen['id_truyen'] ?>"><?= $rowTruyen['tieude'] ?></a>
                </div>
                <p class="luotdoc luotdoc-1"> <?= formatLuotDoc($rowTruyen['luotdoc']) ?></p>
            <?php endif; ?>

            <?php if ($count == 1) : ?>
                <div class="top-1-image">
                    <img src="<?= $rowTruyen['hinhanh'] ?>" alt="Hình ảnh truyện">
                    <span class="book-cover-shadow"></span>
                </div>
            <?php endif; ?>
        </div>
        <?php
        $count++;
    }
} else {
    // Hiển thị thông báo khi không có dữ liệu
    echo '<p>Không có dữ liệu truyện đọc nhiều trong tháng này.</p>';
}

// Đóng kết nối
?>

    </div>
    </div>


    

    <div class="column">
        <h3 class="theh">TOP GOLD</h3>
        <div class="weekly-list">
        <?php
// Kết nối đến cơ sở dữ liệu

function formatGold($gold) {
    if ($gold >= 1000000) {
        return round($gold / 1000000, 1) . 'M';
    } elseif ($gold >= 1000) {
        return number_format($gold);
    } else {
        return $gold;
    }
}

// Truy vấn lấy danh sách truyện theo số lượng "gold" cao nhất
$currentMonth = date('n'); // Tháng hiện tại (1-12)
$currentYear = date('Y');  // Năm hiện tại

// Truy vấn lấy danh sách truyện theo số lượng "gold" cao nhất trong tháng hiện tại
$queryGoldNhieuNhat = "SELECT t.id_truyen, t.tieude, IFNULL(g.gold, 0) AS gold, t.hinhanh, t.id_admin, t.tacgia
                       FROM tbl_truyen t
                       LEFT JOIN tbl_gold_theothang g ON t.id_truyen = g.id_truyen AND g.thang = ? AND g.nam = ?
                       WHERE t.truyen_status = 1
                       ORDER BY gold DESC
                       LIMIT 10;";

// Chuẩn bị và thực hiện truy vấn
$stmt = $mysqli->prepare($queryGoldNhieuNhat);
$stmt->bind_param("ii", $currentMonth, $currentYear);
$stmt->execute();
$resultGoldNhieuNhat = $stmt->get_result();

// Kiểm tra và xử lý kết quả
if ($resultGoldNhieuNhat->num_rows > 0) {
    $count = 1;
    while ($rowTruyen = $resultGoldNhieuNhat->fetch_assoc()) {
        // Hiển thị thông tin truyện
        ?>
        <div class="weekly-item">
            <?php if ($count == 1) : ?>
                <div class="weekly-lists top-gold">
                    <div class="medal-icon"><i class="fa-regular fa-money-bill-1"></i></div>
                    <div class="top-1-info">
                        <a class="tieudetruyen" href="index.php?quanly=thongtintruyen&id_truyen=<?= $rowTruyen['id_truyen'] ?>">
                            <?= $rowTruyen['tieude'] ?>
                        </a>
                        <div class="tenuser"><i class="fas fa-user-edit"></i> <?= $rowTruyen['tacgia'] ?></div>
                        <div class="luotgold"><i class="fa-solid fa-piggy-bank"></i> <?= formatGold($rowTruyen['gold']) ?></div>
                    </div>
                </div>
            <?php else : ?>
                <div class="weekly-lists">
                    <p class="rank-number"><?= $count ?></p>
                    <a class="tieudetruyena" href="index.php?quanly=thongtintruyen&id_truyen=<?= $rowTruyen['id_truyen'] ?>">
                        <?= $rowTruyen['tieude'] ?>
                    </a>
                </div>
                <p class="luotgold luotgold-1"> <?= formatGold($rowTruyen['gold']) ?></p>
            <?php endif; ?>

            <?php if ($count == 1) : ?>
                <div class="top-1-image">
                    <img src="<?= $rowTruyen['hinhanh'] ?>" alt="Hình ảnh truyện">
                </div>
            <?php endif; ?>

        </div>
        <?php
        $count++;
    }
} else {
    // Hiển thị thông báo khi không có dữ liệu
    echo '<p>Không có dữ liệu truyện được đề cử nhiều tuần.</p>';
}

// Đóng kết nối
?>



        </div>
    </div>
    <div class="column">
    <h3 class="theh">TOP CHI TIÊU</h3>
    <div class="weekly-list">
    <?php
    // Kết nối đến cơ sở dữ liệu

    // Truy vấn lấy danh sách người dùng theo tổng số gold chi tiêu cao nhất
    $queryGoldChiTieuNhieuNhat = "SELECT u.id_user, u.tenuser, u.avatar, IFNULL(SUM(uca.gold), 0) AS total_gold
                                  FROM tbl_user u
                                  LEFT JOIN user_chapter_access uca ON u.id_user = uca.user_id
                                  GROUP BY u.id_user, u.tenuser, u.avatar
                                  ORDER BY total_gold DESC
                                  LIMIT 10;";

    // Chuẩn bị và thực hiện truy vấn
    $stmt = $mysqli->prepare($queryGoldChiTieuNhieuNhat);
    $stmt->execute();
    $resultGoldChiTieuNhieuNhat = $stmt->get_result();

    // Kiểm tra và xử lý kết quả
    if ($resultGoldChiTieuNhieuNhat->num_rows > 0) {
        $count = 1;
        while ($rowUser = $resultGoldChiTieuNhieuNhat->fetch_assoc()) {
            // Hiển thị thông tin người dùng
            ?>
            <div class="weekly-item">
                <?php if ($count == 1) : ?>
                    <div class="weekly-lists top-gold">
                        <div class="medal-icon"><i class="fa-regular fa-money-bill-1"></i></div>
                        <div class="top-1-info">
                            <a class="tieudetruyen" href="index.php?quanly=thongtinuser&id_user=<?= $rowUser['id_user'] ?>">
                                <?= $rowUser['tenuser'] ?>
                            </a>
                            <div class="luotgold"><i class="fa-solid fa-piggy-bank"></i> <?= formatGold($rowUser['total_gold']) ?></div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="weekly-lists">
                        <p class="rank-number"><?= $count ?></p>
                        <a class="tieudetruyena" href="index.php?quanly=thongtinuser&id_user=<?= $rowUser['id_user'] ?>">
                            <?= $rowUser['tenuser'] ?>
                        </a>
                    </div>
                    <p class="luotgold luotgold-1"> <?= formatGold($rowUser['total_gold']) ?></p>
                <?php endif; ?>

                <?php if ($count == 1) : ?>
                    <div class="top-1232">
                        <img style="width:100px;" class="img123321" src="<?= $rowUser['avatar'] ?>" alt="Avatar người dùng">
                    </div>
                <?php endif; ?>
            </div>
            <?php
            $count++;
        }
    } else {
        // Hiển thị thông báo khi không có dữ liệu
        echo '<p>Không có dữ liệu người dùng chi tiêu nhiều.</p>';
    }

    // Đóng kết nối
    ?>
    </div>
</div>




    </div>
    <div class="fsdfs433">
    <h3 class="thehh">Truyện Hoàn Thành</h3>
    <a style="color: #c17200;" href="index.php?quanly=truyen&truyen=hoanthanh">Xem thêm<i class="fa-solid fa-angles-right"></i></a>
</div>
<div class="completed-stories-container">
    <button class="scroll-button left" onclick="scrollLeft()">&#9664;</button>
    <div class="completed-stories">
        <?php
        // Kết nối đến cơ sở dữ liệu

        // Truy vấn lấy danh sách truyện hoàn thành
        $queryTruyenHoanThanh = "SELECT id_truyen, tieude, hinhanh 
                                 FROM tbl_truyen 
                                 WHERE status_tt = 1 
                                 ORDER BY ngaydang DESC";

        // Chuẩn bị và thực hiện truy vấn
        $stmt = $mysqli->prepare($queryTruyenHoanThanh);
        $stmt->execute();
        $resultTruyenHoanThanh = $stmt->get_result();

        // Kiểm tra và xử lý kết quả
        if ($resultTruyenHoanThanh->num_rows > 0) {
            while ($rowTruyen = $resultTruyenHoanThanh->fetch_assoc()) {
                ?>
                <div class="story-itemss">
                    <a href="index.php?quanly=thongtintruyen&id_truyen=<?= $rowTruyen['id_truyen'] ?>">
                        <img src="<?= $rowTruyen['hinhanh'] ?>" alt="<?= $rowTruyen['tieude'] ?>">
                        <a class="tieudetruyena" href="index.php?quanly=thongtintruyen&id_truyen=<?php echo $rowTruyen['id_truyen']; ?>"><?php echo $rowTruyen['tieude']; ?></a>
                    </a>
                </div>
                <?php
            }
        } else {
            // Hiển thị thông báo khi không có dữ liệu
            echo '<p>Không có truyện hoàn thành.</p>';
        }

        // Đóng kết nối
        $stmt->close();
        ?>
    </div>
    <button class="scroll-button right" onclick="scrollRight()">&#9654;</button>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const scrollContainer = document.querySelector('.completed-stories');
    const scrollAmount = 300;
    const scrollInterval = 3000; // 3 seconds

    function scrollLeft() {
        scrollContainer.scrollBy({
            top: 0,
            left: -scrollAmount,
            behavior: 'smooth'
        });
    }

    function scrollRight() {
        scrollContainer.scrollBy({
            top: 0,
            left: scrollAmount,
            behavior: 'smooth'
        });
    }

    document.querySelector('.scroll-button.left').addEventListener('click', scrollLeft);
    document.querySelector('.scroll-button.right').addEventListener('click', scrollRight);

    setInterval(function() {
        const maxScrollLeft = scrollContainer.scrollWidth - scrollContainer.clientWidth;
        if (scrollContainer.scrollLeft >= maxScrollLeft) {
            scrollContainer.scrollTo({
                left: 0,
                behavior: 'smooth'
            });
        } else {
            scrollRight();
        }
    }, scrollInterval);
});
</script>


</main>
