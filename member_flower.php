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
define("scrirp", "member_message");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

//批量删除花朵
if (@$_GET['action']=='delete'&&isset($_POST['ids'])){
    $_clean=array();
    $_clean['ids']= implode(',',$_POST['ids']);
    
    _query("delete from tg_flower where tg_id in({$_clean['ids']})");

    global $_conn;
    //判断是否修改成功.此处不能等于1了，因为多条数据就是多条。直接去掉数字，表示只要有就行
    if (_affected_rows()){
        //关闭数据库
        mysqli_close($_conn);
        
        // 删除成功，跳转到列表页.函数在公共函数库
        _location("花朵删除成功","member_flower.php");
    }else {
        mysqli_close($_conn);
        
        _location("很遗憾,没有任何花朵被删除","member_flower.php");
    }
}


//这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
global $_pagesize,$_pagenum;
//第一个参数获取总条数，第二个指定每页多少条。函数在global页面
//如果有数组变量或者普通变量，数字用单引号和点，字符串用单引号加双引号再加点，而且双引号必须在外边；前提是整个SQL语句用单引号
// 如果sql语句用了双引号，则数组变量用单引号加上花括号，普通变量什么都不加
_page('select tg_id from tg_flower where tg_touser="'.$_COOKIE['username'].'" ',20);
// _page("select tg_id from tg_message where tg_touser='{$_COOKIE['username']}'",5);

$_result= _query("SELECT
                        tg_flower,tg_id,tg_fromuser,tg_touser,tg_content,tg_date
                 FROM
                        tg_flower
                where tg_touser='{$_COOKIE['username']}'

                ORDER BY
                        tg_date DESC
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

<?php 
require root_path.'/includes/header.inc.php';
   
?>


<div id = "member">
    
    <?php 
    require root_path.'/includes/member.inc.php';
    ?>
    

    <div id = "member_message">
    	<h2>花朵管理中心</h2>
        <form method="post" action="?action=delete">
    	 <table cellspacing="1" >
	 		<tr><th>送花人</th><th>送花寄语</th><th>时间</th><th>送花数量</th><th>操作</th></tr>
    	 
 	<?php 
 	          $_html = array();
        while (!!$_rows =_fetch_array_list($_result)) {            
            $_html['id'] = $_rows['tg_id'];
            $_html['touser'] = $_rows['tg_touser'];
            $_html['fromuser'] = $_rows['tg_fromuser'];
            $_html['flower'] = $_rows['tg_flower'];
            $_html['content'] = $_rows['tg_content'];
            $_html['date'] = $_rows['tg_date'];
            @$_html['count'] += $_html['flower'];


	    ?>
	    <tr><td><?php echo $_html['fromuser']?></td><td title="<?php echo $_html['content']?>"><?php echo _title($_html['content']) ?></td>
	    <td><?php echo $_html['date']?></td><td><?php echo $_html['flower']?>朵</td><td><input name="ids[]" value="<?php echo $_html['id']?>" type="checkbox" /></td></tr>
	<?php
         }
	?>
        	<tr><td colspan="5" >共<?php echo $_html['count']?>朵花</td></tr>
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