<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月1日/上午12:56:17
* 修改时间：2019年6月1日上午12:56:17
* 修改备注：   
* 版本：
*/

session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "login");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

// 登陆状态
_login_state();

//开始处理登陆
if (@$_GET["action"]=="login"){

    if (!empty($_system['code'])){ 
  // 为了防止恶意注册和跨站攻击.函数在global文件中
    _check_code($_POST["code"], $_SESSION["code"]);
    }

    //引入验证文件,在表达式中，用include最合适
    include root_path.'/includes/login.func.php';
        
        
    $_clean = array();   
    $_clean["username"]=_check_username($_POST["username"],2,20);
    $_clean["password"]= _check_password($_POST["password"],6);
    $_clean["time"]= $_POST["time"];
    
    global $_conn;
    //到数据库验证
    $_query= mysqli_query($_conn,"SELECT*FROM tg_user WHERE tg_username='".$_clean['username']."' and tg_password='".$_clean['password']."'") ;
    if (!!$_rows= mysqli_fetch_array($_query,MYSQLI_ASSOC)) {
       
       // 登陆成功后，记录登陆信息
       _query("update tg_user set tg_last_time = NOW(), 
                                 tg_last_ip = '{$_SERVER["REMOTE_ADDR"]}',
                                 login_count = login_count+1
                         where 
                                 tg_username = '{$_rows['tg_username']}'       

                ");
            
       
       _setcookies($_clean["username"], $_clean["time"]);
       
       if ($_rows['level']==1){
           $_SESSION['admin']=$_clean["username"];
       }
       
        //关闭数据库
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        //跳转不弹窗
        _location('',"index.php");
        
        
    }else {
        //关闭数据库
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        //跳转
        _location('账号或者密码不正确，请重新登陆',"login.php");
        
        
        exit("用户名或者密码不正确");
    }
    
    //关闭数据库
    mysqli_close($_conn);
    
    

 
} 

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- 调用css公共库文件，不用每个页面都写<link ref> 这个函数 -->
<?php 
require root_path.'/includes/title.inc.php';
?>

</head>

<body>

<?php 
require root_path.'/includes/header.inc.php';
?>

<div id="login">
	<h2> 用户登陆</h2>
	<form method="post" name="login" action="login.php?action=login">
		<dl>

    		<dd>用户名：<input type="text" name="username" class="text1" /></dd>
    		<dd>密   码：<input type="password" name="password" class="text2" /></dd>
    		<dd>保   留：<input type="radio" name="time" value="0" checked="checked"/>不保留<input type="radio" name="time" value="1" />一天<input type="radio" name="time" value="2" />一周</dd>
    		
    		<?php if (!empty($_system['code'])){ ?>
    		<dd>验 证 码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
    		<?php }?>
    		
    		<dd><input type="submit" name="submit" value="登陆" class="submit"/><input type="button" name="btn" value="注册" id="location" class="btn"/></dd>
    	</dl>
    </form>
</div>



<?php 
require root_path.'/includes/footer.inc.php';	


?>


</body>
</html>