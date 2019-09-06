<?php 
/**
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月27日/下午11:56:20
* 修改时间：2019年5月27日下午11:56:20
* 修改备注：   
* @version: 1、将css文件打包，定义常量指定本页内容;2、选择头像
* 注意事项：
* 问题：1、magin,如果是auto，就是居中？
*/

session_start();

//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "index");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';   
       

//读取xml文件
$_html=_get_xml('new.xml');

//读取帖子列表
//这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
global $_pagesize,$_pagenum,$_system;
//第一个参数获取总条数，第二个指定每页多少条。函数在global页面.DESC 表示倒叙
_page("select tg_id from tg_article where tg_reid=0 ",$_system['article']);

$_result= _query('SELECT
                        tg_id,tg_title,tg_readcount,tg_commendcount
                 FROM
                        tg_article
                where tg_reid=0
                ORDER BY
                        tg_date DESC 
                limit 
                        '.$_pagenum.','.$_pagesize.'');



//最新图片，找到时间点最后上传的，并且是非公开的
//子查询
$_photo=_fetch_array("SELECT 
                               tg_id AS id,
                                 tg_name AS name,
                                tg_url AS url 
                                FROM tg_photo 
                                where tg_sid in (select tg_id from tg_dir where tg_type=0)
                                ORDER BY tg_date DESC
                                 LIMIT 1
        ");


$_percent='0.5';

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

<?php 
require root_path.'/includes/header.inc.php';
   
?>

    <div id="list">
        <h2>帖子列表</h2>
        <a class="post" href="post.php">发表帖子</a>
      
        <ul class="article">
        
          <?php 
          //跟博友列表类似。但是完全按照博友的话，css有不一样。关键是一个在ul里边，一个在外边，受到ui的影响。
          
            $_htmllist = array();
            while (!!$_rows =_fetch_array_list($_result)) {
    	    $_htmllist['id'] = $_rows['tg_id'];
    	    $_htmllist['title'] = $_rows['tg_title'];
    	    $_htmllist['readcount'] = $_rows['tg_readcount'];
    	    $_htmllist['commendcount'] = $_rows['tg_commendcount'];
	    
        	echo '<li class="icon1"><em>阅读数（<strong>'.$_htmllist['readcount'].'</strong>）评论数（<strong>'.$_htmllist['commendcount'].'</strong>）</em><a href="article.php?id='.$_htmllist['id'].'">'.$_htmllist['title'].'</a></li>';
        	
            }
        	?>
        	
        </ul>
        <?php _paging(8);?>

    </div>
    
    <div id="user">
        <h2>最新会员</h2>
        <dl>     
    		<dd class='user'><?php echo $_html['username']?>(<?php echo $_html['sex']?>)</dd>
    		<dt><img src="<?php echo $_html['face']?>" alt="<?php echo $_html['username']?>" /></dt>
    		<dd class='message'><a href ="javascript:;"  name = "message" onclick = "javascript:window.open('message.php?id=71','message','width=700px,height=400px')">发消息</a></dd>
        	<dd class='friend'><a href ="javascript:;"  name = "friend" onclick = "javascript:window.open('friend.php?id=71','friend','width=700px,height=400px')">加为好友</a></dd>
        	<dd class='guest'>写留言</dd>
        	<dd class='flower'><a href ="javascript:;"  name = "flower" onclick = "javascript:window.open('flower.php?id=71','flower','width=700px,height=400px')">给他送花</a></dd>
        	<dd class='email'>邮件：<a href="mailto:"><?php echo $_html['email']?></a></dd>
        	<dd class='url'>网址：<a href="<?php echo $_html['url']?>" target="_blank"><?php echo $_html['url']?></a></dd>
		</dl>
    </div>
    
    <div id="pics">
        <h2>最新图片--<?php echo $_photo['name']?></h2>
        <a href="photo_show_detail.php?id=<?php echo $_photo['id']?>"><img src="thumb.php?filename=<?php echo $_photo['url'] ?>&percent=<?php echo $_percent?>"></img></a>
        
        
    </div>

<?php 
require root_path.'/includes/footer.inc.php';	

?>

</body>
</html>