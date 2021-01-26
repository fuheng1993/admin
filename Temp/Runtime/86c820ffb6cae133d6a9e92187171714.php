<?php /*a:2:{s:55:"/www/wwwroot/xy.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;s:49:"/www/wwwroot/xy.qcq.cc/App/Views/index/index.html";i:1589609627;}*/ ?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlentities($system['title']); ?></title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <link type="text/css" rel="stylesheet" href="/public/web/css/animate.min.css" />
    <link rel="stylesheet" href="/public/web/css/public.css?v=2.0">
</head>
<body>

<div class="index_box">
    <div class="index_top_box">
         <a class="index_ti_login" <?php if($user['id']): ?> href="/login"<?php endif; ?> >
            <div class="index_top_headImg">
                <img src="<?php if(empty($user) || (($user instanceof \think\Collection || $user instanceof \think\Paginator ) && $user->isEmpty())): ?>/public/web/images/proffle.png<?php else: ?><?php echo htmlentities($user['avatar']); ?><?php endif; ?>" alt="">
            </div>
         </a>
        <div class="index_top_info">
            <div class="index_ti_top">
                <img src="/public/web/images/horn@2x.png" alt="">
                <div class="index_tit_mes">
                    <div class="index_titm_con">
                        <?php if(is_array($tel) || $tel instanceof \think\Collection || $tel instanceof \think\Paginator): $i = 0; $__LIST__ = $tel;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                            <p>客户<?php echo htmlentities($v); ?>查询了网贷黑名单</p>
                        <?php endforeach; endif; else: echo "" ;endif; ?>

                    </div>
                </div>
            </div>
            <?php if((!isset($user))): ?>
            <a class="index_ti_login" href="/login">登录/注册</a>
            <?php else: ?>
            <a href="/user">
                <?php if(($user['tel'])): ?>
                已绑定<?php echo substr_replace($user['tel'],'****',3,4); else: ?>
                未绑定手机号
                <?php endif; ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <a href="/jdfx_xb">
        <div class="index_dataqw_box">
            <p>数据权威</p>
            <p>100%加密 信息安全</p>
            <div class="index_dataqw_img">
                <img src="/public/web/images/homepage_banner_man@2x.png" alt="">
                <img class="qw_img1" src="/public/web/images/homepage_banner_grass1@2x.png" alt="">
                <img class="qw_img2" src="/public/web/images/homepage_banner_grass2@2x.png" alt="">
                <img class="qw_img3" src="/public/web/images/homepage_banner_grass3@2x.png" alt="">
                <img class="qw_img4" src="/public/web/images/homepage_banner_grassline1@2x.png" alt="">
                <img class="qw_img5" src="/public/web/images/homepage_banner_grassline2@2x.png" alt="">
                <img class="qw_img6" src="/public/web/images/homepage_banner_grass4@2x.png" alt="">
                <img class="qw_img7" src="/public/web/images/homepage_banner_grassline3@2x.png" alt="">
            </div>
        </div>
    </a>
    <a href="/jdfx_jb">
        <div style="overflow: auto;width: 100%">
            <div class="index_analyze_box">
                <img class="index_ab_hot" src="/public/web/images/tag-hot@2x.png" alt="">
                <div class="analyze_ing_cont">
                    <img class="bg1" src="/public/web/images/homepage_hot_grass1@2x.png" alt="">
                    <img class="bg2" src="/public/web/images/homepage_hot_grass2@2x.png" alt="">
                    <img class="bg3" src="/public/web/images/homepage_hot_grass3@2x.png" alt="">
                    <img class="bg4" src="/public/web/images/homepage_hot_grassline1@2x.png" alt="">
                    <img class="bg5" src="/public/web/images/homepage_hot_grassline2@2x.png" alt="">
                </div>
                <img class="index_ab_bg2" src="/public/web/images/ijdfxbg1.png" alt="">
                <div class="index_ab_top">
                    <img src="/public/web/images/illustration@2x.png" alt="">
                    <div>
                        <p class="index_abt_tit">
                            <span>反欺诈简版</span>
                            <span>3021312人已检测</span>
                        </p>
                        <p class="index_abt_mes">查清原因 更好借</p>
                    </div>
                </div>
                <div class="index_ab_bottom">
                    <span>羊毛党名单</span>
                    <span>欺诈风险名单</span>
                    <span>反欺诈行为</span>
                    <span>逾期记录</span>
                    <span>被机构查询信息</span>
                    <span>多头申请记录</span>
                    <span>个人风险信息</span>
                </div>
                <a href="/jdfx_jb">
                    <div class="breathe-btn">
                        <img src="/public/web/images/01-01-hxdjt@2x.png" alt="">
                    </div>
                    <div class="breathe-btnroll"></div>
                </a>
            </div>
        </div>
    </a>
    <div class="index_ctwo_box">
        <a style="display:block" class="index_ctwob_item" href="/jdfx_xb">
            <img class="index_cti_imgl" src="/public/web/images/illustration2.png" alt="">
            <h1>反欺诈详版</h1>
            <p>反欺诈行为 多头申请记录</p>
            <img class="index_ctwobi_new" src="/public/web/images/tag-new.png" alt="">
        </a>
        <a style="display:block" class="index_ctwob_item" href="/changhuan">
            <img class="index_cti_imgl" src="/public/web/images/illustration2.png" alt="">
            <h1>偿还能力评估</h1>
            <p>消费能力 偿还能力评估</p>
            <img class="index_ctwobi_new" src="/public/web/images/tag-new.png" alt="">
        </a>
    </div>
    <?php if($is_pay==1): ?>
    <div class="index_select_we">
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
            <h1 class="index_sw_title" style="margin-top: 0.6rem;">看看我们客户的<span>评价吧</span></h1>

            <div class="index_banner_box">
                <div class="index_banner_list">
                    <img class="index_bb_bg" src="/public/web/images/isdh.png" alt="">
                    <?php if(is_array($comment) || $comment instanceof \think\Collection || $comment instanceof \think\Paginator): $i = 0; $__LIST__ = $comment;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
                        <div class="index_bl_item">
                            <div class="index_bb_top">
                                <div class="index_bbt_img">
                                    <img src="<?php if((empty($v['user']['litpic']))): ?>/public/web/images/Head-Front.png<?php else: ?><?php echo htmlentities($v['user']['litpic']); ?><?php endif; ?>"
                                         alt="">
                                </div>
                                <div class="index_bbt_info">
                                    <h1>用户<?php echo substr_replace($v['tel'],'****',3,4); ?></h1>
                                    <p><?php echo htmlentities($v['create_time']); ?></p>
                                </div>
                            </div>
                            <div class="index_bb_bottom">
                                <?php echo $v['body']; ?>
                            </div>
                        </div>
                    <?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
                <div class="index_banner_dot">

                </div>

            </div>
            <a href="JavaScript:">
                <div class="index_go_query">
                    <img style="width:2.3rem" src="/public/web/images/button.png" alt="">
                </div>
            </a>
        </div>
    </div>
    <?php endif; ?>
    <div class="index_select_we">
        <h1 class="index_sw_title">互金热点</h1>
        <div class="index_hot_list">
            <?php if(is_array($article) || $article instanceof \think\Collection || $article instanceof \think\Paginator): $i = 0; $__LIST__ = $article;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
            <div class="index_hl_item" data-url="<?php echo htmlentities($v['url']); ?>">
                <div class="index_hli_left">
                    <img style="width: 100%;border-radius: 0.05rem" src="<?php echo htmlentities($v['litpic']); ?>" alt="<?php echo htmlentities($v['title']); ?>">
                </div>
                <div class="index_hli_right">
                    <p class="index_hlir_title"><a><?php echo htmlentities($v['title']); ?></a></p>
                    <p class="index_hlir_bot">
                        <img src="/public/web/images/icons-visitor.png" alt="">
                        <span><?php echo htmlentities($v['hits']); ?></span>
                        <span style="margin-left: 0.2rem;"><?php echo htmlentities($v['create_time']); ?></span>
                    </p>
                </div>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div class="index_select_we">
        <h1 class="index_sw_title">关注<span>我们</span></h1>
        <div class="index_gz_we">
            <img src="<?php echo htmlentities($system['qr_code']); ?>" alt="<?php echo htmlentities($system['title']); ?>">
        </div>
        <div class="index_gz_btn">
            <img src="/public/web/images/button-2@2x.png" style="width: 2.3rem;" alt="">
        </div>
    </div>
</div>
<!--右边固定按钮-->
<div class="index_right_button">
    <span class="index_rb_sz"></span>
    <div class="index_right_menu">
        <a href="/"> <img src="/public/web/images/botton-home@3x.png" alt=""> </a>
        <a href="http://zxcx.weixun8.com/wxkf/2"> <img src="/public/web/images/botton-service@3x.png" alt=""> </a>
        <!--<a href="https://zqszxxjsyxgs.qiyukf.com/client?k=a1f0bfd5b2150459504586d5971439f3&wp=1"> <img src="/public/web/images/botton-service@3x.png" alt=""> </a>-->
        <a href="http://xy.qcq.cc/User/Index.html"> <img src="/public/web/images/botton-profile@3x.png" alt=""> </a>
        <a href="http://xy.qcq.cc/User/History.html"> <img src="/public/web/images/botton-history@3x.png" alt=""> </a>
    </div>
</div>
<!--同意授权弹框-->
<div id="cover_sq" class="cover_sq"></div>
<div id="modal_sq" class="modal_sq">
    <div class="container">
        <div class="content">
            <div class="title">温馨提示</div>
            <div class="text">
                尊敬的用户，我们十分重视您的个人信息安全，在您使用本产品服务之前，请务必认真阅读<a href="http://xy.qcq.cc/shouquanxieyi.html"
                                                           style="color: #28CE58">《授权协议》</a>以及<a
                    href="http://xy.qcq.cc/yinsizhengce.html" style="color: #28CE58">《隐私政策》</a>全部内容。我们需要获得您的同意，方可为您提供优质服务。
            </div>
        </div>
        <a href="javascript:" class="sq_btn">
            同意
        </a>
    </div>
</div>
<input type="hidden" value="<?php echo htmlentities($is_agreement); ?>" id="isAgree">
<div style="height: 0.5rem"></div>
<script src="/public/web/js/wow.js"></script>
<script src="/public/web/js/jquery-3.3.1.min.js"></script>
<script src="/public/web/js/public.js"></script>
<script src="/public/web/js/index.js"></script>

<script>
    function gomyinfo(){
        window.location.href="/user";
    }
    $(function () {
        console.log($("#isAgree").val())
        $(".sq_btn").click(function () {
            $("#cover_sq").hide();
            $("#modal_sq").hide();
            $("#isAgree").val(1)
        })
        $(".index_hl_item").click(function () {
            window.location.href=$(this).attr('data-url');
        })

        if($("#isAgree").val()==1){
            $("#cover_sq").hide();
            $("#modal_sq").hide();
        }else{
            $("#cover_sq").show();
            $("#modal_sq").show();

        }
    })
</script>
</body>

</html>
