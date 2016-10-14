<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

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
                <h3>批发统计</h3>
                <h5>显示当前终端店一段时间内的历史订单金额和数量。</h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>1、统计图展示了历史订单的订单金额和订单数量在时间段内的走势情况及与前一个时间段的趋势对比</li>
            <li>2、统计表显示了符合搜索条件的历史订单列表并可以点击“导出Excel”将订单记录导出为Excel文件</li>
            <li>总下单金额：<strong><?php echo $output['statcount_arr']['orderamount'];?></strong>元&nbsp;&nbsp;&nbsp;&nbsp;总订单数量：<strong><?php echo $output['statcount_arr']['ordernum'];?></strong> </li>
        </ul>
    </div>

    <form  method="get" action="index.php" target="_self">
        <table class="search-form">
            <input type="hidden" name="act" value="purchase_analyse" />
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

    <!-- 下单量 -->
    <div id="stat_tabs" class="ui-tabs" style="min-height:500px;padding-top:10px;">
        <div class="tabmenu">
            <ul class="tab pngFix">
                <li><a href="#orderamount_div" nc_type="showdata" data-param='{"type":"orderamount"}'>订单金额</a></li>
                <li><a href="#ordernum_div" nc_type="showdata" data-param='{"type":"ordernum"}'>订单数量</a></li>
            </ul>
        </div>
        <!-- 下单金额 -->
        <div id="orderamount_div" style="width:930px;"></div>
        <!-- 下单量 -->
        <div id="ordernum_div" style="width:930px;"></div>
    </div>
    <div id="statlist" style="width:930px;"></div>

</div>

<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<!--<script type="text/javascript" src="--><?php //echo SHOP_RESOURCE_SITE_URL; ?><!--/js/ui.core.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo SHOP_RESOURCE_SITE_URL; ?><!--/js/ui.tabs.js"></script>-->

<script type="text/javascript">
    //切换登录卡
    $('#stat_tabs').tabs();

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
            $.getJSON('index.php?act=purchase_analyse&op=getweekofmonth',{y:year,m:month},function(data){
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

        $('#ordernum_div').highcharts(<?php echo $output['stat_json']['ordernum'];?>);
        $('#orderamount_div').highcharts(<?php echo $output['stat_json']['orderamount'];?>);
        //$('#statlist').load('index.php?act=sale_analyse&op=salelist&t=<?php echo $output['searchtime'];?>&order_type=<?php echo $_REQUEST['order_type'];?>');
    });
</script>