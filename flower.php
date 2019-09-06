<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月4日/下午7:08:33
* 修改时间：2019年6月4日下午7:08:33
* 修改备注：   
* 版本：
*/

session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "flower");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';

//判断是否登陆
if (!isset($_COOKIE['username'])){
    _alert_close("请先登陆");
}


//送花
if (@$_GET['action']=='give'){
    //     为了防止恶意注册和跨站攻击.函数在global文件中
    _check_code($_POST['code'], $_SESSION['code']);
    
    
    //引入验证文件,在表达式中，用include最合适
    include root_path.'/includes/register.func.php';
    
    $_clean=array();
    $_clean['touser']=$_POST['touser'];
    $_clean['fromuser']=$_COOKIE['username'];
    $_clean['flower']=$_POST['flower'];
    $_clean['content']=_check_connent($_POST['content'],10,200);
    
    //不能给自己送花
    if ($_clean['touser']==$_clean['fromuser']){
        _alert_close("请不要给自己送花");
    }
    

    //添加好友信息到数据库
    _query("insert into tg_flower(
                                    tg_touser,
                                    tg_fromuser,
                                    tg_content,
                                    tg_flower,     
                                    tg_date
                                    )
                            values (
                                    '{$_clean['touser']}',
                                    '{$_clean['fromuser']}',
                                    '{$_clean['content']}',
                                    '{$_clean['flower']}',
                                    NOW()
                                    )
            ");
    
    global $_conn;
    if (_affected_rows()==1){
        //关闭数据库
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        _alert_close("送花成功");
    }else {
        mysqli_close($_conn);
//         //清除session
//         _session_destroy();
        _alert_back("送花失败");
    }
  }

 


//获取数据
if (isset($_GET['id'])){
    //$_get[id],如果没有用单引号，也是可以的，默认即使数字。
      if (!! $_rows = _fetch_array("select 
                                        tg_username 
                                    from tg_user 
                                    where dg_id='{$_GET['id']}'")){      
            $_html = array();
           $_html['touser'] = $_rows['tg_username'];       
      }else {
          _alert_close("不存在此用户");
          }
}else {
    _alert_close("非法操作");
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


<div id="message">
	<h3>送花</h3>
	  <form method = "post" action="flower.php?action=give">
    	 <input type="hidden" name="touser" value="<?php echo $_html['touser']?>"/>
    	<dl>
    		<dd>
        		<input type = 'text' readonly="readonly" value ='TO :<?php echo $_html['touser'] ?>' class= 'text'/>
        		<select name="flower">
        			<?php 
        			foreach (range(1, 100) as $_num){
        			    echo '<option value="'.$_num.'">'.$_num.'朵</option>';
        			}
        			    ?>
        		</select>
    		</dd>
    		<dd><textarea name= 'content'>非常喜欢你，送你花吧！</textarea></dd>
    		<dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
    	    <dd><input type="submit" name="submit" value="送花" class="submit"/></dd>
    	</dl>
    </form>
</div>


</body>
</html>