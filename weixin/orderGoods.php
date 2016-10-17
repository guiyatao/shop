<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>订单详情-共铸商城零售店</title>
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

$gongzhuying = new gongzhuying();

//入库操作
if(isset($_GET['op']) && $_GET['op'] == 'instock'){
    $update = array();
    $update['in_flag'] = 1;
    $update['order_status'] = 1;
    $now = date("Y-m-d H:i:s",time());
    $update['in_date'] = $now;
    $update['pay_start_time'] = $now;
    $where = array();
    $where['id'] = $_GET['order_id'];
    $result = $gongzhuying->updateClientOrder($update,$where);
    if($result){
        ?>
        <h1 style="text-align: center;color: red;">Message</h1>
        <div class="weui-msg" style="border: 1px solid red;">
            <div class="weui-iconarea"><i class="weui-icon-success weui-icon_msg"></i></div>
                <div class="weui-textarea">
                <h2 class="weui-msg__title">入库成功</h2>
               
                </div>
            <div class="weui_opr_area">
                <p class="weui_btnarea">
                <a href="myorders.php" class="weui-btn weui-btn_primary">确定</a>
                <a href="myorders.php" class="weui-btn weui-btn_default">取消</a>
                </p>
            </div>
        </div>
        <?php
    }else{
        ?>
        <h1 style="text-align: center;color: red;">Message</h1>
        <div class="weui-msg" style="border: 1px solid red;">
            <div class="weui-iconarea"><i class="weui-icon-warn weui-icon_msg"></i></div>
                <div class="weui-textarea">
                <h2 class="weui-msg__title">入库失败</h2>
                
                </div>
            <div class="weui_opr_area">
                <p class="weui_btnarea">
                <a href="myorders.php" class="weui-btn weui-btn_primary">确定</a>
                <a href="myorders.php" class="weui-btn weui-btn_default">取消</a>
                </p>
            </div>
        </div>
        <?php
    }
    
}
//显示
else{ 
    $order = $gongzhuying->getClientOrderById(trim($_GET['order_id']));
    if(count($order) > 0){
        if(count($order[0]['goods']) > 0){
            foreach($order[0]['goods'] as $k => $v){
                ?>
            <div class="weui-cells">
                <div class="weui-cell">
                    <div class="weui-cell__bd weui-cell_primary">
                        <p>商品条码</p>
                    </div>
                    <div class="weui-cell__ft">
                        <?php echo $v['goods_barcode']; ?>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <p>商品名称</p>
                    </div>
                    <div class="weui-cell__ft">
                        <?php echo $v['goods_nm']; ?>
                    </div>
                </div>
               <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <p>订购数量</p>
                    </div>
                    <div class="weui-cell__ft">
                        <?php echo $v['set_num']; ?>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <p>价格(按批发单位/<?php echo $v['goods_unit'];?>,打折后)</p>
                    </div>
                    <div class="weui-cell__ft">
                        <?php echo $v['goods_discount_price']; ?>
                    </div>
                </div>
                <div class="weui-cell">
                    <div class="weui-cell__bd">
                        <p>总额</p>
                    </div>
                    <div class="weui-cell__ft">
                        <?php echo $v['actual_amount']; ?>
                    </div>
                </div>
                
            </div>
    
            <?php 
            }
            if($order[0]['refund_flag'] == 0){ 
            ?>
            <br>
            <a href="orderGoods.php?op=instock&order_id=<?php echo $_GET['order_id']; ?>" class="weui-btn weui-btn_primary">确认</a>
                
            <?php 
            }
        }else{
            print_r("此订单无商品");
        }
        
    }else{
        print_r("无此订单");
    }
        
}

?>

</body>

</html>