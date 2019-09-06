<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/上午12:54:57
* 修改时间：2019年5月28日上午12:54:57
* 修改备注：   
* 版本：
*/

error_reporting(0); //只适用于当前页

//这个文件放的是系统初始化数据，公共文件


// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用");
}

//转换硬路径常量
define("root_path", substr(dirname(__FILE__), 0,-9)) ;

//拒接新版本
if (PHP_VERSION < "4.1.0"){
    exit("version is too low!");}


//引入核心函数库
require root_path.'/includes/global.func.php';
require root_path.'/includes/mysqli.func.php';
        
//执行耗时。应该包装成函数，会更简洁（如果放到首页，end_time需要放到代码尾）
//［1］、［2］指的是数组的键
// $_mtime = explode(' ', microtime()) ;
// $_start_time = $_mtime[1]+$_mtime[0];

// usleep(2000000)   

// $_mtime = explode(' ', microtime()) ;
// $_end_time = $_mtime[1]+$_mtime[0];
    
// echo $_end_time - $_start_time

//定义称为常量。如果是变量$_start_time，那就没有办法在footer中使用
define("_start_time", _runtime());
// usleep(2000000);             //这个部分，只是在写程序过程中比较方便看出效果

// $_end_time = _runtime();
// echo $_end_time - $_start_time;

//以下应该放到footer页面
// $_end_time = _runtime();
// echo $_end_time - $_start_time
    
    



//初始化数据库
_connect();    //连接
_select_db();   //选择数据库
_set_name();     //设置字符集



//短信提醒

$_message=_fetch_array("select count(tg_id) as count from tg_message where tg_state=0 and tg_touser='{$_COOKIE['username']}'");
if (empty($_message['count'])){
    $_message_html='<strong><a href="member_message.php">(0)</a></strong>';
}else {
    $_message_html='<strong><a href="member_message.php">('.$_message['count'].')</a></strong>';
}



// 读取系统表tg_system 数据
if (!!$_rows=_fetch_array("select
                                    tg_webname,
                                    tg_article,
                                    tg_blog,
                                    tg_photo,
                                    tg_skin,
                                    tg_string,
                                    tg_post,
                                    tg_re,
                                    tg_code,
                                    tg_register
                            from    tg_system
                            where   tg_id=1"
    )){
        
        global $_system;
        $_system=array();
        $_system['webname']=$_rows['tg_webname'];
        $_system['article']=$_rows['tg_article'];
        $_system['blog']=$_rows['tg_blog'];
        $_system['photo']=$_rows['tg_photo'];
        $_system['skin']=$_rows['tg_skin'];
        $_system['string']=$_rows['tg_string'];
        $_system['post']=$_rows['tg_post'];
        $_system['re']=$_rows['tg_re'];
        $_system['code']=$_rows['tg_code'];
        $_system['register']=$_rows['tg_register'];
        
        //如果有skin的cookie，那么久替代系统数据库的皮肤
        if ($_COOKIE['skin']){
            $_system['skin']=$_COOKIE['skin'];
        }
        
        
}else {
    exit('系统错误，请联系管理员');
}


//验证码目前只是做了登陆页和注册页。其他页面类推。一个是html部分，一个是验证码校验部分都得设置非空才执行
// （是否显示，都可以用if是否空）


?>
