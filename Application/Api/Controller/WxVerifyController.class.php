<?php
namespace Api\Controller;
use Think\Controller;
class WxVerifyController extends Controller {
private function curl_file_get_contents1($durl){
   $ch = curl_init($durl);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
   $r = curl_exec($ch);
   curl_close($ch);
   return $r;
 }

    //获取WEB微信登陆二维码
    public function getQr() {
    	//通过get传入uuid
    	$uid = I('get.uid');
    	//根据uuid输出二维码
		$qrUrl = 'https://login.weixin.qq.com/qrcode/'.$uid;
		$qrImg = $this->curl_file_get_contents1($qrUrl);
        if (empty($qrImg)) {
            echo('系统忙，请稍后再试。');
        } else {
            header("Content-Type: image/jpeg;text/html; charset=utf-8");
            echo $qrImg;
        }		
    }

    //获取微信好友信息
    public function getUsrInfo() {
        //通过get传入uuid
        $uid = I('get.uid');
        //判断微信登陆状态地址
        $logUrl = 'https://login.wx2.qq.com/cgi-bin/mmwebwx-bin/login?uuid='.$uid;
        //获得微信登陆跳转地址
        $tmpResp = $this->curl_file_get_contents1($logUrl);
        $tmpArr = explode ('"', $tmpResp); 
        //判断是否获得地址
        if (!empty($tmpArr[1])) {
            //有跳转地址
            $url = $tmpArr[1]; 
            //初始化curl访问对象
            $ch = curl_init($url); 
            curl_setopt($ch,CURLOPT_HEADER,1); 
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            //获取访问对象
            $content = curl_exec($ch); 
            //正则表达获取关键cookie
            preg_match('/wxuin=(.*);/iU',$content,$str); 
            $wxuin = $str[1]; 
            preg_match('/wxsid=(.*);/iU',$content,$str); 
            $wxsid = $str[1]; 
            preg_match('/webwx_data_ticket=(.*);/iU',$content,$str); 
            $webTik = $str[1]; 
            curl_close($ch); 
            $host = parse_url($url);

            //构造web微信访问地址
            $url = 'https://'.$host['host'].'/cgi-bin/mmwebwx-bin/webwxgetcontact?r='.time()*1000;
            //组装cookie
            $cookie = 'wxuin='.$wxuin.'; wxsid='.$wxsid.'; webwx_data_ticket ='.$webTik;
            //初始化curl访问对象
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_COOKIE, $cookie);
            //获取访问对象并解析
            $output = curl_exec($ch);
            curl_close($ch);
            //解析访问对象
            $output = json_decode($output,true);
			echo '<style type="text/css">table {border: 2px #333 solid;}table td {border:1px #999 solid}</style>';
			echo '<table><tr><td>序号</td><td>名称</td><td>备注名</td><td>性别</td><td>省</td><td>市</td></tr>';
			foreach($output['MemberList'] as $key=>$value) {
				echo '<tr><td>'.($key + 1).'</td><td>'.$value['NickName'].'</td><td>'.$value['RemarkName'].'</td><td>'.($value['Sex'] = '0'?'公众号':$value['Sex']).'</td><td>'.$value['Province'].'</td><td>'.$value['City'].'</td></tr>';
			}
            //var_dump($output);
        } else {
            echo ('false');
        }
		echo '</table>';
    }
}