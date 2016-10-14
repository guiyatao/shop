<html>
	<head>
		<script src="./jquery.js"></script>
	</head>
	
	<body>

		<div id="result"></div>
		  
		<script>
			$(document).ready(function(){
				var $baseurl = "http://192.168.3.250/mobile/";

				var $testurl = $baseurl + "index.php?act=login";
				var $data = {username:"qianqi",password:"123456",client:"android"};
// 				testURL($testurl, $data, "get key");


				$testurl = $baseurl + "index.php?act=member_order&op=order_info&order_id=226";
				$data = {
						key:"bcce43d7820ffbf0528831fe3fdb16da"
						//key:"caf86a4a19f7a748a8d240f616242a0a"
				};
				//testURL($testurl, $data, "get order");


                $testurl = $baseurl + "index.php?act=member_buy&op=buy_step2";
                $data={offpay_hash_batch:"m_-Qn_Zj4w7M8ejDUthoTk5eTgKdXWqCIG1-Ash5dkL6rmo", 
                		password:"", 
                		offpay_hash:"5uoQ6CXPL7yNjM0NIpCClPgA66_x_1htBjS-O_N", 
                		pay_name:"online", 
                		rcb_pay:"0", 
                		ifcart:"0", 
                		pay_message:"1|", 
                		cart_id:"569713|1", 
                		order_pickup_type:"1", 
                		invoice_id:"", 
                		key:"7d5c57147c1878eb84db858641b0ceb8", 
                		pd_pay:"0", 
                		address_id:"15", 
                		order_pickup_store:"XYBL0000102", 
                		vat_hash:"prnQtqwJ-lf-RsTF1eTk-yFv1Rj-F1dd4Mc"
                			};

                testURL($testurl, $data, "get order");
			});



			
			
			function testURL($url, $data, $prefixmsg) {
				$.ajax({  
					type : "post",  
					url : $url,  
					data : $data,  
					async : false,  
					success : function(result){  
						$("#result").append("<br/>===================================<br/>" + $prefixmsg + "<br/>" + result + "<br/>===================================<br/>");
					}  
				}); 
			}
		</script>
	</body>
</html>