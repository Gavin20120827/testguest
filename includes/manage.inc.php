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
	<h2> 管理导航</h2>
	<dl>
		<dt>系统管理</dt>
		<dd><a href = 'manage.php'>后台首页</a></dd>
		<dd><a href = 'manage_set.php'>系统设置</a></dd>
	</dl>
	<dl>
		<dt>会员管理</dt>
		<dd><a href = 'manage_menber.php'>会员列表</a></dd>
		<dd><a href = 'manage_job.php'>职务设置</a></dd>
	</dl>
</div>

	
	
	
	
	