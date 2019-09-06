<?php


/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/下午3:00:46
* 修改时间：2019年5月28日下午3:00:46
* 修改备注：
* 版本：
*/
session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "register");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';  

// 登陆状态
_login_state();

//第一种判断表单是否提交（加上表单代码中的隐藏input）
// if ($_POST['action']=='register'){
//     echo '你提交了数据';
//     exit();
// }



//第二种判断表单是否提交的方式

if (@$_GET["action"]=="register"){
    
    //是否关闭了注册功能
    if (empty($_system['register'])){
        _alert_close("已经关闭注册");
    }
    
    // 是否启用验证码
    if (!empty($_system['code'])){ 
//     为了防止恶意注册和跨站攻击.函数在global文件中
    _check_code($_POST["code"], $_SESSION["code"]);
    }
    
    //引入验证文件,在表达式中，用include最合适
    include root_path.'/includes/register.func.php';
    

  
//     //接受数据，但是这种方法比较麻烦
//     $_username = $_POST["username"];
//     $_password = $_POST["password"];
//     $_notpassword = $_POST["notpassword"];

    //创建一个空数组，用来存放提交过来的合法数据,_check_username是自建公共函数
    $_clean = array();   
    
    //  通过唯一标示符提高安全性
    $_clean["uniqid"] = _check_uniqid($_POST["uniqid"],$_SESSION["uniqid"]);
    
    //也是唯一标识符，用来刚注册的用户进行激活处理，方可登陆
    $_clean["active"] = sha1(uniqid(rand(),true));
    $_clean["username"]=_check_username($_POST["username"],2,20);
    $_clean["password"]= _check_password($_POST["password"],$_POST["notpassword"],6);
    $_clean["question"]= _check_question($_POST["question"],4,20);
    $_clean["answer"]= _check_answer($_POST["question"],$_POST["answer"],4,20);
    $_clean["sex"]= $_POST["sex"];
    $_clean["email"]= _check_email($_POST["email"]);
    $_clean["qq"]=_check_qq($_POST["qq"]);
    $_clean["url"]=_check_qq($_POST["url"]);
    
    //新增之前判断用户名是否存在
    global $_conn;
    
    $_query= mysqli_query($_conn,"SELECT tg_username FROM tg_user WHERE tg_username='".$_clean['username']."'") ;
    if (mysqli_fetch_array($_query,MYSQLI_ASSOC)) {
        _alert_back("对不起，此用户已经被注册");}

    
    //     新增用户。变量或者数组的写法：｛'".$_clean['username']."'
 mysqli_query( $_conn , "INSERT INTO tg_user(
                                             tg_username,
                                             tg_password,
                                             tg_question,
                                             tg_answer,
                                             tg_uniqid,
                                             tg_active,
                                             tg_sex,
                                             tg_email,
                                             tg_qq,
                                             tg_url,
                                             tg_reg_time,
                                             tg_last_time,
                                             tg_last_ip

                                           ) 
                                    values( 
                                          '".$_clean['username']."' ,
                                          '".$_clean['password']."',
                                          '".$_clean['question']."' ,
                                          '".$_clean['answer']."',
                                          '".$_clean['uniqid']."' ,
                                          '".$_clean['active']."',
                                         '".$_clean['sex']."',
                                          '".$_clean['email']."',
                                          '".$_clean['qq']."',
                                          '".$_clean['url']."',
                                          NOW(),
                                          NOW(),
                                          '".$_SERVER["REMOTE_ADDR"]."'

                                           )"
    
            ) 
 or die("数据库存入失败".mysqli_error($_conn));
 
   if (_affected_rows()==1){
       
       //获取刚刚注册流程中产生的id，即刚注册的用户的自动生成的id
       $_clean['id']=mysqli_insert_id($_conn);
       
        //关闭数据库
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        
//         //生成xml文件
//         _set_xml('new.xml', $_clean);
        
        // 注册成功，跳转到首页.函数在公共函数库
       _location("恭喜你注册成功","login.php");     
    }else {
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        _location("很遗憾你注册失败","register.php");     
    }


}else {
    $_SESSION["uniqid"]=$_uniqid= sha1(uniqid(rand(),true));
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
<div id="register">
	<h2>会员注册</h2>
	<form method="post" action="register.php?action=register">
<!-- 		<input type="hidden" name="action" value="register"/> -->
			<input type="hidden" name="uniqid" value="<?php echo $_uniqid?>"/>
		<dl>
			<dt>请认真填写以下内容</dt>
			<dd>用户名：<input type="text" name="username" class="text2" />(＊必填，至少两位)</dd>
			<dd>密   码：<input type="password" name="password" class="text1" />(＊必填，至少六位)</dd>
			<dd>确认密码：<input type="password" name="notpassword" class="text" />(＊必填，同上)</dd>
			<dd>密码提示：<input type="text" name="question" class="text" /></dd>
			<dd>密码回答：<input type="text" name="answer" class="text" /></dd>
			<dd>性   别：<input type="radio" name="sex" value="男" checked="checked"/>男<input type="radio" name="sex" value="女"/>女</dd>
            <dd class="face"><img src="face/m01.png" alt="头像选择" onclick="javascript:window.open('face.php','face','width=400,height=400,top=0,left=0,scrollbars=1')" name="face"/></dd>
            <dd>电子邮件：<input type="text" name="email" class="text" /></dd>
            <dd>  Q Q ：<input type="text" name="qq" class="text3" /></dd>
            <dd>主页地址：<input type="text" name="url" class="text" value="http://" /></dd>
            
           <?php if (!empty($_system['code'])){ ?>
            <dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
           <?php }?>
           
            <dd><input type="submit" name="submit" value="注册" class="submit"/></dd>
		</dl>
	</form>
</div>


<?php 
require root_path.'/includes/footer.inc.php';	


?>


</body>
</html>