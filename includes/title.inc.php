<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/下午7:00:34
* 修改时间：2019年5月28日下午7:00:34
* 修改备注：   
* 版本：
*/

// 防止恶意调用
if (!defined("in_tg")){
    exit("非法调用"); 
}
global $_system;

?>
<title ><?php echo $_system['webname']?></title>

<link rel="shortcut icon" href="images/icon/favicon.png"/>
<link rel="stylesheet" type="text/css" href="styles/<?php echo $_system['skin']?>/basic.css"/>
<link rel="stylesheet" type="text/css" href="styles/<?php echo $_system['skin']?>/<?php echo scrirp ?>.css"/>
