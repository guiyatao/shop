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
$homeurl = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".WECHAT_APPID."&redirect_uri=http%3A%2F%2Fwww.gongzhuying.com%2Fweixin%2Fmyorders.php&response_type=code&scope=snsapi_base&state=wxbase#wechat_redirect"; 
try {
    $auth = new wxauth();
} catch (Exception $e) {
    
    header('Location: ' . $homeurl);
}

$gongzhuying = new gongzhuying();

$order = $gongzhuying->getClientOrderById(trim($_GET['order_id']));


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
    $where['clie_id'] = $order[0]['clie_id'];
    
    $result = $gongzhuying->updateClientOrder($update,$where);
    if($result){
        ?>
        <h1 style="text-align: center;color: red;">入库信息提示</h1>
        <div class="weui-msg" style="border: 1px solid red;">
            <div class="weui-iconarea"><i class="weui-icon-success weui-icon_msg"></i></div>
                <div class="weui-textarea">
                <h2 class="weui-msg__title">入库成功</h2>
               
                </div>
            <div class="weui_opr_area">
                <p class="weui_btnarea">
                <a href="<?php echo $homeurl;?>" class="weui-btn weui-btn_primary">返回未入库列表</a>
                </p>
            </div>
        </div>
        <?php
    }else{
        ?>
        <h1 style="text-align: center;color: red;">入库信息提示</h1>
        <div class="weui-msg" style="border: 1px solid red;">
            <div class="weui-iconarea"><i class="weui-icon-warn weui-icon_msg"></i></div>
                <div class="weui-textarea">
                <h2 class="weui-msg__title">入库失败</h2>
                
                </div>
            <div class="weui_opr_area">
                <p class="weui_btnarea">
                <a href="<?php echo $homeurl;?>" class="weui-btn weui-btn_primary">返回未入库列表</a>
                </p>
            </div>
        </div>
        <?php
    }
    
}
//显示
else{ 
   
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
                        <p>总额(元)</p>
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
            
            <a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx134e9c8f60f06f47&redirect_uri=http%3A%2F%2Fwww.gongzhuying.com%2Fweixin%2ForderGoods.php%3Fop%3Dinstock%26order_id%3D<?php echo $_GET['order_id']; ?>&response_type=code&scope=snsapi_base&state=wxbase#wechat_redirect" class="weui-btn weui-btn_primary">确认</a>
                
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