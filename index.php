<?php

/***********************************
 *Note:		:抓取全网的域名com,cn.net
 *Author	:Kermit
 *note		:中国.山东.青岛
 *date		:2016
***********************************/

//nohup php /opt/shell/index.php >>webinfo.log 2>&1 &

//基本设置
define('DEBUG', true);
define('ROOTPATH', str_replace('\\','/',dirname(__FILE__)).'/');

//加载数据库类
require("db_do.php");
$db_do = new db_do();

//curl方法
function getPageText($url)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER ,1); 
    curl_setopt($curl, CURLOPT_HEADER,false);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($curl, CURLOPT_TIMEOUT,15);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($curl);
    
    $curl_errno = curl_errno($curl);
    if($curl_errno) return false;
    return $result;
}

//对抓取的关键词、描述等进行特殊处理以防意外终止
function getPart($char, $max)
{
    $char = trim($char);
    if(strlen($char)> $max) $char = mb_substr($char, 0, $max,'utf-8');
    return $char;
}


//循环抓取
while(1)
{
    //1,提取URL
    $url_arr = $db_do->db_getone("select id,url from webinfo where status = 0 order by id asc limit 1");
    if(!$url_arr) { sleep(5); continue;}

    $url = $url_arr['url'];
    $id = $url_arr['id'];
    $text = getPageText($url);
    $data = array();

    //标题、关键词、描述信息
    $ra = preg_match('/<title>(.*?)<\/title>/s', $text, $title);
    if($ra > 0) $data['title'] = getPart($title[1],100);
    $ra = preg_match('/meta\s*name="keywords"\s*content="(.*?)"\s*\/>/s', $text, $keywords);
    if($ra > 0) $data['keywords'] = getPart($keywords[1], 255);
    $ra = preg_match('/meta\s*name="description"\s*content="(.*?)"\s*\/>/s', $text, $description);
    if($ra > 0) $data['description'] = getPart($description[1], 255);
    $data['status'] = 1;
    $db_do->db_update_mutidata('webinfo', $data, "id = {$id} ");
    
    $ra = preg_match_all('/(http:\/\/(www\.)?[_a-z0-9-]*\.(com|cn|net))/s', $text, $match);
    if($match[1])
    {
        $urlArr = array_unique($match[1]);
        foreach ($urlArr as $k => $url)
        {
            $array= array('url'=> $url);
            $db_do->db_insert_mutidata('webinfo', $array);
        }
    }
    
    echo "ok:".date("Y-m-d H:i:s").'--'.$url."\r\n";

}//end while