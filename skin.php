<?php

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "friend");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

$_skinurl= $_SERVER['HTTP_REFERER'];
//必须通过上一页点击过来，而且必须要有id
if (empty($_skinurl) || !isset($_GET['id'])){
    _alert_back('非法操作');
}else {

//生成一个cookie，用来保存上一页的皮肤
setcookie('skin',$_GET['id']);
//返回当前页面
_location(null, $_skinurl);

}

?>