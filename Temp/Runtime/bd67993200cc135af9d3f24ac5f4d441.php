<?php /*a:2:{s:55:"/www/wwwroot/xy.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;s:55:"/www/wwwroot/xy.qcq.cc/App/Views/index/index/error.html";i:1589772909;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlentities($system['title']); ?>-异常提示</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="stylesheet" href="/public/layui/css/layui.css?t=20180530-2" media="all">
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
</head>
<body>
<div id="app"></div>
<script src="/public/layui/layui.js"></script>
<script>
    layui.use(['form'], function(){
        var table = layui.table
            ,form = layui.form
            ,layer = layui.layer
            ,$ = layui.jquery;

        //边缘弹出
        layer.open({
            type: 1
            ,title:'系统提示'
            ,shadeClose:false
            ,content: '<div style="padding: 20px 80px;"><?php echo htmlentities($msg); ?></div>'
            ,btn: '我知道了'
            ,btnAlign: 'c' //按钮居中
            ,shade: 0 //不显示遮罩
            ,yes: function(){
               window.location.href='<?php echo htmlentities($url); ?>';
            },end:function () {
                window.location.href='<?php echo htmlentities($url); ?>';
            }
        });
    });
</script>
</body>

</html>