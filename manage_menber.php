<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月4日/下午11:31:43
* 修改时间：2019年6月4日下午11:31:43
* 修改备注：   
* 版本：
*/
session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "manage_menber");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

//设置管理员才可以登陆此页面。头文件和login文件只是控制了是否显示后台入口连接。但是如果直接输入后台网址，还是可以直接进后台页面
_manage_login();



//这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
global $_pagesize,$_pagenum;
//第一个参数获取总条数，第二个指定每页多少条。函数在global页面
_page("select dg_id from tg_user",15);

//读取用户表，筛选出所有的会员信息
$_result=_query("select
                        tg_username,
                        dg_id,
                        tg_reg_time,
                        tg_email
                from    
                        tg_user
             ORDER BY 
                        tg_reg_time DESC 
                 limit 
                        $_pagenum,$_pagesize
    
  ");
        
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- 调用公共css库中的文件 -->
<?php 
require root_path.'/includes/title.inc.php';
?>

<script type="text/javascript" src="js/ member_message.js"></script>

</head>
<body>

    <div id="header">
        <h1><a href="index.php" >系统后台管理</a></h1>
        <ul>
            <li><a href="index.php">返回首页</a></li>
        </ul>
    </div>


<div id = "member">
    
    <?php 
    require root_path.'/includes/manage.inc.php';
    ?>
    

     <div id = "member_message">
    	<h2>会员列表中心</h2>
        <form method="post" action="?action=delete">
    	 <table cellspacing="1" >
	 		<tr><th>会员ID</th><th>会员姓名</th><th>邮件地址</th><th>注册时间</th><th>操作</th></tr>
    	 
    	 <?php 
    	 $_html = array();
    	 while (!!$_rows =_fetch_array_list($_result)) {
    	     $_html['id'] = $_rows['dg_id'];
    	     $_html['username'] = $_rows['tg_username'];
    	     $_html['reg_time'] = $_rows['tg_reg_time'];
    	     $_html['email'] = $_rows['tg_email'];
   	 
    	 ?>
    	 
	    <tr><td><?php echo $_html['id']?></td><td><?php echo $_html['username']?></td><td><?php echo $_html['email']?></td><td><?php echo $_html['reg_time']?></td><td><input name="ids[]" value="<?php echo $_html['id']?>" type= "checkbox" /></td></tr>

		<?php }?>



<tr><td colspan="5" ><label for="all">全选<input type="checkbox" name="chkall" id="all"/></label><input type="submit"  value= "批量删除" /></td></tr>
    	 </table>
    	 </form>
<?php 	_paging(2);?>
	</div>

</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>

</body>
</html>