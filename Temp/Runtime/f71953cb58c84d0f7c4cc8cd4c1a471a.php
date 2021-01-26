<?php /*a:1:{s:60:"/www/wwwroot/payment.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;}*/ ?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?php echo htmlentities($title); ?></title>
    <script type="text/javascript" charset="UTF-8" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">
        //调用微信JS api 支付
        function jsApiCall() {
            WeixinJSBridge.invoke(
                'getBrandWCPayRequest',
                {'appId':'<?php echo htmlentities($jsApiParameters['appId']); ?>',
                    'nonceStr':'<?php echo htmlentities($jsApiParameters['nonceStr']); ?>',
                    'package':'<?php echo htmlentities($jsApiParameters['package']); ?>',
                    'signType':'<?php echo htmlentities($jsApiParameters['signType']); ?>',
                    'timeStamp':'<?php echo htmlentities($jsApiParameters['timeStamp']); ?>',
                    'paySign':'<?php echo htmlentities($jsApiParameters['paySign']); ?>'},
                function (res) {
                    WeixinJSBridge.log(res.err_msg);
                    //alert(res.err_code+res.err_desc+res.err_msg);
                    if(res.err_msg=='get_brand_wcpay_request:cancel'){
                        alert("您已取消支付");
                        var cancel_url = "<?php echo htmlentities($order['cancel_url']); ?>";
                        window.location.href=cancel_url?cancel_url:history.go(-1);
                        return false;
                    }
                    if(res.err_msg=='get_brand_wcpay_request:fail'){
                        alert("支付失败，即将跳转错误页面");
                        window.location.href="/api/payment/payError?order_id=<?php echo htmlentities($order_id); ?>";
                        return false;
                    }
                    if(res.err_msg=='get_brand_wcpay_request:ok'){
                        alert("支付成功!");
                        //完成之后的一些逻辑
                        window.location.href="/api/payment/paySuccess?order_id=<?php echo htmlentities($order_id); ?>";
                    }
                }
            );
        }

        function callpay() {
            if (typeof WeixinJSBridge == "undefined") {
                if (document.addEventListener) {
                    document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
                } else if (document.attachEvent) {
                    document.attachEvent('WeixinJSBridgeReady', jsApiCall);
                    document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
                }
            } else {
                jsApiCall();
            }
        }
        callpay();
    </script>
</head>
<body>
<!--<br/>-->

<!--<div align="center">-->
<!--    <font color="#9ACD32"><b>该笔订单支付金额为<span style="color:#f00;font-size:50px">1分</span>钱</b></font><br/><br/>-->
<!--    <button style="width:210px; height:50px; border-radius: 15px;background-color:#FE6714; border:0px #FE6714 solid; cursor: pointer;  color:white;  font-size:16px;" type="button" onclick="callpay()">立即支付-->
<!--    </button>-->
<!--</div>-->
</body>
</html>