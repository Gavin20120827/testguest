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
define("scrirp", "face");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';   

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
	<div id="face">
		<h3>选择头像</h3>
		<dl>
		
<!-- foreach函数。等同for，一般尽量不用for -->

		<?php foreach (range(1, 9) as $number){?>
		<dd><img src="face/m0<?php echo $number?>.png" alt="<?php echo $number?>" onclick="alert(this.src)"/></dd>
		<?php }?>
		
		<?php foreach (range(10, 12) as $number){?>
		<dd><img src="face/m<?php echo $number?>.png" alt="头像<?php echo $number?>" onclick="alert(this.src)"/></dd>
		<?php }?>
			
		
		</dl>
	</div>



</body>
</html>
