<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?act=client_stock&op=index" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['stockout_index_brand'];?></h3>
        <h5><?php echo $lang['stockout_subhead'];?></h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a href="JavaScript:void(0);" class="current"><?php echo '新增商品列表';?></a></li>
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>选中商品可以直接将新商品添加到批发订单中。</li>
    </ul>
  </div>

    <form method="post" name="newGoodsForm" id="newGoodsForm" action="index.php?act=client_stock&op=add_goods">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <table class="flex-table">
        <thead>
          <tr>
            <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
            <th width="60" align="center">商品条码</th>
            <th width="60" align="center">商品名称</th>
            <th width="60" align="center">原价</th>
            <th width="60" align="center">折扣</th>
            <th width="60" align="center">折扣价</th>
            <th width="60" align="center">单位</th>
            <th width="60" align="center">规格</th>
            <th width="60" align="center">供应商名称</th>  
            <th width="60" align="center">最小配量</th>
            <th width="60" align="center">订购数量</th>
            <th width="60" align="center">商城有无</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($output['goods_list']) && is_array($output['goods_list'])){ ?>
          <?php foreach($output['goods_list'] as $val){ ?>
          <tr data-id="<?php echo $val['goods_barcode']; ?>">
            <td class="sign"><i class="ico-check"></i></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_barcode'];?>"><?php echo $val['goods_barcode'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_nm'];?>"><?php echo $val['goods_nm'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_price'];?>"><?php echo $val['goods_price'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_discount'];?>"><?php echo $val['goods_discount'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_discount_price'];?>"><?php echo $val['goods_discount_price'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_unit'];?>"><?php echo $val['goods_unit'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_spec'];?>"><?php echo $val['goods_spec'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['supp_id'];?>"><?php echo $val['supp_id'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['min_set_num'];?>"><?php echo $val['min_set_num'];?></span></td>
            <td class="sort"><input type="number" min="0" name="<?php echo $val['goods_barcode'];?>" title="订购数量" class="editable " /></td>
            <td class="sort"><span column_id="<?php echo $val['goods_barcode'];?>" title="<?php echo $val['goods_online_exist'] == 0 ? '无' : '有';?>"><?php echo $val['goods_online_exist'] == 0 ? '无' : '有';?></span></td>
            <td></td>
          </tr>
          <?php } ?>
          <?php }else { ?>
          <tr>
            <td class="no-data" colspan="100"><i class="fa fa-exclamation-triangle"></i><?php echo $lang['nc_no_record'];?></td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>

</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>
<script type="text/javascript">
$(function(){
    $('.flex-table').flexigrid({
        height:'auto',// 高度自动
        usepager: false,// 不翻页
        striped:false,// 不使用斑马线
        resizable: false,// 不调节大小
        title: '新商品列表',// 表格标题
        reload: false,// 不使用刷新
        columnControl: false,// 不使用列控制
    });
    $("#submitBtn").click(function(){
      if($("#newGoodsForm").valid()){
        $("#newGoodsForm").submit();
      }
    });
});
</script>