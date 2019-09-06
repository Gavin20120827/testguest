<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月2日/上午1:18:48
* 修改时间：2019年6月2日上午1:18:48
* 修改备注：   
* 版本：
*/

session_start();
//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "photo_show_detail");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';


//把图片的评论写进数据库
if ($_GET[action]=="redetail"){
    _check_code($_POST["code"], $_SESSION["code"]);
   
    $_clean = array();
    $_clean["username"]=$_COOKIE["username"];
    $_clean["title"]=$_POST["title"];
    $_clean["content"]=$_POST["content"];
    $_clean["sid"]=$_POST["sid"];
    
    
    global $_conn;
    mysqli_query( $_conn , "INSERT INTO tg_photo_commend(
                                             tg_username,
                                             tg_title,
                                             tg_content,
                                             tg_sid,
                                             tg_date
                                           )
                                    values(
                                          '".$_clean['username']."' ,
                                          '".$_clean['title']."',
                                          '".$_clean['content']."' ,
                                          '".$_clean['sid']."',
                                          NOW()
                                           )"
        
        )
        or die("数据库存入失败".mysqli_error($_conn));
        
        if (_affected_rows()==1){

            //更新评论量
            _query("update tg_photo set tg_commentcount=tg_commentcount+1 where tg_id='{$_clean['sid']}'");
            
            //关闭数据库
            mysqli_close($_conn);
            //             //清除session
            //             _session_destroy();
            _location("恭喜你评论图片成功","photo_show_detail.php?id=".$_clean['sid']);
        }else {
            mysqli_close($_conn);
            //             //清除session
            //             _session_destroy();
            _location("很遗憾你评论失败","photo_show_detail.php?id=".$_clean['sid']);
        }
        
}





//  此id是从photo_show中传过来，即相册的id.此操作用来获得id
if (isset($_GET['id'])){
    
    //读出图片数据
  if (!!$_rows=_fetch_array("select 
                                        tg_id, 
                                        tg_sid,
                                        tg_url,
                                        tg_content,
                                        tg_username,
                                        tg_date,
                                        tg_readcount,
                                        tg_commentcount,
                                        tg_name 
                                from    tg_photo
                                where    tg_id='{$_GET['id']}'

       ")){
       
       //第一步，确定该图片是否加密相册里边；第二步，如果是加密的，确定是否有cookie或者管理员，然后判断是否允许进入
           if (!isset($_SESSION['admin'])){
            if (!!$_rowss=_fetch_array("select tg_type,tg_name,tg_id from tg_dir where tg_id='{$_rows['tg_sid']}'")){
                if (!empty($_rowss['tg_type']) && $_COOKIE['photo'.$_rowss['tg_id']]!=$_rowss['tg_name']){
                    _alert_back('非法操作，此相册为加密相册');
                }
            }else {
                _alert_back('项目目录表出错了');
            }
           }

        //累计阅读量
          _query("update tg_photo set tg_readcount=tg_readcount+1 where tg_id='{$_GET['id']}'");
        
      //把id取出来，然后传给图片上传的页面。确保上传到同一个相册
      $_html=array();
      $_html['id']=$_rows['tg_id'];
      $_html['sid']=$_rows['tg_sid'];
      $_html['name']=$_rows['tg_name'];
      $_html['username']=$_rows['tg_username'];
      $_html['url']=$_rows['tg_url'];
      $_html['content']=$_rows['tg_content'];
      $_html['readcount']=$_rows['tg_readcount'];
      $_html['commentcount']=$_rows['tg_commentcount'];
      $_html['date']=$_rows['tg_date'];
      

      
      global $_pagesize,$_pagenum,$_page,$_id;
      //第一个参数获取总条数，第二个指定每页多少条。函数在global页面
      $_id='id='.$_html['id'].'&';
      _page("select tg_id from tg_photo_commend where tg_sid='{$_html['id']}'",2);
      $_result= _query("SELECT
                                  tg_username,tg_title,tg_content,tg_date,tg_sid,tg_id
                          FROM
                                  tg_photo_commend
                          WHERE
                                  tg_sid='{$_html['id']}'
                      ORDER BY
                                  tg_date ASC
                          limit
                                  $_pagenum,$_pagesize
          ");
      
        
                                  
         
         //取得比自己大的id中最小的那个——上一页
         $_html['preid']=_fetch_array("SELECT
                                                min(tg_id)
                                        AS
                                                id
                                        FROM
                                                tg_photo
                                        WHERE
                                                tg_sid='{$_html['sid']}'
                                        AND
                                                tg_id>'{$_html['id']}'
        ");
         
         if (!empty($_html['preid']['id'])){
             $_html['preid']= '<a href="photo_show_detail.php?id='.$_html['preid']['id'].'">上一页</a>';
         }else{
             $_html['preid']='<span>到头了</span>';
         }                    
                  
         
         
         //取得比自己小的id中最大的那个——上一页
         $_html['nextid']=_fetch_array("SELECT
                                                 max(tg_id)
                                             AS
                                                 id
                                             FROM
                                                 tg_photo
                                             WHERE
                                                 tg_sid='{$_html['sid']}'
                                             AND
                                                 tg_id<'{$_html['id']}'
             ");
         
         if (!empty($_html['nextid']['id'])){
             $_html['nextid']= '<a href="photo_show_detail.php?id='.$_html['nextid']['id'].'">下一页</a>';
         }else{
             $_html['nextid']='<span>到头了</span>';
         }                    
                                  
      
  }else {
      _alert_back('不存在此图片');
  }
}else {
    _alert_back('非法操作');
}


//跟thumb。php一起的。首先是thumb生成一个图片。然后这里定义一个变量，然后传到下边的html，html再通过参数传到thumb，最后传回来本页面显示出来。
$_percent='0.5';



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


<?php 
require root_path.'/includes/header.inc.php';
   
?>


<div  id='photo'>
	<h2>图片详情</h2>

		<dl class="detail">
			<dt><?php echo  $_html[preid]?><img src="<?php echo $_html['url']?>" alt="chaina" /><?php echo $_html['nextid']?></dt>
			<dd><a href="photo_detail?id=<?php echo $_html['id']?>"><?php echo $_html['name']?></a></dd>
			<dd><a href="photo_show.php?id=<?php echo $_html['sid']?>">【返回】</a></dd>
			<dd>阅（<strong><?php echo  $_html['readcount']?></strong>） 评（<strong><?php echo $_html['commentcount']?></strong>）</dd>
			<dd>上传者：<?php echo $_html['username']?></dd>
		</dl>
		
		
		
			<?php
			
			$i=1; // 设置楼层。此处与最底下的$i++,以及中部的<span># php echo $i+(($_page-1)*$_pagesize);楼</span>
	    
			$_html1 = array();
			while (!!$_rows =_fetch_array_list($_result)) {
    	$_html1['username']=$_rows['tg_username'];
    	$_html1['sid']=$_rows['tg_sid'];
    	$_html1['content']=$_rows['tg_content'];
    	$_html1['date']=$_rows['tg_date'];
    	$_html1['title']=$_rows['tg_title'];
    	$_html1['id']=$_rows['tg_id'];
    	
    	
    	//拿出用户表中的其他信息，需要填写进下边的html中，例如邮件等，只有在用户表中有
    	if (!!$_rows=_fetch_array("select
                                            	tg_sex,
                                            	dg_id,
                                            	tg_email,
                                            	tg_url,
                                            	tg_face,
                                            	tg_switch,
                                            	tg_autograph
                                    	from    tg_user
                                    	where    tg_username='{$_html1['username']}'")){
    	//不需要重复写$_html = array()
    	$_html1['sex'] = $_rows['tg_sex'];
    	$_html1['id'] = $_rows['dg_id'];
    	$_html1['email'] = $_rows['tg_email'];
    	$_html1['url'] = $_rows['tg_url'];
    	$_html1['face'] = $_rows['tg_face'];
    	$_html1['switch'] = $_rows['tg_switch'];
    	$_html1['autograph'] = $_rows['tg_autograph'];
	}
		
    	?>
		
	
    <div class="re">
    	<dl>     
    		<dd class='user'><?php echo $_html1['username']?>(<?php echo $_html1['sex'] ?>)</dd>
    		<dt><img src="face/m01.png" alt="炎日" /></dt>
    		<dd class='message'><a href ="javascript:;"  name = "message" onclick = "javascript:window.open('message.php?id=<?php echo $_html1['id'] ?>','message','width=700px,height=400px')">发消息</a></dd>
        	<dd class='friend'><a href ="javascript:;"  name = "friend" onclick = "javascript:window.open('friend.php?id=<?php echo $_html1['id'] ?>','friend','width=700px,height=400px')">加为好友</a></dd>
        	<dd class='guest'>写留言</dd>
        	<dd class='flower'><a href ="javascript:;"  name = "flower" onclick = "javascript:window.open('flower.php?id=<?php echo $_html1['id'] ?>','flower','width=700px,height=400px')">给他送花</a></dd>
    		<dd class='email'>邮件：<a href="mailto:"><?php echo $_html1['email']?></a></dd>
        	<dd class='url'>网址：<a href="<?php echo $_html1['url']?>" target="_blank"><?php echo $_html1['url']?></a></dd>
    	</dl>
        <div class="content">
        	<div class="user">
        		<span>#<?php echo $i+(($_page-1)*$_pagesize);?>楼</span><?php echo $_html1['username']?>发表于 <?php echo $_html1['date']?>
    		</div>
    		<h3><?php echo $_html1['title']; echo $_html['re']?></h3>
    		<div class="detail">
    			<?php echo $_html1['content'] ?>
    			<?php echo $_html['autograph_html']?>
    		</div>

    	</div>

    </div>


		<?php 
		$i++;
			
			}
		
		_paging(1);
		
		?>
		

		
   <?php if (isset($_COOKIE['username'])){?>

    <form method="post" action="?action=redetail">
       <input type="hidden" name="sid" value="<?php echo $_html['id']?>"/>
    	<dl class="redetail">

			<dd>标题：<input type="text" name="title" class="text2" value="RE:<?php echo $_html['name']?> "/>(＊必填，2位到20位)</dd>
			<dd>

				<textarea name="content" ></textarea>
			</dd>
			<dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
			<dd><input type="submit" name="submit" value="评论图片" class="submit"/></dd>
    	</dl>
    </form>
    <?php }?>
		
</div>


<?php 
require root_path.'/includes/footer.inc.php';	

?>


</body>
</html>