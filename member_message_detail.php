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
define("scrirp", "member_message_detail");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

//判断是否登陆
if (!isset($_COOKIE['username'])){
    _alert_close("请先登陆");
}

//删除短信
if (@$_GET['action']=='delete'&&isset($_GET['id'])){
    //两个感叹号表示转换为布尔值.判断是否可以从数据库中找到这个数据
    if(!!$_rows = _fetch_array("select
                                    tg_id
                            from
                                    tg_message
                            where
                                    tg_id = '{$_GET['id']}'")){
     
      //删除单短信
      _query("delete from tg_message where tg_id={$_GET['id']}");

      global $_conn;
      //判断是否修改成功
      if (_affected_rows()==1){
          //关闭数据库
          mysqli_close($_conn);
//           //清除session
//           _session_destroy();
          // 注册成功，跳转到首页.函数在公共函数库
          _location("删除成功","member_message.php");
      }else {
          mysqli_close($_conn);
//           //清除session
//           _session_destroy();
          _alert_back("短信删除失败");
      }
                 
    }else {
        _alert_back('此短信不存在');
    }

}


//获取数据
if (isset($_GET['id'])){

    $_rows = _fetch_array("select
                                tg_id,tg_fromuser,tg_content,tg_date,tg_state
                        from
                                tg_message
                        where
                                tg_id = '{$_GET['id']}'");

    if ($_rows){
        
        //将状态设置为1
        if (empty($_rows['tg_state'])){
            _query("update tg_message set tg_state=1 where tg_id= '{$_GET['id']}'");
            
            global $_conn;
            //判断是否修改成功
            if (!_affected_rows()==1){
                _alert_back("状态修改失败");
            }
        }
        
        $_html = array();
        $_html['id'] =$_rows['tg_id'];
        $_html['fromuser'] =$_rows['tg_fromuser'];
        $_html['content'] =$_rows['tg_content'];
        $_html['date'] =$_rows['tg_date'];
        
    }else {
        _alert_back('此用户不存在');
    }

}else{
    _alert_back('非法登陆');
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

<script type="text/javascript" src="js/member_message_detail.js"></script>

</head>
<body>

<?php 
require root_path.'/includes/header.inc.php';
   
?>


<div id = "member">
    
    <?php 
    require root_path.'/includes/member.inc.php';
    ?>
    

    <div id = "member_message_detail">
    	<h2> 短信详情</h2>
    	<dl>
    		<dd>发信 人：<?php echo $_html['fromuser']?></dd>
    		<dd>内   容：<strong><?php echo $_html['content']?></strong></dd>
        	<dd>发信时间：<?php echo $_html['date']?></dd>
        	<dd class= "button"><input type="button" value="返回列表" id="return"/><input type="button" value=" 删除短信" id="delete" name="<?php echo $_html['id']?>"/></dd>
    	</dl>

 
	</div>

</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>

</body>
</html>