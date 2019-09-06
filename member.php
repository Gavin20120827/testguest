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
define("scrirp", "member");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';



//第一种方式调取管理中心数据。然后在下边div中ech打印出相应的值
// if (isset($_COOKIE['username'])){
//     //获取数据
//     $_rows = _fetch_array("select tg_username,tg_sex,tg_email,tg_qq,tg_url from tg_user where tg_username = '{$_COOKIE["username"]}'");
//     if ($_rows){
//         $_username = $_rows['tg_username'];
//         $_sex = $_rows['tg_sex'];
//         $_email = $_rows['tg_email'];
//         $_qq = $_rows['tg_qq'];
//         $_url = $_rows['tg_url'];
//     }else {
//         _alert_back('此用户不存在');
//     }
// }else{
//     _alert_back('非法登陆');
// }





// 第一种方式调取管理中心数据。然后在下边div中ech打印出相应的值
if (isset($_COOKIE['username'])){
    //获取数据
    $_rows = _fetch_array("select 
                                    tg_username,tg_sex,tg_email,tg_qq,tg_url,tg_reg_time,level 
                            from 
                                    tg_user 
                            where 
                                    tg_username = '{$_COOKIE["username"]}'");
       
    if ($_rows){
        $_html = array();
        $_html['username'] =htmlspecialchars($_rows['tg_username']);
        $_html['sex'] =htmlspecialchars ($_rows['tg_sex']);
        $_html['email']  = htmlspecialchars($_rows['tg_email']);
        $_html['url']  = htmlspecialchars($_rows['tg_url']);
        $_html['qq']  =htmlspecialchars($_rows['tg_qq']);
        $_html['reg_time']  =htmlspecialchars( $_rows['tg_reg_time']);
        switch ($_rows['level']){
            case 0:
                $_html['level'] = '普通会员';
                break;
            case 1:
                $_html['level'] = '管理员';
                break;
             default:
                $_html['level'] = '出错';    
        }
    }else {
        _alert_back('此用户不存在');
    }
}else{
    _alert_back('非法登陆');
}

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


<?php 
require root_path.'/includes/header.inc.php';
   
?>


<div id = "member">

<?php 
require root_path.'/includes/member.inc.php';
?>


	<div id = "member_main">
		<h2> 个人管理中心</h2>
		<dl>

<!-- 第一种方式 -->
<!-- 			<dd>用户名: echo $_username?></dd> -->
<!-- 			<dd>性  别： echo $_sex?></dd> -->
<!-- 			<dd>头  像：</dd> -->
<!-- 			<dd>电子邮件：php echo $_email?> </dd> -->
<!-- 			<dd>主  页：php echo $_url?></dd> -->
<!-- 			<dd>Q   Q： php echo $_qq?></dd> -->
<!-- 			<dd>注册时间：2019.5.5</dd> -->
<!-- 			<dd>身  份：管理员</dd>		 -->
		
<!-- 第二种方式 -->
			<dd>用户名:<?php echo $_html['username']?></dd>
			<dd>性  别： <?php echo $_html['sex']?></dd>
			<dd>头  像：</dd>
			<dd>电子邮件：<?php echo $_html['email']?> </dd>
			<dd>主  页：<?php echo $_html['url']?></dd>
			<dd>Q   Q： <?php echo $_html['qq']?></dd>
			<dd>注册时间：<?php echo $_html['reg_time']?></dd>
			<dd>身  份：<?php echo  $_html['level'] ?></dd>

		</dl>
	</div>

</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>