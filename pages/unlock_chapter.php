<?php
session_start();
include('../admin/config/config.php');

if (isset($_POST['id_chuong'], $_SESSION['id_user'])) {
    $id_chuong = $_POST['id_chuong'];
    $id_user = $_SESSION['id_user'];
    // Get chapter's unlocking gold cost
    $query = "SELECT chuong_gold, id_truyen FROM tbl_chuong WHERE id_chuong = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("i", $id_chuong);
        $stmt->execute();
        $stmt->bind_result($chuong_gold, $id_truyen);
        $stmt->fetch();
        $stmt->close();

        // Get user's current gold
        $query_user = "SELECT gold FROM tbl_user WHERE id_user = ?";
        if ($stmt_user = $mysqli->prepare($query_user)) {
            $stmt_user->bind_param("i", $id_user);
            $stmt_user->execute();
            $stmt_user->bind_result($current_gold);
            $stmt_user->fetch();
            $stmt_user->close();

            // Check if user has enough gold
            if ($current_gold >= $chuong_gold) {
                // Deduct gold and grant access
                $new_gold = $current_gold - $chuong_gold;
                $update_gold_query = "UPDATE tbl_user SET gold = ? WHERE id_user = ?";

                // Begin transaction
                $mysqli->begin_transaction();

                if ($update_stmt = $mysqli->prepare($update_gold_query)) {
                    $update_stmt->bind_param("ii", $new_gold, $id_user);
                    if ($update_stmt->execute()) {
                        // Insert access record into user_chapter_access
                        $insert_access_query = "INSERT INTO user_chapter_access (user_id, id_chuong, access_granted_date) VALUES (?, ?, NOW())";
                        if ($access_stmt = $mysqli->prepare($insert_access_query)) {
                            $access_stmt->bind_param("ii", $id_user, $id_chuong);
                            if ($access_stmt->execute()) {
                                // Increment gold in tbl_truyen
                                $increment_gold_query = "UPDATE tbl_truyen SET gold = gold + ? WHERE id_truyen = ?";
                                if ($increment_stmt = $mysqli->prepare($increment_gold_query)) {
                                    $increment_stmt->bind_param("ii", $chuong_gold, $id_truyen);
                                    if ($increment_stmt->execute()) {
                                        // Update gold in tbl_gold_theothang
                                        $add_gold_query = "CALL AddGoldToCurrentMonth(?, ?)";
                                        if ($add_gold_stmt = $mysqli->prepare($add_gold_query)) {
                                            $add_gold_stmt->bind_param("ii", $id_truyen, $chuong_gold);
                                            if ($add_gold_stmt->execute()) {
                                                // Commit transaction
                                                $mysqli->commit();
                                                echo "success";
                                            } else {
                                                // Rollback transaction
                                                $mysqli->rollback();
                                                echo "Lỗi hệ thống: Không thể cập nhật gold trong bảng tbl_gold_theothang.";
                                            }
                                            $add_gold_stmt->close();
                                        } else {
                                            // Rollback transaction
                                            $mysqli->rollback();
                                            echo "Lỗi hệ thống: Không thể chuẩn bị câu lệnh cập nhật gold trong bảng tbl_gold_theothang.";
                                        }
                                    } else {
                                        // Rollback transaction
                                        $mysqli->rollback();
                                        echo "Lỗi hệ thống: Không thể cập nhật gold trong bảng truyện.";
                                    }
                                    $increment_stmt->close();
                                } else {
                                    // Rollback transaction
                                    $mysqli->rollback();
                                    echo "Lỗi hệ thống: Không thể chuẩn bị câu lệnh cập nhật gold trong bảng truyện.";
                                }
                            } else {
                                // Rollback transaction
                                $mysqli->rollback();
                                echo "Không thể truy cập chương.";
                            }
                            $access_stmt->close();
                        } else {
                            echo "Lỗi hệ thống.";
                        }
                    } else {
                        echo "Không thể cập nhật gold.";
                    }
                    $update_stmt->close();
                } else {
                    echo "Lỗi hệ thống.";
                }
            } else {
                echo "Không đủ gold. Vui lòng mua thêm để mở khóa chương.";
            }
        }
    }
    $mysqli->close();
} else {
    echo "Vui lòng đăng nhập.";
}
?>
