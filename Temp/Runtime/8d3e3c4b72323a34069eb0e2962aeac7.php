<?php /*a:2:{s:55:"/www/wwwroot/xy.qcq.cc/App/Views/api/payment/jsapi.html";i:1588842575;s:67:"/www/wwwroot/xy.qcq.cc/App/Views/index/search/showRefuseReport.html";i:1589792941;}*/ ?>
<?php
    $result = ['A' => '属于无风险区域', 'B' => '属于风险较低区域', 'C' => '属于风险可控区域', 'D' => '属于风险危害区域 ', 'E' => '属于风险高危区域'];
    $xydj =
    ['A' => '信用等级A，信用极好，建议直接通过', 'B' => '信用等级B，信用良好，建议通过', 'C' => '信用等级C，信用一般，建议人工复审', 'D' => '信用等级D，信用较差，建议严格复审', 'E' => '信用等级D，信用极差，建议直接拒绝'];
    $pfjx =
    ['A' => '信用等级A，属于基本上没有风险的区域', 'B' => '信用等级B，属于风险较低的区域', 'C' => '信用等级C，属于风险可控的区域', 'D' => '信用等级D，属于风险危害的区域', 'E' => '信用等级E，属于风险高危的区域'];
    $sf_num = $sj_num = 0;
        if (!empty($data['body']['content']['person']['detections'])) {
            foreach ($data['body']['content']['person']['detections'] as $v) {
                if ($v['conclusion'] != '无风险' && $v['conclusion'] != '通过') {
                $sf_num++;
                }
            }
            foreach ($data['body']['content']['phone']['detections'] as $v) {
                if ($v['conclusion'] != '无风险' && $v['conclusion'] != '通过') {
                $sj_num++;
                }
            }

        }


    function dengji($text)
    {
        $res = '';
        //风险等级
        switch ($text) {
        case '无风险':
        $res = 'text_green';
        break;
        case '中风险':
        $res = 'text_yellow';
        break;
        case '高风险':
        $res = 'text_red';
        break;
        case '通过':
        $res = 'text_green';
        break;
        default:
        $res = 'text_red';
        }
        return $res;
    }

    function dj($text)
    {
        $res = '';
        //风险等级
        switch ($text) {
            case '无风险':
            $res = '低';
            break;
            case '中风险':
            $res = '中';
            break;
            case '高风险':
            $res = '高';
            break;
            case '通过':
            $res = '低';
            break;
            default:
            $res = '高';
        }
        return $res;
    }

    //圆环数值
    function value($text)
    {
        $res = '';
        //风险等级
        switch ($text) {
            case '高':
            $res = '55';
            break;
            case '中':
            $res = '20';
            break;
            case '低':
            $res = '1';
            break;
        }
        echo $res;
    }
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlentities($category['name']); ?>-<?php echo htmlentities($system['title']); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, user-scalable=no,viewport-fit=cover">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="format-detection" content="telphone=no, email=no" />
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="/public/web/project/css/reset.css">
    <link rel="stylesheet" href="/public/web/css/jdfx_details_new_a.css?v=1.2">
    <link rel="stylesheet" href="/public/web/css/iconfont_new_a.css">
    <script src="/public/web/js/jquery-3.3.1.min.js"></script>
    <script src="https://cdn.bootcss.com/echarts/4.1.0.rc2/echarts.min.js"></script>
    <script src="/public/web/js/public.js"></script>
    <!--    <script src="/public/web/js/mt-tabpage.js"></script>-->
</head>
<body>

<div class="jdfx_container">
    <div class="report">
        <div class="topHeight">
            <div class="ra_top_circle">
                <div class="fensug">
                    <div class="wavenum "><b id="denfenjs"><span><?php echo htmlentities($data['body']['result']); ?></span></b>
                    </div>
                    <div class="waven red">
                        <div class="wave">&nbsp;</div>
                        <div class="waveXf">&nbsp;</div>
                    </div>
                </div>
                <div class="ra_tc_jy">
                    <?php echo htmlentities($xydj[$data['body']['result']]); ?>
                </div>

            </div>
            <div class="top_info">
                <div>时间：<?php echo htmlentities($data['time']); ?></div>
                <div>有效期：仅限本次查询</div>
                <div>编号：<?php echo htmlentities($data['seq']); ?></div>
                <div>如有需要请自行保存报告</div>
            </div>
        </div>
        <div class="jdfx_content">
            <div class="tab_title">
                <a class="identity_risk tab_active" href=" ">身份风险 <?php if(($sf_num>0)): ?><span class="num num_active"><?php echo htmlentities($sf_num); ?></span><?php endif; ?>
                </a>
                <a class="phone_risk" href="#phone_risk">手机风险 <?php if(($sj_num>0)): ?><span class="num"><?php echo htmlentities($sj_num); ?></span><?php endif; ?>
                </a>
            </div>

            <!--tab固定导航切换-->
            <div class="fixed_tab">
                <a class="tab_item tab1 tab_active" href="#identity_risk">
                    <span>身份风险</span>
                    <?php if(($sf_num>0)): ?>
                    <span class="num num_active"><?php echo htmlentities($sf_num); ?></span>
                    <?php endif; ?>
                    <!--                    <span class="num num_active">2</span>-->
                </a>
                <a class="tab_item tab2" href="#phone_risk">
                    <span>手机风险</span>

                    <?php if(($sj_num>0)): ?>
                    <span class="num"><?php echo htmlentities($sj_num); ?></span>
                    <?php endif; ?>
                    <!--                    <span class="num">1</span>-->
                </a>
                <div class="tab_item tab3">
                    <div style="margin-top: 0.05rem">信用等级：<?php echo htmlentities($data['body']['result']); ?></div>
                    <div>决策建议：<?php echo htmlentities($data['body']['suggestion']); ?></div>
                </div>
            </div>
            <div id="identity_risk" style="height: 0.2rem"></div>
            <div class="content_risk">
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>评分解析</span>
                    </div>
                    <div class="content">
                        <div class="gray_box">
                            <div class="title"><?php echo htmlentities($pfjx[$data['body']['result']]); ?></div>
                            <div class="text">
                                等级是基于当前时间的相关数据，依据风险模型进行计算得到的
                            </div>
                        </div>
                    </div>
                </div>
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>身份基本信息</span>
                    </div>
                    <ul class="content">
                        <li>
                            <div class="lt">姓名</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['person']['info']['name']); ?></div>
                        </li>
                        <li>
                            <div class="lt">性别</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['person']['info']['gender']); ?></div>
                        </li>
                        <li>
                            <div class="lt">年龄</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['person']['info']['age']); ?></div>
                        </li>
                        <li>
                            <div class="lt">出生日期</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['person']['info']['brithdate']); ?></div>
                        </li>
                        <li>
                            <div class="lt">身份证号</div>

                            <div class="gt"><?php echo htmlentities($data['body']['content']['person']['info']['idNumber']); ?></div>
                        </li>
                        <!--                        <li>-->
                        <!--                            <div class="lt">所属省份</div>-->
                        <!--                            <div class="gt">山东省</div>-->
                        <!--                        </li>-->
                        <!--                        <li>-->
                        <!--                            <div class="lt">所属城市</div>-->
                        <!--                            <div class="gt">菏泽市</div>-->
                        <!--                        </li>-->
                    </ul>
                </div>
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>身份风险综述</span>
                    </div>
                    <div class="content analysis-box-list">
                        <div class="rerult_item">
                            <div class="iconfont iconyiwen text_gray"></div>
                            <div class="iconfont iconbank <?php echo dengji($data['body']['content']['person']['detections'][0]['conclusion']); ?>"></div>
                            <div class="item_bottom text_gray">身份信息 </div>
                            <div class="item_middle <?php echo dengji($data['body']['content']['person']['detections'][0]['conclusion']); ?>"><?php echo htmlentities($data['body']['content']['person']['detections'][0]['conclusion']); ?></div>
                            <input type="hidden" value="<?php echo htmlentities($data['body']['content']['person']['detections'][0]['analysis']); ?>">
                        </div>
                        <div class="rerult_item">
                            <div class="iconfont iconyiwen text_gray"></div>
                            <div class="iconfont iconman <?php echo dengji($data['body']['content']['person']['detections'][1]['conclusion']); ?>"></div>
                            <div class="item_bottom text_gray">司法风险</div>
                            <div class="item_middle <?php echo dengji($data['body']['content']['person']['detections'][1]['conclusion']); ?>"><?php echo htmlentities($data['body']['content']['person']['detections'][1]['conclusion']); ?></div>
                            <input type="hidden" value="<?php echo htmlentities($data['body']['content']['person']['detections'][1]['analysis']); ?>">
                        </div>
                        <div class="rerult_item">
                            <div class="iconfont iconyiwen text_gray"></div>
                            <div class="iconfont iconmulti <?php echo dengji($data['body']['content']['person']['detections'][2]['conclusion']); ?>"></div>
                            <div class="item_bottom text_gray">多头借贷</div>
                            <div class="item_middle <?php echo dengji($data['body']['content']['person']['detections'][2]['conclusion']); ?>"><?php echo htmlentities($data['body']['content']['person']['detections'][2]['conclusion']); ?></div>
                            <input type="hidden" value="<?php echo htmlentities($data['body']['content']['person']['detections'][2]['analysis']); ?>">
                        </div>
                        <div class="rerult_item">
                            <div class="iconfont iconyiwen text_gray"></div>
                            <div class="iconfont iconcourt <?php echo dengji($data['body']['content']['person']['detections'][3]['conclusion']); ?>"></div>
                            <div class="item_bottom text_gray">多头逾期</div>
                            <div class="item_middle <?php echo dengji($data['body']['content']['person']['detections'][3]['conclusion']); ?>"><?php echo htmlentities($data['body']['content']['person']['detections'][3]['conclusion']); ?></div>
                            <input type="hidden" value="<?php echo htmlentities($data['body']['content']['person']['detections'][3]['analysis']); ?>">
                        </div>


                    </div>
                </div>

                <!--司法风险-->
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>司法风险</span>
                        <?php $sffx = $data['body']['content']['person']['detections'][1]['details'];?>
                    </div>
                    <div class="content">
                        <div class="fkcg_roll">
                            <div class="fkcg_box fkcg_box_lt fkcg_box_lt_new">
                                <div class="circle">
                                    <div class="pie_left">
                                        <div class="left"></div>
                                    </div>
                                    <div class="pie_right">
                                        <div class="right"></div>
                                    </div>
                                    <div class="mask">
                                        <input type="hidden" value="<?php echo value(dj($data['body']['content']['person']['detections'][1]['conclusion'])); ?>">
                                        <span><?php echo dj($data['body']['content']['person']['detections'][1]['conclusion']); ?></span>
                                    </div>
                                </div>
                                <div>司法风险</div>
                            </div>
                            <div class="fkcg_box fkcg_box_gt fkcg_box_lt_new">
                                <div style="font-size: 0.3rem;margin-top:0.5rem "><?php echo array_sum([count($sffx[0]['values']),count($sffx[1]['values'])]); ?></div>
                                <div style="margin-top: 0.4rem">信息条数</div>
                            </div>
                        </div>
                        <div class="gray_box">
                            <div class="title">结论分析</div>
                            <div class="text"><?php echo htmlentities($data['body']['content']['person']['detections'][1]['analysis']); ?></div>
                        </div>
                        <?php if(($sffx[0]['values'])): ?>
                        <div class="details_box">
                            <div class="title">
                                <span></span>
                                <span>被执行情况</span>
                            </div>
                            <?php foreach($sffx[0]['values'] as $v): ?>
                            <div class="gray_box">
                                <div class="item">
                                    <div class="lt">案由</div>
                                    <div class="gt"><?php echo htmlentities($v[5]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">案号</div>
                                    <div class="gt"><?php echo htmlentities($v[0]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">法院</div>
                                    <div class="gt"><?php echo htmlentities($v[1]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">立案时间</div>
                                    <div class="gt"><?php echo htmlentities($v[2]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">更新时间</div>
                                    <div class="gt"><?php echo htmlentities($v[3]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">案件状态</div>
                                    <div class="gt"><?php echo htmlentities($v[4]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">案件结果</div>
                                    <div class="gt"><?php echo htmlentities($v[6]); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>

                        </div>
                        <?php endif; if(($sffx[1]['values'])): ?>
                        <div class="details_box">
                            <div class="title">
                                <span></span>
                                <span>涉案情况</span>
                            </div>
                            <?php foreach($sffx[1]['values'] as $v): ?>
                            <div class="gray_box">
                                <div class="item">
                                    <div class="lt">案由</div>
                                    <div class="gt"><?php echo htmlentities($v[5]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">案号</div>
                                    <div class="gt"><?php echo htmlentities($v[0]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">法院</div>
                                    <div class="gt"><?php echo htmlentities($v[1]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">立案时间</div>
                                    <div class="gt"><?php echo htmlentities($v[2]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">更新时间</div>
                                    <div class="gt"><?php echo htmlentities($v[3]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">案件状态</div>
                                    <div class="gt"><?php echo htmlentities($v[4]); ?></div>
                                </div>
                                <div class="item">
                                    <div class="lt">案件结果</div>
                                    <div class="gt"><?php echo htmlentities($v[6]); ?></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!--多头借款风险-->
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>多头借贷</span>
                    </div>
                    <div class="content">
                        <div class="fkcg_roll">
                            <div class="fkcg_box fkcg_box_lt fkcg_box_lt_new">
                                <div class="circle">
                                    <div class="pie_left">
                                        <div class="left"></div>
                                    </div>
                                    <div class="pie_right">
                                        <div class="right"></div>
                                    </div>
                                    <div class="mask">
                                        <input type="hidden" value="<?php echo value(dj($data['body']['content']['person']['detections'][2]['conclusion'])); ?>">
                                        <span><?php echo dj($data['body']['content']['person']['detections'][2]['conclusion']); ?></span>
                                    </div>
                                </div>
                                <div>多头借款风险</div>
                                <?php $dtjkfx = $data['body']['content']['person']['detections'][2]['details'];?>
                            </div>
                            <div class="fkcg_box gt">
                                <div class="yuqi_box yuqi_box_lt yuqi_box_lt_new">
                                    <img class="yhq_img" src="/public/web/images/yhq_lt@3x.png" style="width: 0.2rem;">
                                    <div class="yhq_detail_lt">
                                        <div>借款机构数</div>
                                        <div><?php echo !empty($dtjkfx[0]['values'][0][0]) ? htmlentities($dtjkfx[0]['values'][0][0]) : 0; ?></div>
                                    </div>
                                </div>
                                <div class="yuqi_box yuqi_box_gt">
                                    <img class="yhq_img" src="/public/web/images/yhq_gt@3x.png">
                                    <div class="yhq_detail_gt">
                                        <div>借款次数</div>
                                        <div><?php echo !empty($dtjkfx[0]['values'][0][1]) ? htmlentities($dtjkfx[0]['values'][0][1]) : 0; ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="gray_box">
                            <div class="title">结论分析</div>
                            <div class="text"><?php echo htmlentities($data['body']['content']['person']['detections'][2]['analysis']); ?></div>
                        </div>
                        <div class="details_box">
                            <div class="title">
                                <span></span>
                                <span>借款详情</span>
                            </div>
                            <div class="mt-tabpage" js-tab="1">
                                <div class="mt-tabpage-title">
                                    <a href="javascript:;" data-id="1" class="mt-tabpage-item mt-tabpage-item-cur">被拒绝</a>
                                    <a href="javascript:;" data-id="2" class="mt-tabpage-item">取消申请</a>
                                    <a href="javascript:;" data-id="3" class="mt-tabpage-item">审核通过</a>
                                </div>
                                <div class="tabpage-count">
                                    <ul class="mt-wrap">
                                        <li class="tabpage-item item_a">
                                            <?php foreach($dtjkfx[1]['values'] as $v): if(($v[4]=='拒绝')): ?>
                                            <div class="yuqi_detail_roll">
                                                <div class="yuqi_detail_lt">
                                                    <img src="/public/web/images/icon_bank@3x.png">
                                                </div>
                                                <div class="yuqi_detail_gt">
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">类型</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[2]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">时间</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[0]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">期数</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[3]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">金额</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[1]); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </li>


                                        <li class="tabpage-item item_b">
                                            <?php foreach($dtjkfx[1]['values'] as $v): if(($v[4]=='取消申请')): ?>
                                            <div class="yuqi_detail_roll">
                                                <div class="yuqi_detail_lt">
                                                    <img src="/public/web/images/icon_bank@3x.png">
                                                </div>
                                                <div class="yuqi_detail_gt">
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">类型</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[2]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">时间</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[0]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">期数</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[3]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">金额</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[1]); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php endforeach; ?>

                                        </li>
                                        <li class="tabpage-item item_c" style="overflow: auto">
                                            <?php foreach($dtjkfx[1]['values'] as $v): if(($v[4]!='拒绝'&&$v[4]!='取消申请')): ?>
                                            <div class="yuqi_detail_roll">
                                                <div class="yuqi_detail_lt">
                                                    <img src="/public/web/images/icon_bank@3x.png">
                                                </div>
                                                <div class="yuqi_detail_gt">
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">类型</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[2]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">时间</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[0]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">期数</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[3]); ?></div>
                                                    </div>
                                                    <div class="yuqi_detail_item">
                                                        <div class="yuqi_detail_item_lt">金额</div>
                                                        <div class="yuqi_detail_item_gt"><?php echo htmlentities($v[1]); ?></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <?php endforeach; ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--身份基本信息-->
                <div class="roll" style="margin-bottom: 0">
                    <div class="title">
                        <span></span>
                        <span>身份基本信息</span>
                    </div>
                    <div class="content">
                        <div class="fkcg_roll">
                            <div class="fkcg_box fkcg_box_lt fkcg_box_lt_new">
                                <div class="circle">
                                    <div class="pie_left">
                                        <div class="left"></div>
                                    </div>
                                    <div class="pie_right">
                                        <div class="right"></div>
                                    </div>
                                    <div class="mask">
                                        <input type="hidden" value="<?php echo value(dj($data['body']['content']['person']['detections'][0]['conclusion'])); ?>">
                                        <span><?php echo dj($data['body']['content']['person']['detections'][0]['conclusion']); ?></span>
                                    </div>
                                </div>
                                <div>身份风险</div>
                            </div>
                            <div class="fkcg_box sfxx_box_lt gray_box">
                                <div class="title">结论分析</div>
                                <div class="text"><?php echo htmlentities($data['body']['content']['person']['detections'][0]['analysis']); ?></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div id="phone_risk" style="height: 0.2rem"></div>
            <div class="content_risk">
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>手机基本信息</span>
                    </div>
                    <ul class="content">
                        <li>
                            <div class="lt">运营商</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['phone']['info']['isp']); ?></div>
                        </li>
                        <li>
                            <div class="lt">卡描述</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['phone']['info']['description']); ?></div>
                        </li>
                        <li>
                            <div class="lt">所属省份</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['phone']['info']['province']); ?></div>
                        </li>
                        <li>
                            <div class="lt">所属城市</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['phone']['info']['city']); ?></div>
                        </li>
                        <li>
                            <div class="lt">在网状态</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['phone']['info']['status']); ?></div>
                        </li>
                        <li>
                            <div class="lt">在网时长</div>
                            <div class="gt"><?php echo htmlentities($data['body']['content']['phone']['info']['time']); ?></div>
                        </li>
                    </ul>
                </div>
                <div class="roll">
                    <div class="title">
                        <span></span>
                        <span>手机风险综述</span>
                    </div>
                    <div class="content analysis-box-list">
                        <div class="rerult_item">
                            <div class="iconfont iconyiwen text_gray"></div>
                            <div class="iconfont iconbank text_green"></div>
                            <div class="item_bottom text_gray">非银多头</div>
                            <div class="item_middle <?php echo dengji($data['body']['content']['phone']['detections'][0]['conclusion']); ?>"><?php echo htmlentities($data['body']['content']['phone']['detections'][0]['conclusion']); ?></div>
                            <input type="hidden" value="<?php echo htmlentities($data['body']['content']['phone']['detections'][0]['analysis']); ?>">
                        </div>
                        <div class="rerult_item">
                            <div class="iconfont iconyiwen text_gray"></div>
                            <div class="iconfont iconman <?php echo dengji($data['body']['content']['phone']['detections'][1]['conclusion']); ?>"></div>
                            <div class="item_bottom text_gray">银行多头</div>
                            <div class="item_middle <?php echo dengji($data['body']['content']['person']['detections'][1]['conclusion']); ?>"><?php echo htmlentities($data['body']['content']['phone']['detections'][1]['conclusion']); ?></div>
                            <input type="hidden" value="<?php echo htmlentities($data['body']['content']['phone']['detections'][1]['analysis']); ?>">
                        </div>

                        <div class="rerult_item" style="background: rgba(255,255,255,1);"></div>
                    </div>
                </div>
                <!--非银行多头--开始-->
                <div class="roll" style="margin-bottom: 0">
                    <div class="title">
                        <span></span>
                        <span>非银多头</span>
                        <?php $fyhdt = $data['body']['content']['phone']['detections'][0]['details'];?>
                    </div>
                    <div class="content">
                        <div class="fkcg_roll">
                            <div class="fkcg_box fkcg_box_lt fkcg_box_lt_new">
                                <div class="circle">
                                    <div class="pie_left">
                                        <div class="left"></div>
                                    </div>
                                    <div class="pie_right">
                                        <div class="right"></div>
                                    </div>
                                    <div class="mask">
                                        <input type="hidden" value="<?php echo value(dj($data['body']['content']['phone']['detections'][0]['conclusion'])); ?>">
                                        <span><?php echo dj($data['body']['content']['phone']['detections'][0]['conclusion']); ?></span>
                                    </div>
                                </div>
                                <div>非银多头借款风险</div>
                            </div>
                            <div class="fkcg_box sfxx_box_lt gray_box">
                                <div class="title">结论分析</div>
                                <div class="text"><?php echo htmlentities($data['body']['content']['phone']['detections'][0]['analysis']); ?></div>
                            </div>
                        </div>
                        <div class="details_box">
                            <div class="title">
                                <span></span>
                                <span>非银机构借款申请次数与申请平台数</span>
                            </div>
                            <div class="mt-tabpage" js-tab="3">
                                <div class="mt-tabpage-title line_title">
                                    <span data-id="1" class="mt-tabpage-item mt-tabpage-item-cur">近6月</span>
                                    <span data-id="2" class="mt-tabpage-item">近12月</span>
                                </div>
                                <div class="echarts_name">
                                    <p>
                                        <span class="cishu"></span>
                                        <span>申请次数</span>
                                    </p>
                                    <p>
                                        <span class="shuliang"></span>
                                        <span>平台数量</span>
                                    </p>
                                </div>
                                <div class="mt-tabpage-count" style="height: 2.5rem">
                                    <ul class="mt-tabpage-cont__wrap">
                                        <li class="mt-tabpage-item line_tab">
                                            <div id="echarts_line_a"></div>
                                        </li>
                                        <li class="mt-tabpage-item line_tab">
                                            <div id="echarts_line_b"></div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--非银行多头--结束-->
                <!--银行多头--开始-->
                <?php
                    $yhdt = $data['body']['content']['phone']['detections'][1]['details'];

                ?>
                <div class="roll" style="margin-top: 0">
                    <div class="title">
                        <span></span>
                        <span>银行多头</span>
                    </div>
                    <div class="content">
                        <div class="fkcg_roll">
                            <div class="fkcg_box fkcg_box_lt fkcg_box_lt_new">
                                <div class="circle">
                                    <div class="pie_left">
                                        <div class="left"></div>
                                    </div>
                                    <div class="pie_right">
                                        <div class="right"></div>
                                    </div>
                                    <div class="mask">
                                        <input type="hidden" value="<?php echo value(dj($data['body']['content']['phone']['detections'][1]['conclusion'])); ?>">
                                        <span><?php echo dj($data['body']['content']['phone']['detections'][1]['conclusion']); ?></span>
                                    </div>
                                </div>
                                <div>银行多头借款风险</div>
                            </div>
                            <div class="fkcg_box sfxx_box_lt gray_box">
                                <div class="title">结论分析</div>
                                <div class="text"><?php echo htmlentities($data['body']['content']['phone']['detections'][1]['analysis']); ?></div>
                            </div>
                        </div>
                        <div class="details_box">
                            <div class="title">
                                <span></span>
                                <span>银机构借款申请次数与申请平台数</span>
                            </div>
                            <div class="mt-tabpage" js-tab="2">
                                <div class="mt-tabpage-title pie_title">
                                    <span data-id="1" class="mt-tabpage-item mt-tabpage-item-cur">近6月</span>
                                    <span data-id="2" class="mt-tabpage-item">近12月</span>
                                </div>
                                <div class="echarts_name">
                                    <p>
                                        <span class="cishu"></span>
                                        <span>申请次数</span>
                                    </p>
                                    <p>
                                        <span class="shuliang"></span>
                                        <span>平台数量</span>
                                    </p>
                                </div>
                                <div class="mt-tabpage-count">
                                    <ul class="mt-tabpage-cont__wrap">
                                        <li class="mt-tabpage-item pie_tab">
                                            <div>
                                                <div class="lt">
                                                    <div id="echarts_pie_a"></div>
                                                </div>
                                                <div class="gt">
                                                    <div>
                                                        <span></span>
                                                        <span>传统银行</span>
                                                        <span><?php echo !empty($yhdt[0]['values'][0][5]) ? htmlentities($yhdt[0]['values'][0][5]) : 0; ?></span>
                                                    </div>
                                                    <div>
                                                        <span style="border:2px solid rgba(231,242,234,1);"></span>
                                                        <span>网络银行</span>
                                                        <span><?php echo !empty($yhdt[0]['values'][1][5]) ? htmlentities($yhdt[0]['values'][1][5]) : 0; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="lt">
                                                    <div id="echarts_pie_b"></div>
                                                </div>
                                                <div class="gt">
                                                    <div>
                                                        <span style="border:2px solid rgba(247,211,29,1);"></span>
                                                        <span>传统银行</span>
                                                        <span><?php echo !empty($yhdt[0]['values'][0][6]) ? htmlentities($yhdt[0]['values'][0][6]) : 0; ?></span>
                                                    </div>
                                                    <div>
                                                        <span style="border:2px solid rgba(244,242,236,1);"></span>
                                                        <span>网络银行</span>
                                                        <span><?php echo !empty($yhdt[0]['values'][1][6]) ? htmlentities($yhdt[0]['values'][1][6]) : 0; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="mt-tabpage-item pie_tab">
                                            <div>
                                                <div class="lt">
                                                    <div id="echarts_pie_c"></div>
                                                </div>
                                                <div class="gt">
                                                    <div>
                                                        <span></span>
                                                        <span>传统银行</span>
                                                        <span><?php echo !empty($yhdt[1]['values'][0][5]) ? htmlentities($yhdt[1]['values'][0][5]) : 0; ?></span>
                                                    </div>
                                                    <div>
                                                        <span style="border:2px solid rgba(231,242,234,1);"></span>
                                                        <span>网络银行</span>
                                                        <span><?php echo !empty($yhdt[1]['values'][1][5]) ? htmlentities($yhdt[1]['values'][1][5]) : 0; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="lt">
                                                    <div id="echarts_pie_d"></div>
                                                </div>
                                                <div class="gt">
                                                    <div>
                                                        <span style="border:2px solid rgba(247,211,29,1);"></span>
                                                        <span>传统银行</span>
                                                        <span><?php echo !empty($yhdt[1]['values'][0][6]) ? htmlentities($yhdt[1]['values'][0][6]) : 0; ?></span>
                                                    </div>
                                                    <div>
                                                        <span style="border:2px solid rgba(244,242,236,1);"></span>
                                                        <span>网络银行</span>
                                                        <span><?php echo !empty($yhdt[1]['values'][1][6]) ? htmlentities($yhdt[1]['values'][1][6]) : 0; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--银行多头--结束-->
            </div>
        </div>
        <div class="tips-box">
            <div class="myTitle">
                <img src="/public/web/project/image/title%20bg.png" class="titlebg" alt="">
                <h4>优化建议</h4>
            </div>
            <p>1.您的信用很差！请及时清偿负债，优化信用记录，以免造成大数据不通过！
                2.申请贷款不能急，需要对症下药。如果自己有资产或者公积金，可以优先考虑相关贷款类型，比如：公积金贷款，抵押贷款。
                这样的下款率是非常高的。当然前提是您的基础大数据和银行的征信都没问题。
                3.如果有严重的不良行为记录，则需要及时处理掉，并且一年内不浏览、不注册、不申请、不查询，大数据会有所好转。
                4.连续被2个平台拒绝后，建议至少等3个月以后再进行相关的申请。
                5.连续2次申请都不通过，则不要再继续申请，以免造成恶性循环！因为每次申请大数据都会记录一次，大数据记录越多越不容易通过！</p>
            <div class="ra_myFx_rBgImg">
                <img class="ra_myFxr_img1" src="/public/web/project/image/Fill 55@2x (1).png" alt="">
                <img class="ra_myFxr_img2" src="/public/web/project/image/Leaf Me Alone!-2@2x.png" alt="">
                <img class="ra_myFxr_img3" src="/public/web/project/image/Leaf Me Alone!@2x.png" alt="">
                <img class="ra_myFxr_img4" src="/public/web/project/image/Leaf Bottom@2x.png" alt="">
                <img class="ra_myFxr_img5" src="/public/web/project/image/yhjyman@2x.png" style="width: 100px;" alt="">
            </div>
            <img src="/public/web/project/image/yinhao.png" style="margin: 2%; width: 14%;">
        </div>
        <div class="wechat">
            <div class="wechat-title">
                关注<span>我们</span>
            </div>
            <img class="QRimg" src="<?php echo htmlentities($system['qr_code']); ?>" alt="">
            <div class="followme">
                <img src="/public/web/project/image/followme.png" style="width: 100%;">
                <div class="followmeText">长按关注公众号</div>
            </div>

        </div>
        <div class="bottom">
            <p class="bottom-top">
                <img class="bottom-topimg" src="/public/web/project/image/icons-report.png" alt="">为保障你的信息安全，请确保本人查询</p>
            <p class="bottom-bot">
                <img class="bottom-topimg" src="/public/web/project/image/dun.png" alt="">数据安全声明</p>
            <div class="btext">
                <p>您的个人信息仅用作信息报告查询</p>
                <p>采用MD5+RSA加密算法，银行级数据保护</p>
                <p>我们正在保护您的数据安全</p>
            </div>
        </div>
    </div>
</div>
<!--风险描述弹框-->
<div id="cover"></div>
<div id="modal">
    <div class="desc_title">风险描述</div>
    <div class="desc_content">
        <div class="title">策略描述</div>
        <div class="desc_text"></div>
        <div class="title">结论分析</div>
        <div class="desc_result"></div>
    </div>
</div>
<!--案件结果详情弹框-->
<div id="cover_res"></div>
<div id="modal_res"></div>
<!--警告弹框-->
<div id="cover1" style="display: block"></div>
<div id="modal1" class="cover_jg" style="display: block">
    <div>警告！</div>
    <div>此页面关闭后，报告将会自动删除。 如有需要，请自行截图保存报告。</div>
    <div class="comfirm">好的</div>
</div>

</body>
<script>
    $(function () {
        $(".comfirm").click(function () {
            $("#cover1").hide();
            $("#modal1").hide();
        })
        // tab切换
        $(".line_tab").eq(0).show();
        $(".line_title span").click(function () {
            var num = $(".line_title span").index(this);
            $(".line_tab").hide();
            $(".line_tab").eq(num).show();
            $(this).addClass("mt-tabpage-item-cur").siblings().removeClass("mt-tabpage-item-cur")
        });

        $(".pie_tab").eq(0).show();
        $(".pie_title span").click(function () {
            var num = $(".pie_title span").index(this);
            $(".pie_tab").hide();
            $(".pie_tab").eq(num).show();
            $(this).addClass("mt-tabpage-item-cur").siblings().removeClass("mt-tabpage-item-cur")
        });
        // 点击tab切换以及导航吸顶效果
        $(".tab_title a").click(function (e) {
            $(this).addClass("tab_active").siblings().removeClass("tab_active");
        });
        $(".fixed_tab a").click(function (e) {
            $(this).addClass("tab_active").siblings().removeClass("tab_active");
            $(this).find(".num").addClass("num_active").parents(".tab_item").siblings().find(".num").removeClass("num_active");
        });
        $(".fixed_tab").hide();
        function nav() {
            let height = 0;
            height = $(".topHeight").height();
            $(window).scroll(function () {
                let w = $("body").scrollTop() || $(document).scrollTop(); //获取滚动值
                if (w > height) {
                    $(".fixed_tab").show();
                } else if (w <= 0) {
                    $(".fixed_tab").hide();
                } else {
                    $(".fixed_tab").hide();
                }
            });
        }
        nav();
        $(document).scroll(function () {
            //获取滚动条到顶部的距离
            var t=document.documentElement.scrollTop || document.body.scrollTop;
            if(t>3655){
                $(".phone_risk").addClass("tab_active").siblings().removeClass("tab_active");
                $(".tab2").addClass("tab_active").siblings().removeClass("tab_active");
                $(".tab2").find(".num").addClass("num_active").parents(".tab_item").siblings().find(".num").removeClass("num_active");
            }else{
                $(".identity_risk").addClass("tab_active").siblings().removeClass("tab_active");
                $(".tab1").addClass("tab_active").siblings().removeClass("tab_active");
                $(".tab1").find(".num").addClass("num_active").parents(".tab_item").siblings().find(".num").removeClass("num_active");
            }
        });

        // 身份风险中的切换
        $(".item_b").hide();
        $(".item_c").hide();
        $(".mt-tabpage-title a").click(function () {
            if($(this).attr("data-id")==1){
                $(".item_a").show().siblings().hide();
            }
            if($(this).attr("data-id")==2){
                $(".item_b").show().siblings().hide();
            }
            if($(this).attr("data-id")==3){
                $(".item_c").show().siblings().hide();
            }
        });
        //非银行多头6个月
        var data1 = [
            "<?php echo htmlentities($fyhdt[0]['values'][0][5]+$fyhdt[0]['values'][2][5]); ?>",
            "<?php echo htmlentities($fyhdt[0]['values'][1][5]+$fyhdt[0]['values'][3][5]); ?>",
            "<?php echo htmlentities($fyhdt[0]['values'][4][5]); ?>",
            "<?php echo htmlentities($fyhdt[0]['values'][5][5]); ?>",
        ];
        //非银行多头6个月
        var data2 = [
            "<?php echo htmlentities($fyhdt[1]['values'][0][5]+$fyhdt[0]['values'][2][5]); ?>",
            "<?php echo htmlentities($fyhdt[1]['values'][1][5]+$fyhdt[0]['values'][3][5]); ?>",
            "<?php echo htmlentities($fyhdt[1]['values'][4][5]); ?>",
            "<?php echo htmlentities($fyhdt[1]['values'][5][5]); ?>",
        ];


        // 手机风险中的切换
        let option_line_a = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['小贷', '消费金融', '融资租赁', '其他']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: ['小贷', '消费金融', '融资租赁', '其他']
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '申请次数',
                    type: 'line',
                    stack: '总量',
                    smooth: true,
                    data: data1,
                    itemStyle:{
                        normal:{
                            color:'#28CE58', //折点颜色
                            lineStyle:{
                                color:'#28CE58' //折线颜色
                            }
                        }
                    }
                },
                {
                    name: '平台数量',
                    type: 'line',
                    stack: '总量',
                    smooth: true,
                    data: data2,
                    itemStyle:{
                        normal:{
                            color:'#F7D31D', //折点颜色
                            lineStyle:{
                                color:'#F7D31D' //折线颜色
                            }
                        }
                    }
                }
            ]
        };
        let myChart_line_a = echarts.init(document.getElementById('echarts_line_a'));
        myChart_line_a.setOption(option_line_a);

        //非银行多头6个月
        var data3 = [
            "<?php echo htmlentities($fyhdt[0]['values'][0][6]+$fyhdt[0]['values'][2][6]); ?>",
            "<?php echo htmlentities($fyhdt[0]['values'][1][6]+$fyhdt[0]['values'][3][6]); ?>",
            "<?php echo htmlentities($fyhdt[0]['values'][4][6]); ?>",
            "<?php echo htmlentities($fyhdt[0]['values'][5][6]); ?>",
        ];
        //非银行多头6个月
        var data4 = [
            "<?php echo htmlentities($fyhdt[1]['values'][0][6]+$fyhdt[0]['values'][2][6]); ?>",
            "<?php echo htmlentities($fyhdt[1]['values'][1][6]+$fyhdt[0]['values'][3][6]); ?>",
            "<?php echo htmlentities($fyhdt[1]['values'][4][6]); ?>",
            "<?php echo htmlentities($fyhdt[1]['values'][5][6]); ?>",
        ];
        let option_line_b = {
            tooltip: {
                trigger: 'axis'
            },
            legend: {
                data: ['小贷', '消费金融', '融资租赁', '其他']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            toolbox: {
                feature: {
                    saveAsImage: {}
                }
            },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: ['小贷', '消费金融', '融资租赁', '其他']
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: '申请次数',
                    type: 'line',
                    stack: '总量',
                    smooth: true,
                    data: data3,
                    itemStyle:{
                        normal:{
                            color:'#28CE58', //折点颜色
                            lineStyle:{
                                color:'#28CE58' //折线颜色
                            }
                        }
                    }
                },
                {
                    name: '平台数量',
                    type: 'line',
                    stack: '总量',
                    smooth: true,
                    data: data4,
                    itemStyle:{
                        normal:{
                            color:'#F7D31D', //折点颜色
                            lineStyle:{
                                color:'#F7D31D' //折线颜色
                            }
                        }
                    }
                }

            ]
        };
        let myChart_line_b = echarts.init(document.getElementById('echarts_line_b'));
        myChart_line_b.setOption(option_line_b);

        setTimeout(function() {
            myChart_line_a.dispatchAction({
                type: 'showTip',
                seriesIndex: 0, // 显示第几个series
                dataIndex: 1 // 显示第几个数据
            });
            myChart_line_b.dispatchAction({
                type: 'showTip',
                seriesIndex: 0, // 显示第几个series
                dataIndex: 1 // 显示第几个数据
            });
        },1000);

        let option_pie_a = {
            color: ['#28CE58', '#E7F2EA'],
            stillShowZeroSum: false,
            tooltip: {
                trigger: 'item',
                position: ['10%', '70%'],
                formatter: '{a} <br/>{b} : {c} ({d}%)'
            },
            series: [
                {
                    type: 'pie',
                    radius: '80%',
                    name:"申请次数",
                    data: [
                        {value: "<?php echo !empty($yhdt[0]['values'][0][5]) ? htmlentities($yhdt[0]['values'][0][5]) : 0; ?>", name: '传统银行'},
                        {value: "<?php echo !empty($yhdt[0]['values'][1][5]) ? htmlentities($yhdt[0]['values'][1][5]) : 0; ?>", name: '网络银行'},
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(128, 128, 128, 0.5)'
                        }
                    },
                    label: {
                        normal: {
                            position: 'inner',
                            show : false
                        }
                    }
                }
            ]
        };
        let myChart_pie_a= echarts.init(document.getElementById('echarts_pie_a'));
        myChart_pie_a.setOption(option_pie_a);

        let option_pie_b = {
            color: ['#F7D31D', '#F4F2EC'],
            stillShowZeroSum: false,
            tooltip: {
                trigger: 'item',
                position: ['10%', '70%'],
                formatter: '{a} <br/>{b} : {c} ({d}%)'
            },
            series: [
                {
                    type: 'pie',
                    radius: '80%',
                    name:"申请平台数",
                    data: [
                        {value: "<?php echo !empty($yhdt[0]['values'][0][6]) ? htmlentities($yhdt[0]['values'][0][6]) : 0; ?>", name: '传统银行'},
                        {value: "<?php echo !empty($yhdt[0]['values'][1][6]) ? htmlentities($yhdt[0]['values'][1][6]) : 0; ?>", name: '网络银行'},
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(128, 128, 128, 0.5)'
                        }
                    },
                    label: {
                        normal: {
                            position: 'inner',
                            show : false
                        }
                    }
                }
            ]
        };
        let myChart_pie_b= echarts.init(document.getElementById('echarts_pie_b'));
        myChart_pie_b.setOption(option_pie_b);


        let option_pie_c = {
            color: ['#28CE58', '#E7F2EA'],
            stillShowZeroSum: false,
            tooltip: {
                trigger: 'item',
                position: ['10%', '70%'],
                formatter: '{a} <br/>{b} : {c} ({d}%)'
            },
            series: [
                {
                    type: 'pie',
                    radius: '80%',
                    name:"申请次数",
                    data: [
                        {value: "<?php echo !empty($yhdt[1]['values'][0][5]) ? htmlentities($yhdt[1]['values'][0][5]) : 0; ?>", name: '传统银行'},
                        {value: "<?php echo !empty($yhdt[1]['values'][1][5]) ? htmlentities($yhdt[1]['values'][1][5]) : 0; ?>", name: '网络银行'},
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(128, 128, 128, 0.5)'
                        }
                    },
                    label: {
                        normal: {
                            position: 'inner',
                            show : false
                        }
                    }
                }
            ]
        };
        let myChart_pie_c= echarts.init(document.getElementById('echarts_pie_c'));
        myChart_pie_c.setOption(option_pie_c);
        let option_pie_d = {
            color: ['#F7D31D', '#F4F2EC'],
            stillShowZeroSum: false,
            tooltip: {
                trigger: 'item',
                position: ['10%', '70%'],
                formatter: '{a} <br/>{b} : {c} ({d}%)'
            },
            series: [
                {
                    type: 'pie',
                    radius: '80%',
                    name:"申请平台数",
                    data: [
                        {value: "<?php echo !empty($yhdt[1]['values'][0][6]) ? htmlentities($yhdt[1]['values'][0][6]) : 0; ?>", name: '传统银行'},
                        {value: "<?php echo !empty($yhdt[1]['values'][1][6]) ? htmlentities($yhdt[1]['values'][1][6]) : 0; ?>", name: '网络银行'},
                    ],
                    itemStyle: {
                        emphasis: {
                            shadowBlur: 10,
                            shadowOffsetX: 0,
                            shadowColor: 'rgba(128, 128, 128, 0.5)'
                        }
                    },
                    label: {
                        normal: {
                            position: 'inner',
                            show : false
                        }
                    }
                }
            ]
        };
        let myChart_pie_d= echarts.init(document.getElementById('echarts_pie_d'));
        myChart_pie_d.setOption(option_pie_d);

        // 顶部分数悬浮框
        getxfk();
        function getxfk() {
            //水波颜色切换
            var waveHeight = 10;
            var waveNum = 60; //分数
            var newNum = 0;
            var ele = $("#denfenjs span").text();
            switch (ele) {
                case 'A':waveNum=0;break;
                case 'B':waveNum=10;break;
                case 'C':waveNum=30;break;
                case 'D':waveNum=60;break;
                case 'E':waveNum=99;break;
            }
            setInterval(function () {
                newNum++;
            }, 20);
            function waves() {
                if (waveHeight < waveNum) {
                    $(".wave").css("height", waveHeight + "%");
                    $(".waveXf").css("height", waveHeight + "%");
                    waveHeight++;
                }
                setTimeout(function () {
                    waves()
                }, 15);
            }
            waves();


            if(ele =="A"||ele =="B") {
                $(".waven").addClass("green");
            } else {
                $(".waven").removeClass("green");
            }
            if(ele =="C") {
                $(".waven").addClass("yellow");
            } else {
                $(".waven").removeClass("yellow");
            }
            if(ele =="D"||ele =="E") {
                $(".waven").addClass("red");
            } else {
                $(".waven").removeClass("red");
            }
        }
        // 圆环
        $('.fkcg_roll .circle').each(function(index, el) {
            let num = $(this).find('input').val() * 3.6;
            if (num<=180) {
                $(this).find('.right').css('transform', "rotate(" + num + "deg)");
            } else {
                $(this).find('.right').css('transform', "rotate(180deg)");
                $(this).find('.left').css('transform', "rotate(" + (num - 180) + "deg)");
            };
        });
        //身份和手机风险描述
        let eleNum=$(".rerult_item").length;
        let text1="根据被查询人的相关身份信息，在人群风险分布模型里检测风险情况";
        let text2="根据被查询人的身份信息，在司法体系中检测是否存在被曝光、失信行为、被执行以及相关";
        let text3="根据被查询人的身份信息，在资金端的借款数据中进行综合分析，得出借款风险情况";
        let text4="根据被查询人的身份信息，在资金端的逾期数据中进行综合分析，得出逾期风险情况";
        let text5="根据被查询人的相关信息，分析其在非银机构场景下，多头借款申请数据中的潜在风险情况";
        let text6="根据被查询人的相关信息，分析其在银行机构场景下，多头借款申请数据中的潜在风险情况";
        for(let i=0;i<eleNum;i++){
            $(".rerult_item").eq(i).click(function (e) {
                let content=eval('text'+(i+1));
                let title=$(this).find(".item_bottom").text();
                let result=$(this).find("input").val()
                $(".desc_result").text(result)
                $(".desc_title").text(title);
                $("#modal .desc_content .desc_text").html(content);
                $("#cover").show();
                $("#modal").show();
                e.stopPropagation();
            })
        }
        $("#cover").click(function(event){
            var _con = $('#modal');  // 设置目标区域
            if(!_con.is(event.target) && _con.has(event.target).length === 0){
                $("#cover").hide();
                $("#modal").hide();
            }
        });
        // 案件结果详情弹框
        $(".anjian_res").click(function () {
            $("#cover_res").show();
            $("#modal_res").show();
            let anjian_res=$(this).find("span").text();
            $("#modal_res").text(anjian_res);
        })
        $("#cover_res").click(function(event){
            var _con = $('#modal_res');  // 设置目标区域
            if(!_con.is(event.target) && _con.has(event.target).length === 0){
                $("#cover_res").hide();
                $("#modal_res").hide();
            }
        });
        // 优化建议
        let score=$("#denfenjs span").text();
        let text_a="恭喜您！您的信用非常好，请继续保持！";
        let text_b="您的信用还不错哦，请继续保持，记得保护好个人身份信息，以免泄露被他人利用！";
        let text_c="注册平台过多，会增大风险，控制好自己的手，切记不要频繁注册！";
        let text_d="申请贷款务必填写真实信息，切勿夸大造假，避免频繁申请，同时保护好个人身份信息，谨防泄露！";
        let text_e="您的信用很差！请及时清偿负债，优化信用记录，以免造成大数据不通过";
        if(score=="A"){
            $(".tips-box p").text(text_a)
        }else if(score=="B"){
            $(".tips-box p").text(text_b)
        }else if(score=="C"){
            $(".tips-box p").text(text_c)
        }else if(score=="D"){
            $(".tips-box p").text(text_d)
        }else if(score=="E"){
            $(".tips-box p").text(text_e)
        }
    });
</script>
</html>

