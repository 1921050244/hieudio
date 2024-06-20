<!-- modules/main.php -->
<div class="container-fluid">
    <div class="row">
        <!-- Menu -->

        <!-- Nội dung chính -->
            <?php
            
                include('config/config.php');

                if(isset($_GET['action']) && $_GET['query']){
                    $tam = $_GET['action'];
                    $query = $_GET['query'];
                } else {
                    $tam = '';
                    $query = '';
                }

                if($tam=='quanlytruyen' && $query=='them'){
                    include('./modules/quanlytruyen/them.php' );
                } elseif($tam=='quanlytruyen' && $query=='lietke'){
                    include('./modules/quanlytruyen/lietke.php' );
                } elseif($tam=='quanlytruyen' && $query=='themchuong'){
                    include('./modules/quanlytruyen/themchuong.php' );
                } elseif($tam=='quanlytruyen' && $query=='sua'){
                    include('./modules/quanlytruyen/sua.php' );
                } elseif($tam=='quanlytruyen' && $query=='xuly'){
                    include('./modules/quanlytruyen/xuly.php' );

                } elseif($tam=='quanlychuong' && $query=='lietke'){
                    include('./modules/quanlychuong/lietke.php' );
                } elseif($tam=='quanlychuong' && $query=='them'){
                    include('./modules/quanlychuong/them.php' );
                } elseif($tam=='quanlychuong' && $query=='themchuong'){
                    include('./modules/quanlychuong/themchuong.php' );
                } elseif($tam=='quanlychuong' && $query=='sua'){
                    include('./modules/quanlychuong/sua.php' ); 
                } elseif($tam=='quanlychuong' && $query=='xuly'){
                    include('./modules/quanlychuong/xuly.php' ); 

                } elseif($tam=='quanlytheloai' && $query=='lietke'){
                    include('./modules/quanlytheloai/lietke.php' ); 
                } elseif($tam=='quanlytheloai' && $query=='xuly'){
                    include('./modules/quanlytheloai/xuly.php' ); 
                } elseif($tam=='quanlytheloai' && $query=='sua'){
                    include('./modules/quanlytheloai/sua.php' );

                } elseif($tam=='quanlyslide' && $query=='lietke'){
                    include('./modules/quanlyslide/lietke.php' ); 
                } elseif($tam=='quanlyslide' && $query=='xuly'){
                    include('./modules/quanlyslide/xuly.php' ); 
                } elseif($tam=='quanlyslide' && $query=='sua'){
                    include('./modules/quanlyslide/sua.php' );

                } elseif($tam=='quanlytaikhoan' && $query=='lietke'){
                    include('./modules/quanlytaikhoan/lietke.php' );
                } elseif($tam=='quanlytaikhoan' && $query=='sua'){
                    include('./modules/quanlytaikhoan/sua.php' );
                } elseif($tam=='quanlytaikhoan' && $query=='xuly'){
                    include('./modules/quanlytaikhoan/xuly.php' );

                } elseif($tam=='quanlychinhsach' && $query=='lietke'){
                    include('./modules/quanlychinhsach/lietke.php' );
                } elseif($tam=='quanlychinhsach' && $query=='them'){
                    include('./modules/quanlychinhsach/them.php' );
                } elseif($tam=='quanlychinhsach' && $query=='xuly'){
                    include('./modules/quanlychinhsach/xuly.php' );
                } elseif($tam=='quanlychinhsach' && $query=='sua'){
                    include('./modules/quanlychinhsach/sua.php' );

                } elseif($tam=='quanlythongbao' && $query=='lietke'){
                    include('./modules/quanlythongbao/lietke.php' );
                } elseif($tam=='quanlythongbao' && $query=='them'){
                    include('./modules/quanlythongbao/them.php' );
                } elseif($tam=='quanlythongbao' && $query=='xuly'){
                    include('./modules/quanlythongbao/xuly.php' );
                } elseif($tam=='quanlythongbao' && $query=='sua'){
                    include('./modules/quanlythongbao/sua.php' );

                } elseif($tam=='quanlygold' && $query=='lietke'){
                    include('./modules/quanlygold/lietke.php' );
                } elseif($tam=='quanlygold' && $query=='xuly'){
                    include('./modules/quanlygold/xuly.php' );
                } elseif($tam=='quanlygold' && $query=='them'){
                    include('./modules/quanlygold/them.php' );
                } elseif($tam=='dashboard' && $query=='dashboard'){
                    include('./modules/dashboard.php' );
                } elseif($tam=='trangchu' && $query=='home'){
                    include('./modules/home.php' );
                } elseif($tam=='crack' && $query=='xuly'){
                    include('./modules/crack/xuly.php' );
                } elseif($tam=='crack' && $query=='them'){
                    include('./modules/crack/them.php' );

                } elseif($tam=='quanlythanhtoan' && $query=='lietke'){
                    include('./modules/quanlythanhtoan/lietke.php' );
                } elseif($tam=='quanlythanhtoan' && $query=='xemchitiet'){
                    include('./modules/quanlythanhtoan/xemchitiet.php' );
                } elseif($tam=='dangnhap' && $query=='dangnhap'){
                    include('./modules/dangnhap.php' ); 
                } elseif($tam=='dangky' && $query=='dangky'){
                    include('./modules/dangky.php' ); 
                } else {
                    // Nội dung mặc định khi không có trang cụ thể được yêu cầu
             
                }
            ?>

    </div>
</div>
