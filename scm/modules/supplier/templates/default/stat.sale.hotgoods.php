<?php defined('InShopNC') or exit('Access Invalid!');?>

<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/seller_center.css" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />

<div class="page">
    <div class="alert mt10" style="clear:both;">
        <ul class="mt5">
            <li>1、图表展示了当前供应商不同地区合作终端店的个数和该地区的订单金额</li>
        </ul>
    </div>
    <div id="stat_tabs" class="ui-tabs" style="min-height:500px;padding-top:10px;">
        <div class="tabmenu">
            <ul class="tab pngFix">
                <li><a href="#orderamount_div" nc_type="showdata" data-param='{"type":"orderamount"}'>订单金额</a></li>
                <li><a href="#goodsnum_div" nc_type="showdata" data-param='{"type":"goodsnum"}'>合作商家个数</a></li>
            </ul>
        </div>
        <!-- 下单金额 -->
        <div id="orderamount_div">
            <div id="container_ordergamount" style="width:930px;"></div>
        </div>
        <!-- 下单商品数 -->
        <div id="goodsnum_div">
            <div id="container_goodsnum" style="width:930px;"></div>
        </div>
    </div>
</div>





<script charset="utf-8" type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js" ></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/highcharts/highcharts.js"></script>
<!--<script type="text/javascript" src="--><?php //echo SHOP_RESOURCE_SITE_URL; ?><!--/js/ui.core.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo SHOP_RESOURCE_SITE_URL; ?><!--/js/ui.tabs.js"></script>-->

<script type="text/javascript">
//展示搜索时间框
function show_searchtime(){
	s_type = $("#search_type").val();
	$("[id^='searchtype_']").hide();
	$("#searchtype_"+s_type).show();
}

$(function(){
	//切换登录卡
	$('#stat_tabs').tabs();
	
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
		$.getJSON('index.php?act=statistics_goods&op=getweekofmonth',{y:year,m:month},function(data){
	        if(data != null){
	        	for(var i = 0; i < data.length; i++) {
	        		$("[name='searchweek_week']").append('<option value="'+data[i].key+'">'+data[i].val+'</option>');
			    }
	        }
	    });
	});

	$('#container_goodsnum').highcharts(<?php echo $output['stat_json']['goodsnum'];?>);
	$('#container_ordergamount').highcharts(<?php echo $output['stat_json']['orderamount'];?>);
});
</script>