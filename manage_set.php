<?php
/**
 * 项目名称：多用户留言系统
 * 创建人：zhangwei
 * 创建时间：2019年6月4日/上午9:11:00
 * 修改时间：2019年6月4日上午9:11:00
 * 修改备注：
 * 版本：
 */
session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "manage_set");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


//设置管理员才可以登陆此页面。头文件和login文件只是控制了是否显示后台入口连接。但是如果直接输入后台网址，还是可以直接进后台页面
_manage_login();

//修改系统表
if ($_GET['action']=='set'){
    //判断用户是否存在
    if (!!$_rows=_fetch_array("select tg_uniqid from tg_user where tg_username='{$_COOKIE[username]}'")){
        
        $_clean = array();
        $_clean["webname"]=$_POST["webname"];
        $_clean["article"]=$_POST["article"];
        $_clean["blog"]=$_POST["blog"];
        $_clean["photo"]=$_POST["photo"];
        $_clean["skin"]=$_POST["skin"];
        $_clean["string"]=$_POST["string"];
        $_clean["post"]=$_POST["post"];
        $_clean["re"]=$_POST["re"];
        $_clean["code"]=$_POST["code"];
        $_clean["register"]=$_POST["register"];
        
        _query("UPDATE tg_system SET
                                   tg_webname ='{$_clean["webname"]}',
                                    tg_article ='{$_clean["article"]}',
                                    tg_blog ='{$_clean["blog"]}',
                                    tg_photo ='{$_clean["photo"]}',
                                    tg_skin ='{$_clean["skin"]}',
                                    tg_string ='{$_clean["string"]}',
                                     tg_post ='{$_clean["post"]}',
                                    tg_re ='{$_clean["re"]}',
                                    tg_code ='{$_clean["code"]}',
                                    tg_register ='{$_clean["register"]}'
                            WHERE
                                    tg_id =1
                            ");
       
        global $_conn;
        //判断是否修改成功
        if (_affected_rows()==1){
            //关闭数据库
            mysqli_close($_conn);
            //         //清除session
            //         _session_destroy();
            //  修改成功，跳转回到本页.函数在公共函数库
            _location("恭喜你修改成功","manage_set.php");
        }else {
            mysqli_close($_conn);
            //         //清除session
            //         _session_destroy();
            _location("很遗憾,没有任何数据被修改","manage_set.php");
        }
        
        
    }else{
        _alert_back("异常");
    }

}



//读取系统设置表
if (!!$_rows=_fetch_array("select 
                                    tg_webname,
                                    tg_article,
                                    tg_blog,
                                    tg_photo,
                                    tg_skin,
                                    tg_string,
                                    tg_post,
                                    tg_re,
                                    tg_code,
                                    tg_register
                            from    tg_system 
                            where   tg_id=1"
    )){
    
    $_html=array();
    $_html['webname']=$_rows['tg_webname'];
    $_html['article']=$_rows['tg_article'];
    $_html['blog']=$_rows['tg_blog'];
    $_html['photo']=$_rows['tg_photo'];
    $_html['skin']=$_rows['tg_skin'];
    $_html['string']=$_rows['tg_string'];
    $_html['post']=$_rows['tg_post'];
    $_html['re']=$_rows['tg_re'];
    $_html['code']=$_rows['tg_code'];
    $_html['register']=$_rows['tg_register'];
    
    
    // 每页文章列表
    if ($_html['article']==10){
        $_html['article_html']='<select name="article"><option value="10" selected="selected">每页10篇</option><option value="15">每页15篇</option></select>';
    }elseif ($_html['article']==15){
        $_html['article_html']='<select name="article"><option value="10">每页10篇</option><option value="15" selected="selected">每页15篇</option></select>';
    }
    
    //每页博友列表
    if ($_html['blog']==10){
        $_html['blog_html']='<select name="blog"><option value="10" selected="selected">每页10人</option><option value="15">每页15人</option></select>';
    }elseif ($_html['blog']==15){
        $_html['blog_html']='<select name="blog"><option value="10">每页10人</option><option value="15" selected="selected">每页15人</option></select>';
    }
    
    
    //每页相册列表
    if ($_html['photo']==8){
        $_html['photo_html']='<select name="photo"><option value="8" selected="selected">每页8张</option><option value="12">每页12张</option></select>';
    }elseif ($_html['photo']==12){
        $_html['photo_html']='<select name="photo"><option value="8">每页8张</option><option value="12" selected="selected">每页12张</option></select>';
    }
    
    // 皮肤
    if ($_html['skin']==1){
        $_html['skin_html']='<select name="skin"><option value="1" selected="selected">1号皮肤</option><option value="2">2号皮肤</option><option value="3">3号皮肤</option></select>';
    }elseif ($_html['skin']==2){
        $_html['skin_html']='<select name="skin"><option value="1">1号皮肤</option><option value="2" selected="selected">2号皮肤</option><option value="3">3号皮肤</option></select>';
    }elseif ($_html['skin']==3){
        $_html['skin_html']='<select name="skin"><option value="1">1号皮肤</option><option value="2">2号皮肤</option><option value="3" selected="selected">3号皮肤</option></select>';
    }
    
    // 发帖限制
    if ($_html['post']==30){
        $_html['post_html']='<input type="radio" name="post" value="30" checked="checked"/>30秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="post" value="60"/>1分钟&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="post" value="180"/>3分钟';
    }elseif ($_html['post']==60){
        $_html['post_html']='<input type="radio" name="post" value="30"/>30秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="post" value="60" checked="checked"/>1分钟&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="post" value="180"/>3分钟';    
    }elseif ($_html['post']==180){
         $_html['post_html']='<input type="radio" name="post" value="30" />30秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="post" value="60"/>1分钟&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="post" value="180" checked="checked"/>3分钟';
    }
    
    // 回帖限制
    if ($_html['re']==15){
        $_html['re_html']='<input type="radio" name="re" value="15" checked="checked"/>15秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="re" value="30"/>30秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="re" value="45"/>45秒';
    }elseif ($_html['re']==30){
        $_html['re_html']='<input type="radio" name="re" value="15" />15秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="re" value="30" checked="checked"/>30秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="re" value="45"/>45秒';
    }elseif ($_html['re']==45){
        $_html['re_html']='<input type="radio" name="re" value="15" />15秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="re" value="30"/>30秒&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="re" value="45" checked="checked"/>45秒';
    }
    
     //验证码
    if ($_html['code']==1){
        $_html['code_html']='<input type="radio" name="code" value="1" checked="checked"/>启用&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="code" value="0"/>不启用';
    }else{
        $_html['code_html']='<input type="radio" name="code" value="1"/>启用&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="code" value="0" checked="checked"/>不启用';
    }
    
    //是否允许开放注册
    if ($_html['register']==1){
        $_html['register_html']='<input type="radio" name="register" value="1" checked="checked"/>允许&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="register" value="0"/>不允许';
    }else{
        $_html['register_html']='<input type="radio" name="register" value="1"/>允许&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" name="register" value="0" checked="checked"/>不允许';
        
    }
    
    
    
}else {
    _alert_back('系统错误，请联系管理员');
}



?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<!-- 调用公共css库中的文件 -->
<?php 
require root_path.'/includes/title.inc.php';
?>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
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


	<div id = "member_main">
		<h2> 后台管理中心</h2>
		<form method="post" action="?action=set">
		<dl>
			<dd>网站名称：<input type="text" name="webname" class="text" value="<?php echo $_html['webname']?>"/></dd>
			<dd>文章每页列表数：<?php echo $_html['article_html']?></dd>
			<dd>博客每页列表数：<?php echo $_html['blog_html']?></dd>
			<dd>相册每页列表数：<?php echo $_html['photo_html']?></dd>
			<dd>站点默认皮肤：<?php echo $_html['skin_html']?></dd>
			<dd>非法字符过滤：<input type="text" name="string" class="text" value="<?php echo $_html['string'] ?>"/>＊用英文下划线"|"隔开</dd>
            <dd>每次发帖限制：<?php echo $_html['post_html']?></dd>
            <dd>每次回帖限制：<?php echo $_html['re_html']?></dd>
			<dd>是否启用验证码：<?php echo $_html['code_html']?></dd>
			<dd>是否允许开放注册：<?php echo $_html['register_html']?></dd>
			<dd><input type="submit" value="修改系统设置" class="submit"/></dd>
		</dl>
		</form>
	</div>

</div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>