<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['stockout_index_brand'];?></h3>
        <h5><?php echo $lang['stockout_subhead'];?></h5>
      </div>
      <ul class="tab-base nc-row">
        <li><a href="JavaScript:void(0);" class="current"><?php echo '缺货商品列表';?></a></li>
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>选中商品可以直接提交订单，也可以添加新商品。</li>
    </ul>
  </div>

    <form method="post" name="orderForm" id="orderForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <table class="flex-table">
        <thead>
          <tr>
            <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
            <th width="60" align="center" class="handle-s"><?php echo $lang['nc_handle'];?></th>
            <th width="60" align="center">商品条码</th>
            <th width="60" align="center">商品名称</th>
            <th width="60" align="center">原价</th>
            <th width="60" align="center">折扣</th>
            <th width="60" align="center">折扣价</th>
            <th width="60" align="center">单位</th>
            <th width="60" align="center">规格</th>
            <th width="60" align="center">库存</th>
            <th width="60" align="center">库存下限</th>
            <th width="60" align="center">供应商名称</th>
            <th width="60" align="center">最小配量</th>
            <th width="60" align="center">订购数量</th>
            <th width="60" align="center">订购金额</th>
            <th width="60" align="center">新商品</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
          <?php foreach($output['list'] as $val){ ?>
          <tr data-id="<?php echo $val['clie_id']; ?>">
            <td class="sign"><i class="ico-check"></i></td>
            <td class="handle-s"><a href="index.php?act=cms_navigation&op=cms_navigation_drop&navigation_id=<?php echo $val['navigation_id'];?>" class="btn red confirm-del"><i class="fa fa-trash-o"></i><?php echo $lang['nc_del'];?></a></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_barcode'];?>"><?php echo $val['goods_barcode'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_nm'];?>"><?php echo $val['goods_nm'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_price'];?>"><?php echo $val['goods_price'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_discount'];?>"><?php echo $val['goods_discount'];?></span></td>
            <td class="sort"><span id="price_<?php echo $val['goods_barcode'];?>"column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_discount_price'];?>"><?php echo $val['goods_discount_price'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_unit'];?>"><?php echo $val['goods_unit'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_spec'];?>"><?php echo $val['goods_spec'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_stock'];?>"><?php echo $val['goods_stock'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['goods_low_stock'];?>"><?php echo $val['goods_low_stock'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['supp_id'];?>"><?php echo $val['supp_id'];?></span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['min_set_num'];?>"><?php echo $val['min_set_num'];?></span></td>
            <td class="sort"><input id="num_<?php echo $val['goods_barcode'];?>" type="number" min="0" name="<?php echo $val['goods_barcode'];?>" title="订购数量" class="editable " /></td>
            <td class="sort"><span class="unit_price" id="unitprice_<?php echo $val['goods_barcode'];?>" column_id="<?php echo $val['clie_id'];?>" title="0.00">0.00</span></td>
            <td class="sort"><span column_id="<?php echo $val['clie_id'];?>" title="<?php echo $val['min_set_num'];?>"><?php echo isset($val['goods_stock']) ? '否' : '是';?></span></td>
            <!--td class="yes-onoff"><a href="JavaScript:void(0);" class=" <?php echo $val['navigation_open_type']? 'enabled':'disabled'?>" ajax_branch='navigation_open_type'  nc_type="inline_edit" fieldname="navigation_open_type" fieldid="<?php echo $val['navigation_id']?>" fieldvalue="<?php echo $val['navigation_open_type']?'1':'0'?>" title="<?php echo $lang['nc_editable'];?>"><img src="<?php echo ADMIN_TEMPLATES_URL;?>/images/transparent.gif"></a></td-->
      <!--input id="goods_barcode" name="goods_barcode" type="hidden" value="<?php echo $val['goods_barcode'];?>" />
      <input id="goods_nm" name="goods_nm" type="hidden" value="<?php echo $val['goods_nm'];?>" />
      <input id="goods_price" name="goods_price" type="hidden" value="<?php echo $val['goods_price'];?>" />
      <input id="goods_discount" name="goods_discount" type="hidden" value="<?php echo $val['goods_discount'];?>" />
      <input id="goods_unit" name="goods_unit" type="hidden" value="<?php echo $val['goods_unit'];?>" />
      <input id="goods_spec" name="goods_spec" type="hidden" value="<?php echo $val['goods_spec'];?>" />
      <input id="supp_id" name="supp_id" type="hidden" value="<?php echo $val['supp_id'];?>" /-->
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
      <dl class="row">
        <dt class="tit">
          <label for="total_price">已订商品总价</label>
        </dt>
        <dd class="opt red">
          <label id="total_price" for="total_price">0.00</label>
        </dd>
      </dl>
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
        title: '库存列表',// 表格标题
        reload: false,// 不使用刷新
        columnControl: false,// 不使用列控制
        buttons : [
            {
                display: '<i class="fa fa-plus"></i>新增商品',
                
                name : 'add',
                bclass : 'add',
                title : '新增导航',
                onpress : fg_operation
            },
            {
                display: '<i class="fa fa-trash"></i>批量删除',
                name : 'del',
                bclass : 'del',
                title : '将选定行数据批量删除',
                onpress : function() {
                    var ids = [];
                    $('.trSelected[data-id]').each(function() {
                        ids.push($(this).attr('data-id'));
                    });
                    if (ids.length < 1 || !confirm('确定删除?')) {
                        return false;
                    }
                    location.href = 'index.php?act=cms_navigation&op=cms_navigation_drop&navigation_id=__IDS__'.replace('__IDS__', ids.join(','));
                }
            }
        ]
    });

    $('a.confirm-del').live('click', function() {
        if (!confirm('确定删除？')) {
            return false;
        }
    });

    $("input[type='number']").bind('input propertychange', function() {  
        var barcode = this.id.split('_')[1];
        $("#unitprice_" + barcode).html((parseFloat($("#price_" + barcode).html()) * this.value).toFixed(2));
        var total_price = 0;
        $(".unit_price").each(function(index, element){
          total_price += parseFloat(element.innerText);
        });
        $("#total_price").html(total_price.toFixed(2));
    });  

});

function fg_operation(name, bDiv) {
    if (name == 'add') {
        window.location.href = 'index.php?act=client_stock&op=add_goods';
    }
}
function fg_del(id) {
    if(confirm('删除后将不能恢复，确认删除这项吗？')){
        $.getJSON('index.php?act=help_store&op=del_help', {id:id}, function(data){
            if (data.state) {
                $("#flexigrid").flexReload();
            } else {
                showError(data.msg)
            }
        });
    }
}

$(function(){$("#submitBtn").click(function(){
    if($("#orderForm").valid()){
      $("#orderForm").submit();
  }
  });
});
</script>