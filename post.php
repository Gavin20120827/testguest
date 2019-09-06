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
define("scrirp", "post");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';  


//判断是否登陆
if (!isset($_COOKIE['username'])){
    _location("请先登陆","login.php");
}


if (@$_GET['action']=='post'){
    //     为了防止恶意注册和跨站攻击.函数在global文件中
    _check_code($_POST['code'], $_SESSION['code']);
    
    global $_system,$_conn;
    //验证一下第二次发帖是否在规定的时间外
    _time(time(), $_COOKIE['post_time'], $_system['post']);
    
    
    //引入验证文件,在表达式中，用include最合适
    include root_path.'/includes/register.func.php';
    
    $_clean = array();
    
    $_clean["username"]= $_COOKIE["username"];
    $_clean["type"]= $_POST["type"];
    $_clean["title"]=_check_title($_POST["title"],2,20);
    $_clean["content"]=_check_content($_POST["content"],10);
    
    
    mysqli_query( $_conn , "INSERT INTO tg_article(
                                             tg_username,
                                             tg_type,
                                             tg_title,
                                             tg_content,
                                             tg_date
        
                                           )
                                    values(
                                          '".$_clean['username']."' ,
                                          '".$_clean['type']."',
                                          '".$_clean['title']."' ,
                                          '".$_clean['content']."',
                                          NOW()
                                           )"
        
        )
        or die("数据库存入失败".mysqli_error($_conn));
        
        if (_affected_rows()==1){
            
            //获取刚刚注册流程中产生的id，即刚注册的用户的自动生成的id
            $_clean['id']=mysqli_insert_id($_conn);
            
            // 保存发帖的时间在cookie之中
            setcookie('post_time',time());
            
            //关闭数据库
            mysqli_close($_conn);
//             //清除session
//             _session_destroy();
          
            // 帖子发表成功，跳转到article文件，为article文件传值
            _location("恭喜你发表帖子成功","article.php?id=".$_clean['id']);
        }else {
            mysqli_close($_conn);
//             //清除session
//             _session_destroy();
            _alert_back("很遗憾发表帖子失败");
        }

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


<script type="text/javascript" src="js/post.js"></script>

</head>

<body>

<?php 
require root_path.'/includes/header.inc.php';
?>
<div id="post">
	<h2>发表帖子</h2>
	<form method="post"  action="?action=post">
		<dl>
			<dt>请认真填写以下内容</dt>
			<dd>
			类型：
			<?php 
			// 空格
			echo '&nbsp;&nbsp; &nbsp;';
			     foreach (range(1, 16) as $_num){
			         if ($_num==1){
			             //labelfor 以及id组合，可以让点击图片就可以选中
			         echo '<label for="type'.$_num.'"><input type="radio" id="type'.$_num.'" name="type" value="'.$_num.'" checked="checked"/>';
	         }else {
	             echo '<label for="type'.$_num.'"><input type="radio" id="type'.$_num.'" name="type" value="'.$_num.'"/>';
	             
	         }
		         echo '&nbsp;<img src="images/m071.png "/></label>';
		         echo '&nbsp;&nbsp; &nbsp;';
		         
		         
                if ($_num==8){
                    echo '<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp';
                }
			     }
			?>
			
			</dd>
			<dd>标题：<input type="text" name="title" class="text2" />(＊必填，2位到20位)</dd>
			<dd>

				<textarea name="content" ></textarea>
			</dd>
			<dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
			<dd><input type="submit" name="submit" value="发表帖子" class="submit"/></dd>
		</dl>
	</form>
</div>


<?php 
require root_path.'/includes/footer.inc.php';	


?>


</body>
</html>