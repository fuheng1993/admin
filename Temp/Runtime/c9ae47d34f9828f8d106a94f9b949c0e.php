<?php /*a:2:{s:55:"/www/wwwroot/xy.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;s:64:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/verifySuccess.html";i:1589786782;}*/ ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>授权验证成功-<?php echo htmlentities($system['title']); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="/public/web/css/public.css">
</head>
<body>
<div class="data_verify_box">
    <div class="data_verify_imgBox" style="margin-left: -0.94rem;top: 0.5rem;">
        <img src="/public/web/images/dcbg.png" style="width: 1.88rem;" alt="">
        <div class="dv_img_box">
            <img class="dc_img1" src="/public/web/images/dccg.png" alt="">
            <div class="dv_img_mode">
            </div>
        </div>
    </div>
</div>
<div class="data_verify_text">
    <h1><?php echo htmlentities($order['name']); ?>的数据<br>授权验证成功</h1>
    <p>请查阅报告详情</p>
    <img src="/public/web/images/datackbg@3x.png" onclick="go()" style="width: 3.2rem;" alt="">
</div>

<script src="/public/web/js/jquery-3.3.1.min.js"></script>
<script src="/public/web/js/public.js"></script>
<script>
    function go(){
        window.location.href="<?php echo htmlentities($url); ?>";
    }
</script>
</body>
</html>