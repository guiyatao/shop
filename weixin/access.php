<?php

$APPID="wx134e9c8f60f06f47";
$APPSECRET="ad43034140c565002ee93d3342dc6ea6";

$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$APPID."&secret=".$APPSECRET;

//$TOKEN_URL="https://api.weixin.qq.com/cgi-bin/user/info?access_token=oUvc-mWly_8iID-d7y0t3J64N3UNl_jZ1kiStHE1hzBd_W-mFN0nxFvooaNFvCYEd4ZVw4n7LwTtuZgahLkzzGLhwxMwnIqb54DNQPt5YoaznarycJ0vZt02ELZG2tiuJMXdAGAQTE&openid=oA_6CvyqKLO72H61Lig81d9VMoeY&lang=zh_CN";
$json=file_get_contents($TOKEN_URL);
$result=json_decode($json,true);
print_r($result);

?>