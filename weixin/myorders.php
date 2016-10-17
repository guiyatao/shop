<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>我的订单-共铸商城零售店</title>
<meta name="viewport"
	content="width=device-width,initial-scale=1,user-scalable=0">
<link rel="stylesheet" href="./css/weui.min.css" />

<style>
img {
	max-width: 100%;
}
</style>
</head>
<body>

<?php
/**
 * 微信oAuth认证示例
 */
ini_set("display_errors", "On");
error_reporting(E_ALL | E_STRICT);

require_once "./gongzhuying.php";

$auth = new wxauth();

$openid = $auth->wxuser['open_id'];

$gongzhuying = new gongzhuying();

$isStoreOwner = $gongzhuying->checkIfStoreOwner($openid);

if($isStoreOwner){
    //获取当前终端店未入库,未申请退款的订单
    $where = array(
        "clie_id" => $isStoreOwner[0]['clie_id'],
        "order_status" => 0,
        "out_flag" => 1,
        "in_flag" => 0,
        "refund_flag" =>0,
    );
    $unstoragelist = $gongzhuying->getUnStorageList($where);
   ?>
    <div class="weui-cells__title"><?php echo $isStoreOwner[0]['clie_ch_name']; ?>——未入库订单</div>
    <?php
    if(count($unstoragelist) > 0){
         foreach($unstoragelist as $k => $v){
        ?>
        <div class="weui-cells">
            <div class="weui-cell">
                <div class="weui-cell__bd weui-cell_primary">
                    <p>订单号</p>
                </div>
                <div class="weui-cell__ft">
                    <?php echo $v['order_no']; ?>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>供应商名</p>
                </div>
                <div class="weui-cell__ft">
                    <?php echo $v['supp_ch_name']; ?>
                </div>
            </div>
            <a class="weui-cell" href="orderGoods.php?order_id=<?php echo $v["id"]; ?>">
                <div class="weui-cell__bd">
                    <p style="color:black;">详情</p>
                </div>
                <div class="weui-cell__ft">
                                                            点击查看详情
                </div>
            </a>
            <?php if($v['refund_flag'] == 1 ){?>
            <div class="weui-cell">
                <div class="weui-cell__bd">
                    <p>退款状态</p>
                </div>
                <div class="weui-cell__ft">
                                                            已申请退款
                </div>
            </div>
            <?php }elseif ($v['refund_flag'] == 0){ ?>
             <a href="orderGoods.php?op=instock&order_id=<?php echo $v["id"]; ?>" class="weui-btn weui-btn_primary">确认</a>
            <?php
            }
            ?>
            
        </div>

        <?php 
        }
    }
}
else {
    print_r("您不是终端店的店主");
}
    


?>

</body>
</html>