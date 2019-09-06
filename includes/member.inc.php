<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月4日/上午9:54:56
* 修改时间：2019年6月4日上午9:54:56
* 修改备注：   
* 版本：
*/
// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用");
    
}

?>

<div id = "member_sidebar">
	<h2> 中心导航</h2>
	<dl>
		<dt>账号管理</dt>
		<dd><a href = 'member.php'>个人信息</a></dd>
		<dd><a href = 'member_modify.php'>修改资料</a></dd>
		
		<dt>其他管理</dt>
		<dd><a href = 'member_message.php'>短信查询</a></dd>
		<dd><a href = 'member_friend.php'>好友设置</a></dd>
		<dd><a href = 'member_flower.php'>查询花朵</a></dd>
		<dd><a href = '####'>个人相册</a></dd>	
	</dl>
</div>

	
	
	
	
	