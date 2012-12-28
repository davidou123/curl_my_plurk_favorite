<?php
set_time_limit(0);
function do_action($url,$new_parms=array())
{

		$oauth_consumer_key = ""; //你的consumer_key complain
		$oauth_consumer_secret = ""; //你的consumer_secret
		$oauth_token = ""; //你的tokeny_key
		$oauth_token_secret = ""; //你的token_secret
	
	
  //  global $oauth_consumer_key,$oauth_token,$oauth_consumer_secret,$oauth_token_secret;
    $oauth_nonce = rand(10000000,99999999);
    $oauth_timestamp = time();
    $parm_array = array("oauth_consumer_key"=>$oauth_consumer_key,"oauth_nonce"=>$oauth_nonce,"oauth_consumer_key"=>$oauth_consumer_key,"oauth_signature_method"=>"HMAC-SHA1","oauth_timestamp"=>$oauth_timestamp,"oauth_token"=>$oauth_token,"oauth_version"=>"1.0");
    $parm_array = array_merge($parm_array,$new_parms);
    $base_string = sort_data($parm_array);
    $base_string = "POST&".rawurlencode($url)."&".rawurlencode($base_string);

    $key = rawurlencode($oauth_consumer_secret)."&".rawurlencode($oauth_token_secret);
    $oauth_signature = rawurlencode(base64_encode(hash_hmac("sha1",$base_string,$key,true)));

    $parm_array = array_merge($parm_array,array("oauth_signature"=>$oauth_signature));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS,sort_data($parm_array)); 
    $data = curl_exec($ch); 
    curl_close($ch);
    return json_decode($data,TRUE);
}
function sort_data($data)
{
    ksort($data);
    $string="";
    foreach($data as $key=>$val)
    {
           if($string=="")
           {
            $string = $key."=".$val;
           }
           else
           {
               $string .= "&".$key."=".$val;
           }
    }
    return $string;
}
/*
  array(23) {
    ["replurkers_count"]=>
    int(0)
    ["replurkable"]=>    bool(true)
    ["favorite_count"]=>   int(0)
    ["is_unread"]=>    int(1)
    ["content"]=>    string(337) "最多人說讚的訊息 [2012/12/27]<br />第1名:<br /><a href="http://www.plurk.com/p/hwasg0" class="ex_link plink meta"><img src="http://images.plurk.com/3tX7GDF7ZcW1mjnXj2G3EJ.jpg" height="48" />[咎井淳] 假日project─成人版(如果您已成年還是個資深腐女且閱讀過言之罪,請進這裡,不符合上述條...</a>"
    ["user_id"]=>    int(4902064)
    ["plurk_type"]=>    int(0)
    ["qualifier_translated"]=>    string(0) ""
    ["replurked"]=>    bool(false)
    ["favorers"]=>    array(0) {    }
    ["replurker_id"]=>    NULL
    ["owner_id"]=>    int(9248794)
    ["responses_seen"]=>    int(0)
    ["qualifier"]=>    string(1) ":"
    ["plurk_id"]=>    int(1082600434)
    ["response_count"]=>    int(7)
    ["limited_to"]=>    NULL
    ["no_comments"]=>    int(0)
    ["posted"]=>    string(29) "Fri, 28 Dec 2012 06:35:02 GMT"
    ["lang"]=>    string(5) "tr_ch"
    ["content_raw"]=>    string(76) "最多人說讚的訊息 [2012/12/27]
第1名:
http://www.plurk.com/p/hwasg0"
    ["replurkers"]=>    array(0) {    }
    ["favorite"]=>    bool(false)
  }*/
?>
<?
require_once("SQLconnection.php");
function sqlinsert($url,$owner_id,$qualifier_translated,$content,$posted){
	$sql = "INSERT INTO myfavorite (link,uid, qualifier_translated, content, date) values
		('".$url."','".$owner_id."', '".$qualifier_translated."', '".$content."', '".$posted."')";
	// 建立MySQL資料庫連結
	$link = create_connection();
	// 執行SQL指令
	$result =mysql_query($sql);
	mysql_close($link);  //關閉sql*/
	return $sql;

}
	 $_GET ['next']= str_replace (":", "%3A", $_GET ['next']);  //網址列會把它改掉 所以要改回來
$data = do_action("http://www.plurk.com/APP/Timeline/getPlurks",array("limit"=>100,"filter"=>"only_favorite","offset"=>$_GET ['next']));//"offset"=>"2010-6-20T21%3A55%3A34"
 
$data = $data['plurks'];
//print_r($data);
if($data){
foreach($data as $key=>$val)
{

	$purl= base_convert($val['plurk_id'], 10, 36);
	$posted=date("Y/m/d H:i:s", strtotime($val['posted']));
     $echo_out.= $val['owner_id'].$val['qualifier_translated']."<pre>".$val['content']."</pre>噗".$purl." ".$posted."<hr>";
	 $lastpost=date("Y-m-d H:i:s", strtotime($val['posted'])); //2010-6-20T21%3A55%3A34
	 $lastpost= ereg_replace (" ", "T", $lastpost); 
	// $lastpost= str_replace (":", "%3A", $lastpost); 
	 $_GET ['next']=$lastpost;
 sqlinsert($purl,$val['owner_id'],$val['qualifier_translated'],$val['content'],$posted);
}}
else{echo"跑完";$finish=true;}
	
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?if($finish!=true){?>
<meta http-equiv="refresh" content="1;url='?next=<?=$_GET ['next']?>'" /> 
<?}?>
<title>抓取我的最愛的噗</title>
</head>
<body>
<fieldset>
<legend>取得前30則未讀的噗浪</legend>
<?=$echo_out?>
</fieldset>
<hr />
<?=$lastpost?>
<center>
</center>
</body>
</html>