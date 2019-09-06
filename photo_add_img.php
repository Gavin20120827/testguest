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
define("scrirp", "photo_add_img");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

//必需会员才能登陆
if (!$_COOKIE['username']){
    _alert_back('必需会员才能登陆');
}


//保存图片，录入数据库
if ($_GET['action']=="addimg"){
    include 'includes/register.func.php';
    $_clean=array();
    $_clean['name']=_check_photo_url($_POST['name']);
    $_clean['url']=$_POST['url'];
    $_clean['content']=$_POST['content'];
    $_clean['sid']=$_POST['sid'];
    
    _query("insert into tg_photo (
                                        tg_name,
                                        tg_url,
                                        tg_content,
                                        tg_sid,
                                        tg_username,
                                        tg_date
                                  )
                            values(
                                      '{$_clean['name']}',     
                                      '{$_clean['url']}',  
                                      '{$_clean['content']}',  
                                      '{$_clean['sid']}' ,
                                      '{$_COOKIE['username']}',
                                      NOW()
                                )
            ");
  
    if (_affected_rows()==1){
        global $_conn;
        //关闭数据库
        mysqli_close($_conn);
        
        //  成功，跳回本页
        _location("图片添加成功",'photo_show.php?id='.$_clean['sid']);
    }else {
        mysqli_close($_conn);
        _location("图片添加失败！",'photo_show.php?id='.$_clean['sid']);
    }
    
}





//  此id是从photo_show中传过来,photo_show又是从photo中传过来，即相册的id。然后赋值给下边表单中的隐藏字段
if (isset($_GET['id'])){
    
    //只有dir表中有的id才可以访问（不同通过修改url访问不存在的相册）
    if (!!$_rows=_fetch_array("select tg_id, tg_dir from tg_dir where tg_id='{$_GET['id']}'
    
    ")){
    //把id取出来，然后传给图片上传的页面。确保上传到同一个相册
    $_html=array();
    $_html['id']=$_rows['tg_id'];
    $_html['dir']=$_rows['tg_dir'];
    
    }else {
        _alert_back('不存在此相册');
    }
    
}else {
    _alert_back('非法操作');
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

<script type="text/javascript" src="js/photo_add_img.js"></script>

</head>
<body>


<?php 
require root_path.'/includes/header.inc.php';
   
?>


<div  id='photo'>
	<h2>添加图片</h2>
		<form method="post" action="?action=addimg">
		
<!-- 		 这个就是相册的id -->
		<input type="hidden" name="sid" value="<?php echo $_html['id']?>"/>
	<dl>
		<dd>图片名称：<input type="text" name="name" class="text"/></dd>
    	<dd>图片地址：<input type="text" name="url" id="url" readonly="readonly" class="text"/><a href="javascript:;" title="<?php echo $_html['dir']?>" id="up">上传</a></dd>
    	<dd>图片描述：<textarea name="content"></textarea></dd>
    	<dd><input type="submit" value="添加图片" class="submit"/></dd>
	</dl>
		</form>
	
</div>


<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>