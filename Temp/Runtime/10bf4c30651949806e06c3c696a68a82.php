<?php /*a:4:{s:55:"/www/wwwroot/xy.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;s:55:"/www/wwwroot/xy.qcq.cc/App/Views/index/index/index.html";i:1589615712;s:57:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/verify.html";i:1589787820;s:64:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/refuseDetails.html";i:1589794190;}*/ ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlentities($category['name']); ?>-<?php echo htmlentities($system['title']); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link rel="stylesheet" href="/public/web/css/iconfont.css">
    <link rel="stylesheet" href="/public/web/css/public.css?v=3">
    <link rel="stylesheet" href="/public/web/css/esignDemo.css?v=3">
    <script src="/public/web/js/jquery-3.3.1.min.js"></script>
    <script>var www="<?php echo htmlentities($host); ?>";</script>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?da5659045682ffcb23f3bccf399dbd09";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "https://hm.baidu.com/hm.js?2eee881da6629e01293e09546700a7c9";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>

    <style>
        .query_cilii_box:nth-child(1){
            left:15%;
        }
        .query_cilii_box:nth-child(2){
            left:45%;
        }
        .query_cilii_box:nth-child(3){
            left:75%;
        }
    </style>
</head>
<body>
<div class="query_top">
    <img src="/public/web/images/querytitle@2x.png" style="width: 0.93rem;" alt="">
    <h1>没逾期也会变网黑？</h1>
    <p>网黑逾期一键查 快！全！准！</p>
    <div class="query_top_imgBox">
        <img class="query_top_img1" src="/public/web/images/phone@2x.png" alt="">
        <img class="query_top_img2" src="/public/web/images/query1377@2x.png" alt="">
        <img class="query_top_img3" src="/public/web/images/query3.png" alt="">
        <img class="query_top_img4" src="/public/web/images/query4.png" alt="">
        <img class="query_top_img5" src="/public/web/images/query5.png" alt="">
        <img class="query_top_img6" src="/public/web/images/query4.png" alt="">
        <img class="query_top_img7" src="/public/web/images/query7.png" alt="">
        <img class="query_top_img8" src="/public/web/images/query8.png" alt="">
        <img class="query_top_img9" src="/public/web/images/query9.png" alt="">
        <img class="query_top_img10" src="/public/web/images/query10.png" alt="">
        <img class="query_top_img11" src="/public/web/images/query11.png" alt="">
        <img class="query_top_img12" src="/public/web/images/query12.png" alt="">
        <img class="query_top_img13" src="/public/web/images/query13.png" alt="">
        <img class="query_top_img14" src="/public/web/images/query14.png" alt="">
        <img class="query_top_img15" src="/public/web/images/query15.png" alt="">
        <img class="query_top_img16" src="/public/web/images/query16.png" alt="">
        <img class="query_top_img17" src="/public/web/images/query17.png" alt="">
    </div>
</div>
<div class="query_content">
    <!--    <img style="width: 112%;height: 10.3rem; margin-left: -6%;" src="/public/web/images/bg-query@2x.png" alt="">-->
    <div class="index_select_we" style="width: 92%;margin: 0.2rem auto 0.2rem auto">
        <div class="query_ticon">
            <span>
                <a href="/refuseDetailsReport">
                    <img src="/public/web/images/btn-sample@3x.png" alt="">
                    <div class="shadow"></div>
                </a>
            </span>
            <span>
                <a href="/history">
                    <img src="/public/web/images/btn-history@3x.png" alt="">
                </a>
           </span>
        </div>
        <input type="hidden" id="formaction" value="/doRefuseDetails" />
        <div class="query_ci_list">
            <div class="query_cil_item">
                <p>姓名</p>
                <div class="query_cili_input">
                    <?php foreach($history as $v): ?>
                        <div class="query_cilii_box">
                            <a onClick="queren('<?php echo htmlentities($v['name']); ?>','<?php echo htmlentities($v['tel']); ?>','<?php echo htmlentities($v['id_card']); ?>')">
                                <img src="/public/web/images/q2box.png" style="width: 0.68rem;" alt="">
                                <div class="query_cilii_name">
                                    <?php echo htmlentities($v['name']); ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                    <input class="query_input_name" name="username" id="username" type="text" placeholder="请输入您的姓名">
                </div>
            </div>
            <div class="query_cil_item">
                <p>身份证号</p>
                <div class="query_cili_input">
                    <input type="text" name="sfz" id="sfz" placeholder="请输入您的身份证号">
                </div>
            </div>
            <div class="query_cil_item">
                <p>手机号</p>
                <div class="query_cili_input">
                    <input type="number" name="tel" id="tel" placeholder="请输入您的手机号">
                </div>
            </div>
            <div class="query_cil_item">
                <p>验证码</p>
                <div class="query_cili_input code_input">
                    <input type="number" name="code" id="code" placeholder="请输入短信验证码"  maxlength="4">
                    <span class="code_text getcode"  onclick="getSearchCode($(this),$('#tel'))">获取验证码</span>
                    <span class="code_text codetime"></span>
                </div>
            </div>
        </div>
        <div class="query_ci_btn"  onclick="submit()">
            获取报告 ￥<?php echo htmlentities($category['price']); ?>
        </div>
        <div class="query_ci_xy">
            <span class="query_xyIn"></span>
            <p>我已阅读并同意大数据查询
                <a href="/license">《授权协议》</a>
            </p>
        </div>
    </div>
</div>
<div class="ra_myFx_hintBox query_major">
    <div class="ra_myFx_hint">
        <p class="ra_myFxz_text">报告<span>主要内容</span>
        </p>
        <img class="ra_myFxh_clickSj " src="/public/web/images/bottom-Collapse@2x.png" style="width: 0.22rem;" alt="">
    </div>
    <div class="ra_myFxh_slideBox" style="padding: 0;">
        <div class=" query_icon_list">
            <span>
                <img src="/public/web/images/12@2x.png" alt="">
                <span>风险评分</span>
           </span>
            <span>
                <img src="/public/web/images/1@2x.png" alt="">
                <span>黑名单检测</span>
            </span>
            <span>
                <span class="iconfont icon-shixin text_green"></span>
                <span>失信名单查询</span>
           </span>
            <span>
                <img src="/public/web/images/9@2x.png" alt="">
                <span>多头申请信息</span>
           </span>
            <span>
                <img src="/public/web/images/4@2x.png" alt="">
                <span>历史借贷详情</span>
           </span>
            <span>
                <img src="/public/web/images/1@2x.png" alt="">
                <span>履约行为</span>
            </span>
            <span>
                <img src="/public/web/images/10@2x.png" alt="">
                <span>借款记录</span>
           </span>
            <span>
                <img src="/public/web/images/11@2x.png" alt="">
                <span>还款记录</span>
           </span>
            <span>
                <span class="iconfont icon-yuqi text_green"></span>
                <span>逾期记录</span>
           </span>
            <span>
                <div class="iconfont icon-jingcha text_green"></div>
                <span>违法犯罪</span>
            </span>
            <span>
                <div class="iconfont icon-yangmaodang text_green"></div>
                <span>羊毛党</span>
           </span>
            <span>
                <img src="/public/web/images/13@2x.png" alt="">
                <span>优化建议</span>
           </span>
        </div>
    </div>
</div>

<div class="index_select_we" style="width: 92%;margin: auto">
    <h1 class="index_sw_title">为什么<span>选择我们</span></h1>
    <div class="index_sw_list">
        <div class="index_sw_item">
            <h1>权威</h1>
            <p>数据来源于各大权威机构。帮您全方位检测信用问题，根据《征信业管理条例》相关规定，如您对报告有异议，可提交申诉。</p>
            <div class="index_qwi_content">
                <img class="bg1" src="/public/web/images/homepage_advantage_1_bg@2x.png" alt="">
                <img class="bg2" src="/public/web/images/homepage_advantage_1_phone@2x.png" alt="">
                <img class="bg3" src="/public/web/images/homepage_advantage_1_Fill 1@2x.png" alt="">
                <img class="bg4" src="/public/web/images/homepage_advantage_1_Fill 10@2x.png" alt="">
                <img class="bg5" src="/public/web/images/homepage_advantage_1_Fill 16@2x.png" alt="">
                <img class="bg6" src="/public/web/images/homepage_advantage_1_Fill 18@2x.png" alt="">
                <img class="bg7" src="/public/web/images/homepage_advantage_1_Leaf Me Alone!@2x.png" alt="">
                <img class="bg8" src="/public/web/images/homepage_advantage_1_Leaf@2x.png" alt="">
                <img class="bg9" src="/public/web/images/homepage_advantage_1_Twisted Leaf@2x.png" alt="">
                <img class="bg10" src="/public/web/images/homepage_advantage_1Leaf@2x.png" alt="">
                <img class="bg11" src="/public/web/images/homepage_advantage_1_chapter@2x.png" alt="">
                <img class="ph1" src="/public/web/images/homepage_advantage_1_Chart@2x.png" alt="">
                <!--<img class="ph2" src="/public/web/images/homepage_advantage_1_Value2@2x.png" alt="">
                <img class="ph3" src="/public/web/images/homepage_advantage_1_Value3@2x.png" alt="">-->
                <img class="ln1" src="/public/web/images/Value@2x.png" alt="">
                <img class="ln2" src="/public/web/images/Value-1@2x.png" alt="">
                <img class="ln3" src="/public/web/images/Value-2@2x.png" alt="">
                <img class="ln4" src="/public/web/images/Value-3@2x.png" alt="">
                <img class="ln5" src="/public/web/images/Value-4@2x.png" alt="">
                <img class="row1" src="/public/web/images/Row1@2x.png" alt="">
                <img class="row2" src="/public/web/images/Row2@2x.png" alt="">
                <img class="row3" src="/public/web/images/homepage_advantage_1_Row3@2x.png" alt="">
            </div>
        </div>
        <div class="index_sw_item">
            <h1>专业</h1>
            <p>拥有10年开发经验的技术团队以及强大的售后服务团队已经为20万+用户提供全面的数据分析与优化方案。</p>
            <div class="index_zyi_content">
                <img class="bg" src="/public/web/images/Background Shape@2x.png" alt="">
                <img class="ca1" src="/public/web/images/Big Cards@2x.png" alt="">
                <img class="ca2" src="/public/web/images/Cards@2x.png" alt="">
                <img class="ca3" src="/public/web/images/Cells@2x.png" alt="">
                <img class="ca4" src="/public/web/images/Checkmark!@2x.png" alt="">
                <img class="ca5" src="/public/web/images/List@2x.png" alt="">
                <img class="ca6" src="/public/web/images/Profile@2x.png" alt="">
                <img class="ca7" src="/public/web/images/Whiteboard Stuff@2x.png" alt="">
                <img class="ma1" src="/public/web/images/man@2x.png" alt="">
                <img class="ma2" src="/public/web/images/girl@2x.png" alt="">
                <img class="ma3" src="/public/web/images/hair@2x.png" alt="">
            </div>
        </div>

        <div class="index_sw_item">
            <h1>精准</h1>
            <p>数据来源于各大权威机构。帮您全方位检测信用问题，根据《征信业管理条例》相关规定，如您对报告有异议，可提交申诉。</p>
            <div class="index_jzi_content">
                <img class="bg" src="/public/web/images/Background Shape 2@2x.png" alt="">
                <img class="ph" src="/public/web/images/phone@2x.png" alt="">
                <img class="ji1" src="/public/web/images/Leaf Me Alone!@2x.png" alt="">
                <img class="ji2" src="/public/web/images/Leaf Me Alone!-1@2x.png" alt="">
                <img class="ji3" src="/public/web/images/Fill 55@2x.png" alt="">
                <img class="ji4" src="/public/web/images/Pie Chart@2x.png" alt="">
                <img class="ji5" src="/public/web/images/Leaf Bottom@2x.png" alt="">
                <img class="ji6" src="/public/web/images/Fill 16@2x.png" alt="">
                <img class="ji7" src="/public/web/images/Fill 18@2x.png" alt="">
                <img class="row1" src="/public/web/images/Row@2x.png" alt="">
                <img class="row2" src="/public/web/images/Row4@2x.png" alt="">
                <img class="row3" src="/public/web/images/Row3@2x.png" alt="">
                <img class="row4" src="/public/web/images/Row4@2x.png" alt="">
                <img class="bg1" src="/public/web/images/bg-target@2x.png" alt="">
                <img class="bg2 wow animated" src="/public/web/images/arrow@2x.png" alt="">
            </div>
        </div>
        <div class="index_sw_item">
            <h1>安全</h1>
            <p>数据来源于各大权威机构。帮您全方位检测信用问题，根据《征信业管理条例》相关规定，如您对报告有异议，可提交申诉。</p>
            <div class="index_aqi_content">
                <img class="bg" src="/public/web/images/bg@2x.png" alt="">
                <img class="ph" src="/public/web/images/phone@2x.png" alt="">
                <img class="ai1" src="/public/web/images/Fill 16@2x.png" alt="">
                <img class="ai2" src="/public/web/images/Fill 18@2x.png" alt="">
                <img class="ai3" src="/public/web/images/Leaf-4@2x.png" alt="">
                <img class="ai4" src="/public/web/images/query1377@2x.png" alt="">
                <img class="ai5" src="/public/web/images/Image@2x.png" alt="">
                <img class="row1" src="/public/web/images/Row@2x.png" alt="">
                <img class="row2" src="/public/web/images/Row4@2x.png" alt="">
                <img class="row3" src="/public/web/images/Row3@2x.png" alt="">
                <div class="bg1">
                    <img src="/public/web/images/Shield@2x.png" alt="">
                    <i class="i"></i>
                </div>
                <img class="bg2" src="/public/web/images/lock@2x.png" alt="">
                <img class="ai6" src="/public/web/images/Leaf-2@2x.png" alt="">
                <img class="ai7" src="/public/web/images/Leaf-1@2x.png" alt="">
                <img class="ai8" src="/public/web/images/Leaf-3@2x.png" alt="">
                <img class="ai9" src="/public/web/images/Leaf@2x.png" alt="">
            </div>
        </div>
        <div class="index_sw_item">
            <h1>高效</h1>
            <p>数据来源于各大权威机构。帮您全方位检测信用问题，根据《征信业管理条例》相关规定，如您对报告有异议，可提交申诉。</p>
            <div class="index_gxi_content">
                <img class="bg" src="/public/web/images/gx_bg.png" alt="">
                <img class="bg1" src="/public/web/images/Leaf-2@2x.png" alt="">
                <img class="bg2" src="/public/web/images/Leaf-1@2x.png" alt="">
                <img class="bg3" src="/public/web/images/Leaf@2x.png" alt="">
                <img class="bg4" src="/public/web/images/Leaf-3@2x.png" alt="">
                <img class="bg5" src="/public/web/images/Leaf Bottom@2x.png" alt="">
                <img class="bg6" src="/public/web/images/rocket1@2x.png" alt="">
            </div>
        </div>
    </div>
</div>
<!--关于我们-->
<div class="about_container">
    <div class="sprdetail_content">
        <div class="sprabout_title">
            关于我们
        </div>
        <div class="sprabout_content">
            西安铭樊金融信息服务有限公司成立于2017年8月31日，企业秉承着“需求决定市场、市场引领技术、技术指导市场”的理念，依托自身多年的大数据应用技术和实践，迅速推出了大数据平台类产品以及行业解决方案，旗下大数据服务品牌，为众多企业发展带来了新的增涨点，同时为大数据行业发展提供了实践蓝本！未来，我们将在大数据领域持续深耕，力求将最好的技术、最好的产品带向市场，带给用户。
        </div>
    </div>
</div>
<!--联系我们-->
<div class="contact_container">
    <div class="sprdetail_content">
        <div class="sprabout_title">
            联系我们
        </div>
        <div class="spracontact_content">
            <div class="contact_item">
                <div class="contact_item_lt">
                    <span>商务合作:</span>
                    <span>023-65650891</span>
                </div>
                <a class="contact_item_gt" href="tel:023-65650891">立即拨打</a>
            </div>
            <div class="contact_item">
                <div class="contact_item_lt">
                    <span>邮箱:</span>
                    <span id="codeNum">qianzong0807@163.com</span>
                </div>
                <div class="contact_item_gt" id="codeBtn" data-clipboard-target="#input">点击复制</div>
            </div>
            <div class="contact_item">
                <div class="contact_item_lt">
                    <span>版本</span>
                    <span>V4.0.2</span>
                </div>
            </div>
        </div>
    </div>
</div>
<textarea id="input" style="opacity: 0;height: 0.05rem" ></textarea>
<div class="ra_myFx_footer">
    <div class="ra_myFxf_top">
        <img src="/public/web/images/icons-report@2x.png" style="width: 0.14rem;" alt="">
        <span>为保证您的信息安全，请确保本人查询</span>
    </div>
    <div class="ra_myFxf_aq">
        <img src="/public/web/images/aq79@2x.png" style="width: 0.2rem;" alt="">
        <span>数据安全声明</span>
    </div>
    <p>您的个人信息仅用作信息报告查询</p>
    <p>采用MD5+RSA加密算法，银行级数据保护</p>

    <p>我们正在保护您的数据安全</p>
</div>
<!--手机绑定弹出框-->
<div id="cover"></div>
<div id="modal">
    <div class="login_box login_cover_box">
        <h1 class="login_title login_title_cover">绑定手机</h1>
        <div class="login_input">
            <div class="login_input_item">
                <img src="/public/web/images/icon-phone%20number@2x.png" style="width: 0.25rem" alt="">
                <input name="tel" id="telphone" type="text" placeholder="请输入您的手机号码">
            </div>
            <div class="login_input_item login_iitwo">
                <div class="login_iitwo_left">
                    <img src="/public/web/images/icon-Verification%20code@2x.png" style="width: 0.25rem" alt="">
                    <input type="text" id="yzm" placeholder="请输入短信验证码">
                </div>
                <span class="register_getCode getcode1" onclick="code1($(this),$('#telphone'))">获取验证码</span>
                <span class="register_getCode codetime1"></span>
            </div>
        </div>
        <div class="tips">温馨提示：无需注册直接登陆</div>
        <div class="login_btn">
            <img  id="close" style="width: 1.32rem;" src="/public/web/images/botton-cancel@2x.png" alt="">
            <img  onclick="goLogin()"   style="width: 1.32rem;" src="/public/web/images/botton-Landing@2x.png" alt="">
        </div>
        <input id="agreememt" value="1" type="hidden" />
        <p class="login_agreement">
            <span class="login_agreement_dot login_ad_in"></span>
            <span class="login_agreement_data">同意大数据查询<a href="/license">《授权协议》</a></span>
        </p>
    </div>
</div>
<!--信息提交确认框-->
<div id="cover_confirm"></div>
<div id="modal_confirm">
    <div class="oc_box" style="box-shadow: 0 0 0">
        <div class="oc_jdfx">
            <img src="/public/web/images/illustration@2x.png" style="width: 0.41rem;" alt="">
            <p><?php echo htmlentities($category['name']); ?></p>
        </div>
        <div class="oc_list">
            <p>
                <span>姓名</span>
                <span class="username"></span>
            </p>
            <p>
                <span>身份证</span>
                <span class="sfz"></span>
            </p>
            <p>
                <span>手机号</span>
                <span class="tel"></span>
            </p>
        </div>
        <div class="oc_itext">
            <img src="/public/web/images/icon-confirm@2x.png" style="width: 0.22rem;"  alt="">
            <span>请确认您输入的信息是否正确？</span>
        </div>
        <div class="confirm_btn">
            <img class="revise_btn" src="/public/web/images/botton-revise@3x.png">
            <img onclick="doSearch()" src="/public/web/images/botton-confirm@3x.png">
        </div>
    </div>
</div>
<!--签名确认框-->
<div id="cover_qm"></div>
<div id="modal_qm">
    <div class="query_cil_item" style="margin-top: 0">
        <p>电子签名</p>
        <div class="canvasDiv">
            <div id="editing_area">
                <canvas id="canvasEdit"></canvas>
            </div>
        </div>
        <div class="imgDiv">
            <span id="sign_show"></span>
        </div>
        <div class="btnDiv">
            <a id="sign_clear" class="clearBtn sign_btn_commen">清除</a>
            <a id="sign_ok" class="okBtn sign_btn_commen">确认</a>
        </div>
    </div>
</div>
<input type="hidden" value="<?php echo htmlentities($system['is_sign']); ?>" id="issignshow">
<script>
    document.write('<script src="/public/web/js/index.js?v=3"><\/script>');
    document.write('<script src="/public/web/js/query.js?v=3"><\/script>');
    document.write('<script src="/public/web/js/public.js?v=3"><\/script>');
    document.write('<script src="/public/web/js/esign.js?v=3"><\/script>');
</script>
<script src="/public/web/js/clipboard.min.js"></script>

<script>
    var telphoneNum = "<?php echo htmlentities($user['tel']); ?>";

    $(function () {
        // 电子签名
        //初始化动作，根据DOM的ID不同进行自定义，如果不写则内部默认取这四个
        $().esign("canvasEdit", "sign_show", "sign_clear", "sign_ok");
        $("#codeBtn").click(function(){
            let e = document.getElementById("codeNum").innerText;
            let t = document.getElementById("input");
            t.value = e;
            let clipboard = new ClipboardJS('#codeBtn');
            clipboard.on("success", function(e){
                alert("复制成功");
                e.clearSelection();
            });
            clipboard.on("error", function(e){
                alert("请选择“拷贝”进行复制!");
            });
        });
    })

    function queren(names,tels,shengfs){
        $("#username").val(names);
        $("#sfz").val(shengfs);
        $("#tel").val(tels);
    }
    // 获取验证码
    var issend = true;
    var isok=true;
    $(".codetime1").hide();
    $(".getcode1").show();
    function code1(e,telphone) {
        var tel = telphone.val();
        if(tel==''){
            alert('手机号不能为空！');return false;
        }
        if(!issend){
            alert('短信已发送！');
        }
        $(".codetime1").show();
        $(".getcode1").hide();
        isSend = false;
        $.ajax({
            url: www + "/getBindTelCode",
            type: 'POST',
            async: false,
            dataType: 'json',
            data: {tel: tel},
            success: function (res) {
                if (res.status != 1) {
                    alert(res.msg);
                    isok = false;
                }
            },
            error: function () {
                alert('网络错误！');
                isok = false;
            }
        });
        if (!isok) {
            return false;
        }
        var timeo = 60;
        $(".codetime1").text('重新获取（' + timeo + 's）');
        var timeStop = setInterval(function () {
            timeo--;
            if (timeo > 0) {
                $(".codetime1").show();
                $(".getcode1").hide();
                $(".codetime1").text('重新获取（' + timeo + 's）');
                e.addClass('disabled_click');//禁止点击
            } else {
                $(".codetime1").hide();
                $(".getcode1").show();
                timeo = 60;//当减到0时赋值为60
                e.text('获取验证码');
                clearInterval(timeStop);//清除定时器
                e.removeClass('disabled_click');//移除属性，可点击
            }
        }, 1000)
    }
</script>

</body>
</html>