<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/上午12:20:19
* 修改时间：2019年5月28日上午12:20:19
* 修改备注：   
* 版本：
*/
session_start();


// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用");
}


global $_message_html

?>
<script type="text/javascript" src="js/skin.js"></script>


<div id="header">

                                              <!-- 只需要写文件名就好了，不需要写具体的文件路径 -->
<h1><a href="index.php" >多用户留言系统</a></h1>
<ul>
<li><a href="index.php">首页</a></li>

<?php 

 if (isset($_COOKIE['username'])){
     //$_message_html 在common文件中
     echo '<li><a href="member.php">'.$_COOKIE['username'].'个人中心</a>'.$_message_html.'</li>';
     echo "\n";
 }else {
     echo '<li><a href="register.php">注册</a></li>';
     echo "\n";
     echo '<li><a href="login.php">登录</a></li>'; 
     echo "\n";
 }

?>
<li><a href="blog.php">博友</a></li>
<li><a href="photo.php">相册</a></li>
<li class="skin" onmouseover='inskin()' onmouseout='outskin()'>
    <a href="javascript:;">风格</a>
    <dl id="skin_switch">
<!--     与skin。php,以及common文件互动 -->
    	<dd><a href="skin.php?id=1">1号皮肤</a></dd>
    	<dd><a href="skin.php?id=2">2号皮肤</a></dd>
    	<dd><a href="skin.php?id=3">3号皮肤</a></dd>
    </dl>
</li>

<?php 
    if (isset($_COOKIE['username'])&&isset($_SESSION['admin'])){
        echo '<li><a href="manage.php">后台管理 <a></li>&nbsp;';
    }


    if (isset($_COOKIE['username'])){
        echo '<li><a href="logout.php">退出</a></li>';
    }

?>

</ul>
</div>




