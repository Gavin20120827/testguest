<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月2日/上午1:18:48
* 修改时间：2019年6月2日上午1:18:48
* 修改备注：   
* 版本：
*/

session_start();
//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "photo_add_dir");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

//必需管理员才能登陆
_manage_login();

// 修改相册目录
if ($_GET['action']=='modify'){
    
    $_clean = array();
    $_clean["id"]=$_POST["id"];
    $_clean["name"]=$_POST["name"];
    $_clean["type"]=$_POST["type"];
    $_clean["content"]=$_POST["content"];
    $_clean["password"]=$_POST["password"];
    $_clean["face"]=$_POST["face"];
    
    
    if (empty($_clean['type'])){
        _query("update tg_dir set
                                        tg_name='{$_clean[name]}',
                                        tg_type='{$_clean[type]}',
                                        tg_password='null',
                                        tg_face='{$_clean[face]}',
                                        tg_content='{$_clean[content]}'
                                where
                                        tg_id='{$_clean["id"]}'
");
    }else {
            _query("update tg_dir set
                tg_name='{$_clean[name]}',
                tg_type='{$_clean[type]}',
                tg_password='{$_clean[password]}',
                tg_face='{$_clean[face]}',
                tg_content='{$_clean[content]}'
                where
                tg_id='{$_clean["id"]}'
                ");
        
    }

    global $_conn;
    if (_affected_rows()==1){
        //关闭数据库
        mysqli_close($_conn);
        _location("目录修改成功","photo.php");
    }else {
        mysqli_close($_conn);
        _alert_back("目录修改失败");
    }

}
    


// 读出从photo.php中传过来的id
if (isset($_GET['id'])){
    if (!! $_rows = _fetch_array("select tg_id,tg_name,tg_type,tg_face,tg_content
                                    from    tg_dir
                                    where    tg_id='{$_GET['id']}'"))
    {
        
        $_html = array();
        $_html['id'] = $_rows['tg_id'];
        $_html['name'] = $_rows['tg_name'];
        $_html['type'] = $_rows['tg_type'];
        $_html['face'] = $_rows['tg_face'];
        $_html['content'] = $_rows['tg_content'];
        
        if ($_html['type']==0){
            $_html['type_html']='checked="checked"';
        }elseif ($_html['type']==1){
            $_html['type_html1']='checked="checked"';
        }
 
    
    }else {
        _alert_back("不存在此目录");
    }

}else {
    _alert_back("非法操作");
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

<script type="text/javascript" src="js/photo_add_dir.js"></script>

</head>
<body>


<?php 
require root_path.'/includes/header.inc.php';
   
?>




<div  id='photo'>
	<h2>修改相册目录</h2>
		<form method="post" action="?action=modify">
	<dl>
		<dd>相册名称：<input type="text" name="name" class="text" value="<?php echo $_html['name']?>"/></dd>
    	<dd>相册类型：<input type="radio" name="type" value="0" <?php echo $_html['type_html']?>/>公开<input type="radio" name="type" value="1" <?php echo $_html['type_html1']?>/>私密</dd>
    	<dd>相册封面：<input type="text" name="face" class="text" value="<?php echo $_html['face']?>"/></dd>
    	<dd id="pass" <?php if ($_html['type']==1) echo 'style="display:block"'?>>相册密码：<input type="password" name="password" class="text"/></dd>
    	<dd>相册描述：<textarea name="content"><?php echo $_html['content']?></textarea></dd>
    	<dd><input type="submit" value="修改目录" class="submit"/></dd>
	</dl>
	     <input type="hidden" value="<?php echo $_html['id']?>" name="id"/>
		</form>
</div>


<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>