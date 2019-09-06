<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月31日/下午6:41:11
* 修改时间：2019年5月31日下午6:41:11
* 修改备注：   
* 版本：
*/



// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用");
}

// 数据库连接定义常量
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PWD','QyOcNnQCp59j1nNt');
define('DB_NAME','test');


// // 原来的数据库连接的写法
// //创建数据库连接
// $_conn = mysqli_connect(DB_HOST,DB_USER,DB_PWD) or die('数据库连接失败');

// //选择一款数据库
// mysqli_select_db($_conn,DB_NAME) or die("指定的数据库不存在");

// // 选择字符集
// mysqli_query($_conn, "SET NAMES UTF8") or die("字符集错误");



function _connect(){
    global $_conn ;
    if (!$_conn=mysqli_connect(DB_HOST,DB_USER,DB_PWD)){
        exit("数据库连接失败");
    }
}

function _select_db(){
    global$_conn;
    if (!mysqli_select_db($_conn,DB_NAME)){
        exit("找不到指定的数据库");
    }
}

function _set_name(){
    global $_conn;
    if (!mysqli_query($_conn, "SET NAMES UTF8"))
        exit("字符集错误");
        
}


function _query($_sql){
    global $_conn;
    if (!$_result = mysqli_query($_conn, $_sql)) {
        exit("数据库执行失败");
    }
    return $_result;
}

//只能获取指定数据组的一条数据组
function _fetch_array($_sql){
    return mysqli_fetch_array(_query($_sql));
}

//可以返回指定数据集的所有数据
function _fetch_array_list($_result){
    return mysqli_fetch_array($_result,MYSQLI_ASSOC);
}


//表示影响到的记录数
function _affected_rows(){
    global $_conn;
    return mysqli_affected_rows($_conn);
}

?>
