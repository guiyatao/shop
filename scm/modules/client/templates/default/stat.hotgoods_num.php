<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css"/>
<style>
    .fr select{
        color: #777;
        background-color: #FFF;
        height: 30px;
        vertical-align: middle;
        padding: 0 4px;
        border: solid 1px #E6E9EE;
    }
    .fr input{
        height: 28px;
        vertical-align: middle;
        padding: 0 4px;
        border: solid 1px #E6E9EE;
    }

</style>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>热门商品</h3>
                <h5>显示当前终端店的下单商品总数排名前30位的商品曲线图。</h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>1、符合以下任何一种条件的订单即为有效订单：1）采用在线支付方式支付并且已付款；2）采用货到付款方式支付并且交易已完成</li>
            <li>2、图表展示了符合搜索条件的有效订单中的下单商品总数排名前30位的商品</li>
        </ul>
    </div>

    <form  method="get" action="index.php" target="_self">
        <table class="search-form">
            <input type="hidden" name="act" value="goods_analyse" />
            <input type="hidden" name="op" value="index" />
        <tr>
            <td class="tr">
                <div class="fr">
                    <label class="submit-border"><input type="submit" class="submit" value="搜索" /></label>
                </div>
                <div class="fr">
                    <div class="fl" style="margin-right:3px;">
                        <select name="search_type" id="search_type" class="s-select">
                            <option value="day" <?php echo $output['search_arr']['search_type']=='day'?'selected':''; ?>>按照天统计</option>
                            <option value="week" <?php echo $output['search_arr']['search_type']=='week'?'selected':''; ?>>按照周统计</option>
                            <option value="month" <?php echo $output['search_arr']['search_type']=='month'?'selected':''; ?>>按照月统计</option>
                        </select>
                    </div>
                    <div id="searchtype_day" class="fl" >
                        <input type="text" value="<?php echo @date('Y-m-d',$output['search_arr']['day']['search_time']);?>" name="search_time" id="search_time" class="s-input-txt">
                    </div>
                    <div id="searchtype_week" class="fl" >
                        <select name="searchweek_year" >
                            <?php foreach ($output['year_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <select name="searchweek_month" >
                            <?php foreach ($output['month_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['week']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <select name="searchweek_week" >
                            <?php foreach ($output['week_arr'] as $k=>$v){?>
                                <option value="<?php echo $v['key'];?>" <?php echo $output['search_arr']['week']['current_week'] == $v['key']?'selected':'';?>><?php echo $v['val']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div id="searchtype_month" class="fl" >
                        <select name="searchmonth_year" >
                            <?php foreach ($output['year_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_year'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                        <select name="searchmonth_month" >
                            <?php foreach ($output['month_arr'] as $k=>$v){?>
                                <option value="<?php echo $k;?>" <?php echo $output['search_arr']['month']['current_month'] == $k?'selected':'';?>><?php echo $v; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </td>
            </tr>
        </table>
    </form>

    <!-- 下单商品数 -->
    <div id="goodsnum_div">
        <div id="container_goodsnum" style="width:930px;"></div>
        <div style="width:930px;">
            <table class="ncsc-default-table">
                <thead>
                <tr class="sortbar-array">
                    <th class="w90">序号</th>
                    <th>商品名称</th>
                    <th>下单商品数</th>
                </tr>
                </thead>
                <tbody>
                <?php if (!empty($output['statlist']['goodsnum']) && is_array($output['statlist']['goodsnum'])) { ?>
                    <?php foreach($output['statlist']['goodsnum'] as $k=>$v) { ?>
                        <tr class="bd-line">
                            <td><?php echo $k+1; ?></td>
                            <td class="tl"><span class="over_hidden w400 h20"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $v['goods_id']));?>" target="_blank"><?php echo $v['goods_name'];?></a></span></td>
                            <td><?php echo $v['goodsnum'];?></td>
                        </tr>
                    <?php }?>
                <?php } else { ?>
                    <tr>
                        <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
<script type="text/javascript">
    //展示搜索时间框
    function show_searchtime(){
        s_type = $("#search_type").val();
        $("[id^='searchtype_']").hide();
        $("#searchtype_"+s_type).show();
    }

    $(function(){

        //统计数据类型
        var s_type = $("#search_type").val();
        $('#search_time').datepicker({dateFormat: 'yy-mm-dd'});

        show_searchtime();
        $("#search_type").change(function(){
            show_searchtime();
        });

        //更新周数组
        $("[name='searchweek_month']").change(function(){
            var year = $("[name='searchweek_year']").val();
            var month = $("[name='searchweek_month']").val();
            $("[name='searchweek_week']").html('');
            $.getJSON('index.php?act=goods_analyse&op=getweekofmonth',{y:year,m:month},function(data){
                if(data != null){
                    for(var i = 0; i < data.length; i++) {
                        $("[name='searchweek_week']").append('<option value="'+data[i].key+'">'+data[i].val+'</option>');
                    }
                }
            });
        });


        // 高级搜索提交
        $('#ncsubmit').click(function(){
            //$("#flexigrid").flexOptions({url: 'index.php?act=activity&op=get_xml&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
        });


        $('#container_goodsnum').highcharts(<?php echo $output['stat_json']['goodsnum'];?>);

    });
</script>