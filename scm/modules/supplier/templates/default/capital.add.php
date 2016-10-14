<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=capital&op=index" title="返回资金列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3><?php echo "资金表管理"?> - <?php echo "新增"?></h3>
                <h5><?php echo "当前供应商的所有资金表管理"?></h5>
            </div>
        </div>
    </div>

    <form id="capital_form" method="post">
        <input type="hidden" name="form_submit" value="ok" />
        <div class="ncap-form-default">
            <dl class="row">
                <dt class="tit">
                    <label for="name"><em>*</em>开户行</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="" name="supp_bank" id="supp_bank" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="name"><em>*</em>卡号</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="" name="supp_cardno" id="supp_cardno" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <dl class="row">
                <dt class="tit">
                    <label for="name"><em>*</em>资金(元)</label>
                </dt>
                <dd class="opt">
                    <input type="text" value="" name="supp_capital" id="supp_capital" class="input-txt">
                    <span class="err"></span>
                </dd>
            </dl>
            <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>

        </div>
    </form>
</div>


<script type="text/javascript">
    $(function(){
        //验证价格
        jQuery.validator.addMethod( "price",function(value,element){
            var pattern = /^[0-9]+(\.[0-9]{0,2})?$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入有效的价格' );

        //验证银行卡号
        jQuery.validator.addMethod( "bankcard",function(value,element){
            var pattern = /^(\d{16}|\d{19})$/;
            if(value !='') {
                if(!pattern.exec(value))
                {
                    return false;
                }
            };
            return true;
        } ,  '<i class="fa fa-exclamation-circle"></i>请输入有效的银行卡号' );

        //按钮先执行验证再提交表单
        $("#submitBtn").click(function(){

            if($("#capital_form").valid()){
                $("#capital_form").submit();
            }
        });
        $('#capital_form').validate({
            errorPlacement: function(error, element){
                var error_td = element.parent('dd').children('span.err');
                error_td.append(error);
            },
            rules : {
                supp_bank: {
                    required :true,
                    minlength: 1,
                    maxlength: 15,
                },
                supp_cardno:{
                    required:true,
                    bankcard:true,
                    remote   : {
                        url :'index.php?act=capital&op=ajax&branch=check_card_no',
                        type:'get',
                        data:{
                            supp_cardno : function(){
                                return $('#supp_cardno').val();
                            },
                            capital_id : ''
                        }
                    }
                },
                supp_capital:{
                    required: true,
                    price: true,
                }
            },
            messages : {
                supp_bank: {
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "开户银行不能为空";?>',
                    maxlength: '<i class="fa fa-exclamation-circle"></i><?php echo "开户银行必须在1-15位之间";?>',
                    minlength: '<i class="fa fa-exclamation-circle"></i><?php echo "开户银行必须在1-15位之间";?>',

                },
                supp_cardno:{
                    required : '<i class="fa fa-exclamation-circle"></i><?php echo "卡号不能为空";?>',
                    bankcard : '<i class="fa fa-exclamation-circle"></i><?php echo "请输入有效的银行卡号";?>',
                    remote   : '<i class="fa fa-exclamation-circle"></i><?php echo "银行卡号有重复,请您换一个";?>',
                },
                supp_capital:{
                    required: '<i class="fa fa-exclamation-circle"></i><?php echo "资金不能为空";?>',
                    price: '<i class="fa fa-exclamation-circle"></i><?php echo "请输入有效的资金";?>',
                }
            }
        });
    });
</script>