<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月1日/下午9:40:18
* 修改时间：2019年6月1日下午9:40:18
* 修改备注：   
* 版本：
*/


// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用"); 
}


if (!(function_exists("_alert_back"))){
    exit("_alert_back()函数不存在");
}


// 创建一个函数，过滤用户名
function _check_username($_string,$_min_num,$_max_num){
    //去掉两边的空格
    $_string = trim($_string);
    
    //长度小于两位或者大于20位
    if (mb_strlen($_string,'utf-8')<$_min_num || mb_strlen($_string,'utf-8')>$_max_num){
        _alert_back("用户名长度小于".$_min_num."位或者大于".$_max_num."位");
    }
    
    //限制敏感字符
    $_char_pattern = '/[<>\'\ \  \ ]/';
    if (preg_match($_char_pattern, $_string)){
        _alert_back("用户名不得包含敏感字符");
    }
    
// 将用户名转义输入
return $_string;
}


//创建一个函数，过滤密码
$_string ="";
function _check_password($_string,$_min_num){
    //密码不得小于六位
    if (strlen($_string)<$_min_num){
        _alert_back("密码不得小于".$_min_num."位");
    }
    
    return md5(md5($_string).'LEE');
    
}

// 保存cookie
function _setcookies($_username,$_time){

    switch ($_time){
        case '0':  //浏览器进程
            setcookie('username',$_username);
            break;
        case '1':  //一天
            setcookie('username',$_username,time()+86400);
            break;
        case '2':  //一周
            setcookie('username',$_username,time()+604800);
            break;
    } 
}



?>
