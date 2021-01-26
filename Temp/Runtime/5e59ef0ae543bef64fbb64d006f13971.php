<?php /*a:5:{s:64:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/refuseDetails.html";i:1589794190;s:55:"/www/wwwroot/xy.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;s:64:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/verifySuccess.html";i:1589786782;s:55:"/www/wwwroot/xy.qcq.cc/App/Views/index/index/index.html";i:1589615712;s:57:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/verify.html";i:1589787820;}*/ ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title>正在进行数据授权验证-<?php echo htmlentities($system['title']); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="/public/web/css/public.css">

</head>
<body>
<div class="data_verify_box">
    <div class="data_verify_imgBox">
        <img src="/public/web/images/databg.png" style="width: 1.31rem;" alt="">

        <img class="data_verify_img1" src="/public/web/images/dataimg1.png" alt="">

        <img class="data_verify_img3" src="/public/web/images/dataimg3.png" alt="">
        <div class="data_verify_ssi">
            <img class="data_verify_img2" src="/public/web/images/dataimg2.png" alt="">
        </div>
    </div>

</div>
<div class="data_verify_text">
    <h1>正在进行数据授权验证</h1>
    <p>请稍后</p>
</div>
<div class="processcontainer">
    <div id="processbar" style="width:0%;"></div>
</div>
<div id="processbar_text"></div>
<script src="/public/web/js/jquery-3.3.1.min.js"></script>
<script src="/public/web/js/public.js"></script>
<script>
    console.log("<?php echo htmlentities($url); ?>")
var t=setTimeout(function(){
	window.location.href="<?php echo htmlentities($url); ?>";
},5000);
function setProcess(){
    var processbar = document.getElementById("processbar");
    var processbar_text = document.getElementById("processbar_text");
    processbar.style.width = parseInt(processbar.style.width) + 1 + "%";
    processbar_text.innerHTML = processbar.style.width;
    if(processbar.style.width == "100%"){
        window.clearInterval(bartimer);
    }
}
var bartimer = window.setInterval(function(){setProcess();},50);
window.onload = function(){
    bartimer;
}
</script>
</body>
</html>