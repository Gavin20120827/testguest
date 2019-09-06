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
define("scrirp", "blog");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


//这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
global $_pagesize,$_pagenum,$_system;
//第一个参数获取总条数，第二个指定每页多少条。函数在global页面
_page("select dg_id from tg_user",$_system['blog']);

//从数据库提取数据
// $_result= mysqli_query($_conn, 'select tg_username,tg_sex from tg_user order by tg_reg_time desc limit 0,'.$_pagesize);
// sqli语句插入变量，如果是int类型的就使用$result = mysql_query("insert into user(mycon) values(".$my_con.")");
// 或者$result = mysql_query('insert into user(mycon) values('.$my_con.')');
// 如果是字符型的就要使用单引号或者双引号括起来$result = mysql_query("insert into user(mycon) values('".$my_con."')");
// 或者这样写$result = mysql_query('insert into user(mycon) values("'.$my_con.'")');
$_result= _query('SELECT 
                        dg_id,tg_username,tg_sex 
                 FROM 
                        tg_user 
                ORDER BY 
                        tg_reg_time DESC 
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


<div  id='blog'>
	<h2>博友列表</h2>
<!-- 第一种做法 while (!!_rows = mysqli_fetch_array($_result)) -->
	
	<?php 
	   $_html = array();
	while (!!$_rows =_fetch_array_list($_result)) {
	    $_html['id'] = $_rows['dg_id'];
	    ?>
	<dl>     
		<dd class='user'><?php echo $_rows['tg_username']?></dd>
		<dt><img src="face/m01.png" alt="炎日" /></dt>
		<dd class='message'><a href ="javascript:;"  name = "message" onclick = "javascript:window.open('message.php?id=<?php echo $_html['id']  //ID＝ 等号后边即使有一个空格都不行?>','message','width=700px,height=400px')">发消息</a></dd>
    	<dd class='friend'><a href ="javascript:;"  name = "friend" onclick = "javascript:window.open('friend.php?id=<?php echo $_html['id']  //ID＝ 等号后边即使有一个空格都不行?>','friend','width=700px,height=400px')">加为好友</a></dd>
    	<dd class='guest'>写留言</dd>
    	<dd class='flower'><a href ="javascript:;"  name = "flower" onclick = "javascript:window.open('flower.php?id=<?php echo $_html['id']  //ID＝ 等号后边即使有一个空格都不行?>','flower','width=700px,height=400px')">给他送花</a></dd>
	</dl>
	<?php }
	
	
	_paging(2);
	
	?>
	
</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>