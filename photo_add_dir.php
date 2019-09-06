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



//添加相册目录
if (@$_GET['action']=='adddir'){
    
    $_clean = array();
    $_clean["name"]= $_POST["name"];
    $_clean["type"]= $_POST["type"];
    $_clean["password"]=sha1($_POST["password"]);
    $_clean["content"]=$_POST["content"];
    $_clean["dir"]=time();
    $_clean["dir_too"]='photo/'.time();
    
        //创建一个目录。但是在自己的mac似乎不行
        // mkdir('photo',0777);
        //检查一个目录是否存在
        // echo is_dir('js');
        
    //检查一下主目录是否存在
    if (!is_dir('photo')){
        mkdir('photo',0777);
    }
    //再在主目录里边创建以时间戳为名字的自己的目录
    if (!is_dir('photo/'.$_clean["dir"])){
        mkdir('photo/'.$_clean["dir"]);
    }
    
    //把当前资料存进数据库
    if (empty($_clean['type'])){
        _query("INSERT INTO tg_dir(
                                             tg_name,
                                             tg_type,
                                             tg_content,
                                             tg_date,
                                             tg_dir
                                           )
                                    values(
                                          '{$_clean['name']}' ,
                                          '{$_clean['type']}',
                                          '{$_clean['content']}',
                                           NOW(),
                                           '{$_clean["dir_too"]}'
                                           )
    
    ");
    
    }else {
        _query("INSERT INTO tg_dir(
                                                tg_name,
                                                tg_type,
                                                tg_password,
                                                tg_content,
                                                tg_date,
                                                tg_dir
                                                )
                                        values(
                                                '{$_clean['name']}' ,
                                                '{$_clean['type']}',
                                                 '{$_clean['password']}',
                                                '{$_clean['content']}',
                                                NOW(),
                                                '{$_clean["dir_too"]}'
                                                )
          ");   
    }
    
    //目录添加成功
    global $_conn;
    if (_affected_rows()==1){
        //关闭数据库
        mysqli_close($_conn);
        _location("新增成功","photo.php");
    }else {
        mysqli_close($_conn);
        _alert_back("新增失败");
    }
    
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
	<h2>添加相册目录</h2>
		<form method="post" action="?action=adddir">
	<dl>
		<dd>相册名称：<input type="text" name="name" class="text"/></dd>
    	<dd>相册类型：<input type="radio" name="type" value="0"  checked="checked"/>公开<input type="radio" name="type" value="1" />私密</dd>
    	<dd id="pass">相册密码：<input type="password" name="password" class="text"/></dd>
    	<dd>相册描述：<textarea name="content"></textarea></dd>
    	<dd><input type="submit" value="添加目录" class="submit"/></dd>
	</dl>
		</form>
	
</div>


<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>