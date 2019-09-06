<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月4日/上午9:11:00
* 修改时间：2019年6月4日上午9:11:00
* 修改备注：   
* 版本：
*/
session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "manage");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


//设置管理员才可以登陆此页面。头文件和login文件只是控制了是否显示后台入口连接。但是如果直接输入后台网址，还是可以直接进后台页面
_manage_login();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- 调用公共css库中的文件 -->
<?php 
require root_path.'/includes/title.inc.php';
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
</head>
<body>


    <div id="header">
        <h1><a href="index.php" >系统后台管理</a></h1>
        <ul>
            <li><a href="index.php">返回首页</a></li>
        </ul>
    </div>




<div id = "member">

<?php 
require root_path.'/includes/manage.inc.php';
?>


	<div id = "member_main">
		<h2> 后台管理中心</h2>
		<dl>
			<dd>服务器主机名称：Linux debian 4.9.0-9</dd>
			<dd>服务器版本：Apache 2.0 Handler</dd>

		</dl>
	</div>

</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>