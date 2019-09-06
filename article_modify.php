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

//修改帖子，更新数据库。这是第二遍加载，当用户提交修改表单的时候
if (@$_GET['action']=='article_modify'){
    //为了防止恶意注册和跨站攻击.函数在global文件中
    _check_code($_POST['code'], $_SESSION['code']);
    
    //引入验证文件,在表达式中，用include最合适
    include root_path.'/includes/register.func.php';
           
        $_clean = array();
        $_clean["title"]=$_POST["title"];
        $_clean["content"]=$_POST["content"];
        $_clean["id"]=$_POST["id"];
        $_clean["type"]=$_POST["type"];
        
        global $_conn;
        _query("update tg_article set 
                                        tg_type='{$_clean['type']}' ,
                                        tg_title='{$_clean['title']}' ,
                                        tg_content='{$_clean['content']}',
                                        tg_last_modify_time=NOW()
                                    where tg_id='{$_clean['id']}'")
            
        or die("数据更新失败".mysqli_error($_conn));
        
        if (_affected_rows()==1){
            //关闭数据库
            mysqli_close($_conn);
//             //清除session
//             _session_destroy();
            // 帖子修改成功，跳转到article文件，为article文件传值
            _location("恭喜你修改帖子成功","article.php?id=".$_clean['id']);
        }else {
            mysqli_close($_conn);
//             //清除session
//             _session_destroy();
            _alert_back("很遗憾修改帖子失败");
        }
}






//读出主题帖，默认填写在表单中。id是article点击了修改帖子以后，a标签传过来的。
//中括号中不能有任何空格——一定要注意，不要随便给空格——语法错误：标点，大小写，空格，单词错误等
if (isset($_GET['id'])){
    //$_get[id],如果没有用单引号，也是可以的，默认即使数字。
    if (!! $_rows = _fetch_array("select
                                            tg_type,
                                            tg_username,
                                            tg_title,
                                            tg_content
                                    from     tg_article
                                            where tg_reid=0 and tg_id='{$_GET['id']}'")){
                
            $_html = array();
            $_html['id'] = $_GET['id'];
            $_html['username'] = $_rows['tg_username'];
            $_html['type'] = $_rows['tg_type'];
            $_html['title'] = $_rows['tg_title'];
            $_html['content'] = $_rows['tg_content'];
            
       //防止直接输入id，非自己发的帖子，也通过getid，进入了修改
        if ($_html['username']!=$_COOKIE['username']){
            _alert_back("你没有权限修改");
        }
                                            
                                        
        }else {
            _alert_close("不存在此帖子");
        }
    }else {
        _alert_close("非法操作");
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
	<h2>帖子修改</h2>
	<form method="post"  action="?action=article_modify">
		<input type="hidden" value="<?php echo $_html['id']?>" name="id"/>
		<dl>
			<dt>请认真修改以下内容</dt>
			<dd>
			类型：
			<?php 
			// 空格
			echo '&nbsp;&nbsp; &nbsp;';
			     foreach (range(1, 16) as $_num){
			         if ($_num==$_html['type']){
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
			<dd>标题：<input type="text" name="title" class="text2" value="<?php echo $_html['title'] ?>"/>(＊必填，2位到20位)</dd>
			<dd>

				<textarea name="content" ><?php echo $_html['content'] ?></textarea>
			</dd>
			<dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
			<dd><input type="submit" name="submit" value="修改帖子" class="submit"/></dd>
		</dl>
	</form>
</div>


<?php 
require root_path.'/includes/footer.inc.php';	


?>


</body>
</html>