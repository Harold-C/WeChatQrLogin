<html lang="zh-CN">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="format-detection" content="telephone=no" />

		<title>微信验证DEMO</title>
		<style type="text/css">
			table {
				border: 2px #333 solid;
			}
			table td {
				border:1px #999 solid
			}
		</style>
	</head>
	<body>
		<?php
		function curl_file_get_contents($durl){
   $ch = curl_init($durl);
   curl_setopt($ch,CURLOPT_HEADER,1); 
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   $r = curl_exec($ch);
   curl_close($ch);
   return $r;
 }
			//uuid生成地址
			$uidUrl = 'https://login.weixin.qq.com/jslogin?appid=wx782c26e4c19acffb&redirect_uri=https%3A%2F%2Fwx.qq.com%2Fcgi-bin%2Fmmwebwx-bin%2Fwebwxnewloginpage&fun=new&lang=zh_CN&_='.time()*1000;
			//获取返回
			$tmpResp = curl_file_get_contents($uidUrl);
			$tmpArr = explode ('"', $tmpResp); 
			//获取uuid
			$uid = $tmpArr[1];
			//输出二维码
			// echo ('<img src="http://'.$_SERVER['SERVER_ADDR'].'/index.php/Api/WxVerify/getQr/uid/'.$uid.'">');
			echo ('<img src="http://demo.5-f.net/qr/index.php/Api/WxVerify/getQr/uid/'.$uid.'">');
		?>

		<script src="//cdn.bootcss.com/jquery/2.2.3/jquery.min.js"></script>

		<script type="text/javascript">
				var int = setInterval(
					function() {
						$.ajax({
					        // url: 'http://<?php echo($_SERVER['SERVER_ADDR']) ?>/index.php/Api/WxVerify/getUsrInfo/uid/<?php echo($uid) ?>',
					        url: 'http://demo.5-f.net/qr/index.php/Api/WxVerify/getUsrInfo/uid/<?php echo($uid) ?>',
					        type: 'GET',
					        dataType: 'HTML',
					        timeout: 60000,
					        success: function (result) {
					        	//读取返回
					        	if (result != 'false') {
					        		document.write(result);
					        		clearInterval(int);
					        	}              
					        }
				      })
					}
				,15000)	
		</script>
	</body>
</html>