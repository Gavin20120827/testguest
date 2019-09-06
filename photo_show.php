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
define("scrirp", "photo_show");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


//删除照片
if ($_GET['action']=='del'&&isset($_GET['id'])){
    
    //取得这张图片的发布者
    if (!!$_rows=_fetch_array("select 
                                        tg_username,
                                        tg_id,
                                        tg_sid,
                                        tg_url
                                from
                                        tg_photo
                                where
                                        tg_id='{$_GET['id']}'
                                limit
                                        1
        ")){
        $_html=array();
        $_html['id']=$_rows['tg_id'];
        $_html['sid']=$_rows['tg_sid'];
        $_html['username']=$_rows['tg_username'];
        $_html['url']=$_rows['tg_url'];
        
        global $_conn;
        //判断剩余图片的身份是否合法。此处用html，会被下边替换掉，所以同一个变量没有影响
        if ($_html['username']==$_COOKIE['username']|| isset($_SESSION['admin']) ){
            
            //从数据库删除图片的信息
            _query("delete from tg_photo where tg_id='{$_GET['id']}'");
                
            if (_affected_rows()==1){
                
                //删除物理地址图片操作，先检查是否存在（是否已经删除）
                if (file_exists($_html['url'])){
                    //删除图片的函数。删除物理地址
                    unlink($_html['url']);
                }else {
                    _alert_back("磁盘里已经不存在此图");
                }
                
                //关闭数据库
                mysqli_close($_conn);
                _location("删除图片成功","photo_show.php?id=".$_html['sid']);
            }else {
                mysqli_close($_conn);
                _location("很遗憾删除图片失败","photo_show.php?id=".$_html['sid']);
            }
            
        }else {
            _alert_back('不存在此图片');
        }
        
    }else {
        _alert_back('非法操作1');
    }

}







//  此id是从photo中传过来，即相册的id.此操作用来获得id
if (isset($_GET['id'])){
    
    //只有dir表中有的id才可以访问（不同通过修改url访问不存在的相册）
  if (!!$_rows=_fetch_array("select tg_id, tg_name,tg_type,tg_password from tg_dir where tg_id='{$_GET['id']}'

")){
      //把id取出来，然后传给图片上传的页面。确保上传到同一个相册
      $_html=array();
      $_html['id']=$_rows['tg_id'];
      $_html['name']=$_rows['tg_name'];
      $_html['type']=$_rows['tg_type'];
      $_html['password']=$_rows['tg_password'];
      
      
      //判断输入的相册密码是否匹配
      if ($_POST['password']){
          if (!!$_rows=_fetch_array("select 
                                                tg_id 
                                            from tg_dir
                                             where tg_password='{$_POST['password']}'
          
          ")){
          setcookie('photo'.$_html['id'],$_html['name']);
          //生成coookie之后重定向，可以解决需要刷新才能进入的问题
          _location('', 'photo_show.php?id='.$_html['id']);
               
          }else {
              _alert_back('密码不正确');
          }
      }
      
  }else {
      _alert_back('不存在此相册');
  }
}else {
    _alert_back('非法操作');
}


//跟thumb。php一起的。首先是thumb生成一个图片。然后这里定义一个变量，然后传到下边的html，html再通过参数传到thumb，最后传回来本页面显示出来。
$_percent='0.5';

//这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
global $_pagesize,$_pagenum,$_system,$_id;
//第一个参数获取总条数，第二个指定每页多少条。函数在global页面
$_id='id='.$_html['id'].'&';
_page("select tg_id from tg_photo where tg_sid='{$_GET['id']}' ",$_system['photo']);

$_result= _query("SELECT
                         tg_id,tg_username,tg_name,tg_url,tg_commentcount,tg_readcount
                 FROM
                         tg_photo
                 where   tg_sid='{$_html['id']}'
                ORDER BY
                         tg_date DESC
                limit
                        $_pagenum ,$_pagesize");


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
	<h2><?php echo $_html['name']?>内容展示</h2>
	
	<?php  
	
	//加密相册。
	if (empty( $_html['type'])||$_COOKIE['photo'.$_html['id']]==$_html['name']||isset($_SESSION['admin'])){
	    
	$_html1 = array();
	while (!!$_rows =_fetch_array_list($_result)) {
	    $_html1['id'] = $_rows['tg_id'];
	    $_html1['username'] = $_rows['tg_username'];
	    $_html1['name'] = $_rows['tg_name'];
	    $_html1['url'] = $_rows['tg_url'];
	    $_html1['commentcount'] = $_rows['tg_commentcount'];
	    $_html1['readcount'] = $_rows['tg_readcount'];

	?>
		<dl>
			<dt><a href="photo_show_detail.php?id=<?php echo $_html1['id']?>"><img src="thumb.php?filename=<?php echo $_html1['url'] ?>&percent=<?php echo $_percent?>" alt="chaina" /></a></dt>
			<dd><a href="photo_show_detail?id=<?php echo $_html1['id']?>"><?php echo $_html1['name']?></a></dd>
			<dd>阅（<strong><?php echo  $_html1['readcount']?></strong>） 评（<strong><?php echo  $_html1['commentcount']?></strong>）</dd>
			<dd>上传者：<?php echo $_html1['username']?></dd>
			
		 <?php 
		 
		 if ($_html1['username']==$_COOKIE['username']|| isset($_SESSION['admin']) ){
		 
		 ?>

			<dd><a href="photo_show.php?action=del&id=<?php echo $_html1['id']?>">[删除]</a></dd>
			
		<?php  
	           }
		?>
			
		</dl>
		<?php  
	           }
		?>
	
	<p><a href="photo_add_img.php?id=<?php echo $_html['id']?>">添加图片</a></p>
	
	<?php 
	_paging(1);
	
	}else{
	    echo '<form  method="post" action="photo_show.php?id='.$_html['id'].'">';
	    echo '<p>请输入密码:<input type="password" name="password"/><input type="submit" value="确认" name="submit"/><p>';
        echo '</form>';
	}
	
	
	
	
	
	
	?>
	
</div>


<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>