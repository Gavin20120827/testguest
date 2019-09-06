<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月30日/下午2:51:29
* 修改时间：2019年5月30日下午2:51:29
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


//唯一标识符
function _check_uniqid($_first_uniqid,$_end_uniqid){
    if ((strlen($_first_uniqid) != 40) || ($_first_uniqid!=$_end_uniqid)){
        _alert_back("唯一标识符异常");
    }
    return $_first_uniqid;
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
    
    global $_system;
    //限制敏感用户名,并且告知哪些不能注册
    $_mg=explode('|', $_system['string']);
    
    $mg_string = '';
    foreach ($_mg as $value){
        $mg_string .= $value;
    }
    
    if (in_array($_string, $_mg)){
        _alert_back($mg_string."用户名不得注册");
    }
    
    // 将用户名转义输入
    return $_string;
}


//创建一个函数，过滤密码
$_first_pass ="";
$_end_pass = "";

function _check_password($_first_pass,$_end_pass,$_min_num){
    //密码不得小于六位
    if (strlen($_first_pass)<$_min_num){
        _alert_back("密码不得小于".$_min_num."位");
    }

    //密码两次一致
    if (!($_first_pass==$_end_pass)){
        _alert_back("密码两次不一致，请重新输入");
    
    }
  
    //前两种一般加密
//     return sha1($_first_pass);
//     return md5($_first_pass);
    //多重加密
    return md5(md5($_first_pass).'LEE');

}


function _check_modify_password($_string,$_min_num){
    //密码不得小于六位
    if (!empty($_string)){
       if (strlen($_string)<$_min_num){
           _alert_back("密码不得小于".$_min_num."位");
       }   
    }else {
        return null;
    }

    return md5(md5($_string).'LEE');
}


//创建一个函数，过滤密码问题
function _check_question($_string,$_min_num,$_max_num){
    //长度小于4位或者大于20位
    if (mb_strlen($_string,'utf-8')<$_min_num || mb_strlen($_string,'utf-8')>$_max_num){
        _alert_back("密码提示不得小于".$_min_num."位或者大于".$_max_num."位");
    }      
    return  $_string;
//     return mysqli_real_escape_string($link, $string_to_escape)
}



//创建一个函数，过滤密码回答
function _check_answer($_qune,$_answ,$_min_num,$_max_num){
    //长度小于4位或者大于20位
    if (mb_strlen($_answ,'utf-8')<$_min_num || mb_strlen($_answ,'utf-8')>$_max_num){
        _alert_back("密码回答不得小于".$_min_num."位或者大于".$_max_num."位");
    }
  
    return  $_answ;
    //     return mysqli_real_escape_string($link, $string_to_escape)
}


//创建一个函数，过滤邮件
function _check_email($_string){
    if (!empty($_string)){
        if (!preg_match('/[\w\-\.]+@[\w\-\.]+(\.\w+)+$/', $_string)){
            _alert_back("邮件格式不正确");
        }
    }
    return $_string;
}


//创建一个函数，过滤qq以及其他
function _check_qq($_string){

    return $_string;
}

function _check_url($_string){
    
    return $_string;
}



function _check_connent($_string,$_min_num,$_max_num){
    //长度小于20位或者大于200位
    if (mb_strlen($_string,'utf-8')<$_min_num || mb_strlen($_string,'utf-8')>$_max_num){
        _alert_back("短信内容不得".$_min_num."位或者大于".$_max_num."位");
    }
    return $_string;
}

function _check_title($_string,$_min_num,$_max_num){
    //长度小于2位或者大于20位
    if (mb_strlen($_string,'utf-8')<$_min_num || mb_strlen($_string,'utf-8')>$_max_num){
        _alert_back("标题内容不得小于".$_min_num."位或者大于".$_max_num."位");
    }
    return $_string;
}

function _check_content($_string,$_min_num){
    //长度小于10位
    if (mb_strlen($_string,'utf-8')<$_min_num ){
        _alert_back("帖子内容不得".$_min_num."位");
    }
    return $_string;
}


function _check_photo_url($_string){
    if (empty($_string)){
        _alert_back("名称或者地址不能为空");
    }
    return $_string;
}

?>
