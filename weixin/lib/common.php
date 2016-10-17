<?php

define("WECHAT_TOKEN", "gongzhuying_weixin_service");
define("WECHAT_APPID", "wx134e9c8f60f06f47");
define("WECHAT_APPSECRET", "ad43034140c565002ee93d3342dc6ea6");

function addToLog($db, $content,$uid=0) {
    date_default_timezone_set("Asia/Shanghai");
	$data=array("content"=>$content,
				"created"=> date('Y-m-d H:i:s'),
	            "uid"=>$uid,
	           "uri"=>$_SERVER["REQUEST_URI"],
	            "remoteip"=>$_SERVER["REMOTE_ADDR"]
				);
	$id=$db->insert("weixin_log",$data);
	return $id;
}
