<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月2日/上午12:59:31
* 修改时间：2019年6月2日上午12:59:31
* 修改备注：   
* 版本：
*/

session_start();

//定义常量授权调用
define("in_tg", true);

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


//退出
_unsetcookies();

?>