<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月4日/上午9:11:00
* 修改时间：2019年6月4日上午9:11:00
* 修改备注：   
* 版本：
*/

//遇到sesion的时候，一定要记得开启这个
session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "member_modify");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';



if (@$_GET['action']=='modify'){
    //     为了防止恶意注册和跨站攻击.函数在global文件中
     _check_code($_POST['code'], $_SESSION['code']);
     
     //确保有数据的时候才执行下边
//    if (!!$_rows=_fetch_array("select tg_uniqid from tg_user where tg_username = '{$_COOKIE["username"]}'")){
//      为了防止cookie伪造，还要比对一下唯一标识符。第一种是直接的写法，第二种是函数写法

//      if ($_rows["tg_uniqid"] != $_COOKIE["uniqid"]){
//          _alert_back('唯一标识符异常');
//      }
//        _uniqid($_rows["tg_uniqid"], $_COOKIE["uniqid"]);
       
        //引入验证文件,在表达式中，用include最合适
        include root_path.'/includes/register.func.php';
        
        $_clean = array();
        $_clean["password"]= _check_modify_password($_POST["password"],6);
        $_clean["sex"]= $_POST["sex"];
        $_clean["email"]= _check_email($_POST["email"]);
        $_clean["qq"]=_check_qq($_POST["qq"]);
        $_clean["url"]=_check_qq($_POST["url"]);
        $_clean["switch"]=$_POST["switch"];
        $_clean["autograph"]=$_POST["autograph"];
        
        
        //修改资料
        if (empty($_clean["password"])){
            _query("UPDATE tg_user SET
                                        tg_sex = '{$_clean["sex"]}',
                                        tg_email = '{$_clean["email"]}',
                                        tg_qq = '{$_clean["qq"]}',
                                        tg_url = '{$_clean["url"]}',
                                        tg_switch='{$_clean["switch"]}',
                                        tg_autograph='{$_clean["autograph"]}'
                                WHERE
                                        tg_username = '{$_COOKIE["username"]}'
                                        ");
         
        }else {
            
            _query("UPDATE tg_user SET
                                    tg_password = '{$_clean["password"]}',
                                    tg_sex = '{$_clean["sex"]}',
                                    tg_email = '{$_clean["email"]}',
                                    tg_qq = '{$_clean["qq"]}',
                                    tg_url = '{$_clean["url"]}',
                                    tg_switch='{$_clean["switch"]}',
                                    tg_autograph='{$_clean["autograph"]}'
                            WHERE  
                                    tg_username = '{$_COOKIE["username"]}'
                                    ");
 }
    
    // 目前这里是有问题的，如果用户名相同的时候，就没有办法修改成功了。
    
    global $_conn;
    //判断是否修改成功
    if (_affected_rows()==1){
        //关闭数据库
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        // 注册成功，跳转到首页.函数在公共函数库
        _location("恭喜你修改成功","member.php");
    }else {
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        _location("很遗憾,没有任何数据被修改","member_modify.php");
    }
}


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
                                    tg_switch,tg_autograph,tg_username,tg_sex,tg_email,tg_qq,tg_url 
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
        $_html['switch']  =htmlspecialchars($_rows['tg_switch']);
        $_html['autograph']  =htmlspecialchars($_rows['tg_autograph']);
        
        //性别选择
        if ($_html['sex'] =='男'){
            $_html['sex_html'] = '<input type= "radio" name= "sex" value = "男" checked = "checked"/>男 <input type= "radio" name= "sex" value = "女"/>女';
        }elseif ($_html['sex'] =='女') {
            $_html['sex_html'] = '<input type= "radio" name= "sex" value = "女" checked = "checked"/>女 <input type= "radio" name= "sex" value = "男"/>男';
        }
        
        //签名开关
        if ($_html['switch']==1){
            $_html['switch_html']='<input type="radio" name="switch" value="1" checked="checked"/>启用&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="switch" value="0"/>禁用';
        }elseif ($_html['switch']==0) {
            $_html['switch_html']='<input type="radio" name="switch" value="1" />启用&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="switch" value="0" checked="checked"/>禁用';
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
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- 调用公共css库中的文件 -->
<?php 
require root_path.'/includes/title.inc.php';
?>
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
		<form method= "post" action="member_modify.php?action=modify">
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
			<dd>密  码:<input type = "password" class= "text" name = "password" /> 留空则不修改</dd>
			<dd>性  别：<?php echo $_html['sex_html']?> </dd>
			<dd>头  像：</dd>
			<dd>电子邮件：<input type = "text" class= "text" name = "email" value = "<?php echo $_html['email']?>"/> </dd>
			<dd>主  页：<input type = "text" class= "text" name = "url" value =" <?php echo $_html['url']?>"/></dd>
			<dd>Q   Q：<input type = "text" class= "text" name = "qq" value =" <?php echo $_html['qq']?>"/></dd>
			<dd>个性签名：<?php echo $_html['switch_html']?>
				<p><textarea name="autograph"><?php echo $_html['autograph']?></textarea></p>
			</dd>
			<dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
			<dd><input type="submit" name="submit" value="修改资料" class="submit"/></dd>
		</dl>
		</form>
	</div>

</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>