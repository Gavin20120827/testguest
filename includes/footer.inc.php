<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/上午12:30:10
* 修改时间：2019年5月28日上午12:30:10
* 修改备注：   
* 版本：
*/

// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用");
    
}


$_end_time = _runtime();

global $_conn;
//关闭数据库
mysqli_close($_conn);

?>


<div id="footer">
	<p>本程序执行耗时为：<?php echo round(_runtime()-_start_time,4) ?>秒</p>
    <p>版权所有 翻版必究</p>
    <p>本程序张先生提供</p>
</div>
