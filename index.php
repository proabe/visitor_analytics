<?php include "connection.php"; ?>
<!DOCTYPE html>
<html lang="">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <script>
        var d = new Date();
        var n = d.getTimezoneOffset();
        document.cookie = "mycookie=" + n;

    </script>
</head>

<body>



    <?php

function getIP()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])){
    if(filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
    {
        $ipadd = @$_SERVER['HTTP_CLIENT_IP'];
    }}
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
    if(filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
    {
        $ipadd =@$_SERVER['HTTP_X_FORWARDED_FOR'];
    }}
    else
    {
        $ipadd =$_SERVER['REMOTE_ADDR'];
    }

    return $ipadd;
}


$ip = getIP();

/*var_dump($ip);*/

$opts = array('http'=>array('method'=>"GET",'header'=>"Accept-language: en\r\n" ."User-Agent: not for you\r\n"));
$context = stream_context_create($opts);
   
    $url = 'https://www.whois.com/whois/'.trim((string)$ip);
    $html = @file_get_contents($url,true,$context);
    $body = new DOMDocument();

    libxml_use_internal_errors(TRUE);
    if (!empty($html)) {
        $body->loadHTML($html);
        libxml_clear_errors();
        $body_xpath = new DOMXPath($body);
        $all = $body_xpath->query('//pre[@class="df-raw"]');//hitting the website element where address is saved
        $s="";
        
       foreach($all as $value){
           $s.=$value->nodeValue;
       }
        $s=strstr(strstr($s,"address:"),"e-mail:",true);
        $addr=str_replace("address:","",$s);
        /*var_dump($addr);*/
   
    function getBrowser(){

$agent = $_SERVER['HTTP_USER_AGENT'];
$name = 'NA';


if (preg_match('/MSIE/i', $agent) && !preg_match('/Opera/i', $agent)) {
    $name = 'Internet Explorer';
} elseif (preg_match('/Firefox/i', $agent)) {
    $name = 'Mozilla Firefox';
} elseif (preg_match('/Chrome/i', $agent)) {
    $name = 'Google Chrome';
} elseif (preg_match('/Safari/i', $agent)) {
    $name = 'Apple Safari';
} elseif (preg_match('/Opera/i', $agent)) {
    $name = 'Opera';
} elseif (preg_match('/Netscape/i', $agent)) {
    $name = 'Netscape';
}


return $name;
}

$brow=(string)getBrowser();
        
    /*var_dump($brow);*/    
        
    
    }?>

        <?php
$newu="";
$oldu="";
    
session_start();
if(isset($_SESSION['views']))
  $_SESSION['views']=$_SESSION['views']+1;
  
else
  $_SESSION['views']=1;
/*echo "Views=". $_SESSION['views'];*/
if($_SESSION['views']>1){
   $oldu="old user";
}
else{
   $newu="new user";    
} 
 
?>
            <?php 
    $timezone_hr=(int)-($_COOKIE["mycookie"])/60;
    $timezone_min=(int)-($_COOKIE["mycookie"])%60;
    settype($timezone_hr,"integer");
     $today = date("g:i:s");
     $t=explode(":",$today);;
    $get=getdate();
    $time=((int)$t[0]+(int)$timezone_hr).":".((int)$t[1]+(int)$timezone_min).":".(int)$t[2];
   $tt=explode(":",$time);
     if($tt[1]>60){
         ++$tt[0];
         $tt[1]=$tt[1]-60;
     }
     $times=$tt[0].":".$tt[1].":".$tt[2];
    /*var_dump($times);*/
    $date=$get["mday"]."-".$get["month"]."-".$get["year"];
   /* var_dump($date);*/
    if($newu=="new user"){
    $sql = "INSERT INTO visit_data (Visitor_status ,IP, Loc, Time,Date,Browser) VALUES('$newu','$ip','$addr','$times','$date','$brow')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Success<br>";
        }else{
            echo "error";
        }
    }
    else{
           
        $sql = "INSERT INTO visit_data (Visitor_status ,IP, Loc, Time,Date,Browser) VALUES('$oldu','$ip','$addr','$times','$date','$brow')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Success<br>";
        }else{
            echo "error";
        }
    }
    ?>
</body>

</html>
