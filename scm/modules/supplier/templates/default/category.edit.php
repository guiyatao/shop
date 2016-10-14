<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=category&op=index" title="返回分类列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo "商品分类管理"?> - <?php echo "修改"?></h3>
                <h5><?php echo "供应商的所有商品分类管理"?></h5>
            </div>
        </div>
    </div>

    <form id="category_form" enctype="multipart/form-data" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <input type="hidden" id="cate_id" name="cate_id" value="<?=$output['category']['cate_id']?>" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="name"><em>*</em>分类名称</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?=$output['category']['category_name']?>" name="category_name" id="category_name" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="pid">上级分类</label>
                </dt>
                <dd class="opt">
                    <select name="pid" class="valid">
                        <option value="0">--请选择--</option>
                        <?php if(!empty($output['parent_list']) && is_array($output['parent_list'])){ ?>
                            <?php foreach($output['parent_list'] as $k => $v){ ?>
                                <option <?php if($output['category']['pid'] == $v['cate_id']){ ?>selected='selected'<?php } ?> value="<?php echo $v['cate_id'];?>"><?php echo $v['category_name'];?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <p class="notic"> 如果选择上级分类，那么新增的分类则为被选择上级分类的子分类</p>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="is_open">是否开启</label>
                </dt>
                <dd class="opt">
                    <input type="radio" name="is_open" <?php if($output['category']['is_open'] == 1){ ?> checked<?php } ?>  value="1"  /> 开启
                    <input type="radio" name="is_open" <?php if($output['category']['is_open'] == 0){ ?> checked<?php } ?>  value="0" /> 关闭
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <lebel for="sort">排序</lebel>
                </dt>
                <dd class="opt">
                    <input type="text" value="<?=$output['category']['sort']?>" name="sort" id="sort" class="input-txt">
                    <span class="err"></span>
                    <p class="notic">数字范围为0~255，数字越小越靠前</p>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>

        </div>
    </form>
</div>

<script type="text/javascript">
    $(function(){
        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){

            if($("#category_form").valid()){
                $("#category_form").submit();
            }
        });
        $('#category_form').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                category_name: {
                    required : true,
                    minlength: 1,
                    maxlength: 15,
                    remote   : {
                        url :'index.php?act=category&op=ajax&branch=check_category_name',
                        type:'get',
                        data:{
                            category_name : function(){
                                return $('#category_name').val();
                            },
                            cate_id : function(){
                                return $('#cate_id').val();
                            }
                        }
                    }
                },
                "sort":{
                    required: true,
                    range:[0,255],
                    digits:true,
                }
            },
            messages : {
                category_name: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "分类名不能为空";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "分类名必须在1-15位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "分类名必须在1-15位之间";?>',
                    remote   : '<i class="fa fa-exclamation-circle"></i><?php echo "分类名有重复,请您换一个";?>'
                },
                sort:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "排序不能为空";?>',
                    range:'<i class="fa fa-exclamation-circle"></i><?php echo "排序值必须在0-255之间";?>',
                    digits:'<i class="fa fa-exclamation-circle"></i><?php echo "必须输入整数";?>',
                }
            }
        });
    });
</script>