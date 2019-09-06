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
define("scrirp", "photo");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


// 删除目录
if ($_GET['action']=='del'&&isset($_GET['id'])){
    global $_conn;
    //取得这张图片的发布者
    if (!!$_rows=_fetch_array("select
                                            tg_dir
                                    from
                                            tg_dir
                                    where
                                            tg_id='{$_GET['id']}'
                                    limit
                                            1
        ")){
        $_html=array();
        $_html['dir']=$_rows['tg_dir'];
        
        //删除磁盘目录.先验证是否能够删除目录，然后再进行删除目录中的图片的操作
        if (file_exists($_html['dir'])){
            //删除目录的函数。删除物理地址.rmdir只能删除非空目录，而且需要有相应权限
            if ( rmdir($_html['url'])){

                //1、删除该目录下的所有数据库图片
                _query("delete from tg_photo where tg_sid='{$_GET['id']}'");
                
                //2、删除数据库目录
                _query("delete from tg_dir where tg_id='{$_GET['id']}'");
                
                mysqli_close($_conn);
                _location('目录删除成功');
            }else {
                mysqli_close($_conn);
                _alert_back("目录删除失败");
            }
            
        }else {
            _alert_back("磁盘里已经不存在此目录");
        }
        
    }else {
        _alert_back('不存在此目录');
    }
}




//这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
global $_pagesize,$_pagenum,$_system;
//第一个参数获取总条数，第二个指定每页多少条。函数在global页面
_page("select tg_id from tg_dir",$_system['photo']);

$_result= _query('SELECT
                        tg_id,tg_name,tg_type,tg_face
                 FROM
                        tg_dir
                ORDER BY
                        tg_date DESC
                limit
                        '.$_pagenum.','.$_pagesize.'');

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


<div  id='photo'>
	<h2>相册列表</h2>
	
	<?php 
	   $_html = array();
	while (!!$_rows =_fetch_array_list($_result)) {
	    $_html['id'] = $_rows['tg_id'];
	    $_html['name'] = $_rows['tg_name'];
	    $_html['type'] = $_rows['tg_type'];
	    $_html['face'] = $_rows['tg_face'];
	    
	    if (empty($_html['type'])){
	        $_html['type_html']='(公开)';
	    } else {
	        $_html['type_html']='(私密)';
	    }
	    
	    
	    if (empty($_html['face'])){
	        $_html['face_html']='';
	    } else {
	        $_html['face_html']='<img src="'.$_html['face'].'" alt="'.$_html['name'].'"/>';
	    }
	    
	    //统计照片数量
	    $_html[photo_count]=_fetch_array("select count(*) as count from tg_photo where tg_sid='{$_html['id']}'");
	    
	    
	    ?>
	    
	<dl>
		<dt><a href="photo_show.php?id=<?php echo $_html['id']?>"><?php echo $_html['face_html']?></a></dt>
		<dd><a href="photo_show.php?id=<?php echo $_html['id']?>"><?php echo $_html['name']?><?php echo $_html['type_html']?></a></dd>
		<dd>一共<?php echo  $_html[photo_count]['count']?> 张</dd>
		<?php if (isset($_COOKIE['username'])&&isset($_SESSION['admin'])){?>
		<dd><a href="photo_dir_modify.php?id=<?php echo $_html['id']?>">[修改]</a><a href="photo.php?action=del&id=<?php echo $_html['id']?>">[删除]</a></dd>
		<?php }?>
	</dl>
	
	<?php }
	
	?>

	
<!-- 	管理员才可以上传相册 -->
	<?php 
	if (isset($_SESSION['admin'])&&isset($_COOKIE['username'])){

	?>
	<p><a href="photo_add_dir.php">添加目录</a></p>
	<?php } 
	_paging(2);
	
	?>
	
</div>


<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>