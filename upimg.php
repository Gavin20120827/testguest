<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/下午7:24:57
* 修改时间：2019年5月28日下午7:24:57
* 修改备注：1、
* 版本：
*/

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "upimg");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';   

//必需会员才能登陆
if (!$_COOKIE['username']){
    _alert_back('必需会员才能登陆');
}

//上传图片
if ($_GET['action']=="up"){
   
    //设置图片类型
    $_files=array('image/jpeg','image/pjpeg','image/png','image/x-png','image/gif');
    
    //判断上传的是否数组中的类型
     if (is_array($_FILES['userfile']['type'],$_files)){
         if (!in_array($_FILES['userfile']['type'], $_files)){
             _alert_back("上传的图片必须是jpg、png、gif中的一种");
         }
     }
     
    //判断文件如果出错的错误类型
     if ($_FILES['userfile']['error']>0){
         switch ($_FILES['userfile']['error']){
             case 1:echo "<script>alert('上传文件超过约定值1');history.back();</script>" ;
             break;
             case 2:echo "<script>alert('上传文件超过约定值2');history.back();</script>" ;
             break;
             case 3:echo "<script>alert('部分被上传');history.back();</script>" ;
             break;
             case 4:echo "<script>alert('没有任何文件被上传');history.back();</script>" ;
             break;
         }
         exit;
     }
    
     //判断配置的大小
     if ($_FILES['userfile']['size']>1000000){
         _alert_back('上传的文件不能超过1M');
     }
     
     // 获取文件的扩展名
      $_n=explode('.',$_FILES['userfile']['name']);
      $_name=$_POST['dir'].'/'.time().'.'.$_n[1];
     
     //移动文件
     if (is_uploaded_file($_FILES['userfile']['tmp_name'])){
         //这种办法，第一，写死了上传的目录；如果直接以userfile-name命名的话，上传同一张照片会被覆盖掉
//          if(!@move_uploaded_file($_FILES['userfile']['tmp_name'], 'photo\1560518807/'.$_FILES['userfile']['name'])){
         if (!@move_uploaded_file($_FILES['userfile']['tmp_name'],$_name)){
         
         _alert_back("移动失败");
         }else {
         //  _alert_close('上传成功');
echo "<script>alert('上传成功');window.opener.document.getElementById('url').value='$_name';window.close();</script>";
        exit();
         }
         
     }else {
         _alert_back('上传临时文件不存在');
     }

}

// 接收从js photo_add_img中传过来的dir的值。通过下边的表单，再次返回到上边。
if (!isset($_GET['dir'])){
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

</head>

<body>
	<div id="upimg" style="padding:20px;">
		<form enctype="multipart/form-data" action="upimg.php?action=up" method="post">

<!--        利用隐藏字段，把一些值再传回表单 -->
		<input type="hidden" name="dir" value="<?php echo $_GET['dir']?>"/>
		<input type="hidden" name="MAX_FILE_SIZE" value="1000000"/>
		选择图片：<input style="width:150px" type="file" name="userfile"/>
		<input type="submit" name="send" value="上传"/>
		</form>
	</div>
	
	
	
	
	
	
</body>
</html>
