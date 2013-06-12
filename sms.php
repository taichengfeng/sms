<?php
//@require_once '../HiPaaS.inc.php';
//variable configuration
$host="api.hicloud.hinet.net"; //"hiapi.ext.hipaas.hinet.net";
$serviceid="14";
$isvid="367f7deaa1ce47b185a0c91cb6d8f714";//
$isvkey="n+ABj+1w6e1Ht2A2ziBh0Q==";

//get the token and sign
$a=hiapi_get_auth($host);

$token=$a[0];
$sign=$a[1];

$phone="0970030222";
$msg="testtest from local";

$msgid="A8032153305181373807"; //Cancel 
$sch_time="201306150000"; //假如要設定預約傳送日期

//smsSend();
//smsQuery();
//smsCancel();

switch ($_GET["op"]){
	case "send": 
		smsSend();
	break;
	
	case "query": 
		smsQuery();
	break;
	
	case "cancel": 
		smsCancel();
	break;
}

function smsSend(){
  global $isvid,$serviceid,$isvkey,$host,$token,$sign,$phone,$msg,$sch_time;  
  //$smsserver="http://sms.hiapi1.lab.hipaas.hinet.net/hisms/servlet/send";
  
  if($_GET["sch_time"]!=""){
  	$sch_time = $_GET["sch_time"]; 
  }
  
  $smsserver="hiair-api.hicloud.net.tw/hisms/servlet/send";
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $smsserver);
  curl_setopt($ch,CURLOPT_POST,1);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, 0); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, "isvAccount=$isvid&token=$token&sign=$sign&msisdn=$phone&msg=$msg&sch_time=$sch_time"); 
  $result = curl_exec($ch);
  print_r(explode("<br>",strip_tags($result,"<br>")));
}

function smsCancel(){
  global $isvid,$serviceid,$isvkey,$host,$token,$sign,$phone,$msgid;  
  //$smsserver="http://sms.hiapi1.lab.hipaas.hinet.net/hisms/servlet/send";
  $smsserver="hiair-api.hicloud.net.tw/hisms/servlet/cancel"; 
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $smsserver);
  curl_setopt($ch,CURLOPT_POST,1);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, 0); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, "isvAccount=$isvid&token=$token&sign=$sign&msgid=$msgid"); 
  $result = curl_exec($ch);
  print_r(explode("<br>",strip_tags($result,"<br>"))); 

}


function smsQuery(){
  global $isvid,$serviceid,$isvkey,$host,$token,$sign,$phone,$msgid;  
  //$smsserver="http://sms.hiapi1.lab.hipaas.hinet.net/hisms/servlet/send";
  if($_GET["msgid"]!=""){
  	$sch_time = $_GET["msgid"]; 
  }
  $smsserver="hiair-api.hicloud.net.tw/hisms/servlet/query"; 
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $smsserver);
  curl_setopt($ch,CURLOPT_POST,1);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HEADER, 0); 
  curl_setopt($ch, CURLOPT_POSTFIELDS, "isvAccount=$isvid&token=$token&sign=$sign&msgid=$msgid"); 
  $result = curl_exec($ch);
  print_r(explode("<br>",strip_tags($result,"<br>"))); 

}




function print_getauth($json_get_auth){
	$jsonresult=json_decode($json_get_auth);
	$token=$jsonresult->info->token;
	$sign=$jsonresult->info->sign;
}

function hiapi_get_auth($host){

  global $isvkey,$isvid,$serviceid;

  $nonce = substr(md5(uniqid('nonce_', true)),0,16);
  $timestamp=round(microtime(true)*1000);
  $sdksign=sha1($isvkey.$nonce.$timestamp);
  $url="http://$host/SrvMgr/requestToken/$isvid/$serviceid/$nonce/$timestamp/$sdksign/";
  //print $url."\n";
  
  $ch = curl_init();
  
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
  $r=curl_exec($ch);
  $jsonresult=json_decode($r);
  $token=$jsonresult->info->token;
  $sign=$jsonresult->info->sign;
  return array($token,$sign);
}
?>
