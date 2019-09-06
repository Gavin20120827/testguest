<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年6月8日/下午3:10:36
* 修改时间：2019年6月8日下午3:10:36
* 修改备注：   
* 版本：
*/

session_start();
//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "article");

//引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
require dirname(__FILE__).'/includes/common.inc.php';  


//回帖数据写入数据库
//这是第二遍加载。当用户点击回帖的表单，发送action才会激活。
if ($_GET[action]=="reaticle"){
    _check_code($_POST["code"], $_SESSION["code"]);
    
    global $_conn,$_system;
    //验证一下第二次发帖是否在规定的时间外
    _time(time(), $_COOKIE['retime'], $_system['re']);
    
    //用这种方式测试是否准确，前台会有效果
//     echo "<script>alert('".$_system['re']."')</script>";
    
    $_clean = array(); 
    $_clean["username"]=$_COOKIE["username"];
    $_clean["title"]=$_POST["title"];
    $_clean["content"]=$_POST["content"];
    $_clean["reid"]=$_POST["reid"];
    $_clean["type"]=$_POST["type"];
    
    global $_conn;
    mysqli_query( $_conn , "INSERT INTO tg_article(
                                             tg_username,
                                             tg_title,
                                             tg_content,
                                             tg_reid,
                                             tg_date,
                                             tg_type
                                           )
                                    values(
                                          '".$_clean['username']."' ,
                                          '".$_clean['title']."',
                                          '".$_clean['content']."' ,
                                          '".$_clean['reid']."',
                                          NOW(),
                                          '".$_clean['type']."'
                                           )"
        
        )
        or die("数据库存入失败".mysqli_error($_conn));
        
        if (_affected_rows()==1){
            
            setcookie('retime',time());
            
            //更新评论量
            _query("update tg_article set tg_commendcount=tg_commendcount+1 where tg_reid=0 and tg_id='{$_clean['reid']}'");
           
            //关闭数据库
            mysqli_close($_conn);
//             //清除session
//             _session_destroy();
            _location("恭喜你回复帖子成功","article.php?id=".$_clean['reid']);
        }else {
            mysqli_close($_conn);
//             //清除session
//             _session_destroy();
            _location("很遗憾你回复失败","article.php?id=".$_clean['reid']);
        }

}

 //第一遍加载，从post页面跳转过来。读出主题帖，然后填充主题帖html。id是从post文件写入数据库成功以后，alert_back中传过来的,这个id是帖子的id   
//第二遍再次加载，读出主题帖。因为回帖以后，系统跳出上一步的alert，因为又带有参数reid（此reid就是主题帖的id，不是回帖的id），所以isset又会触发，重新跳回本页面（可以理解为另一个相同内容的页面。）
if (isset($_GET['id'])){
    //$_get[id],如果没有用单引号，也是可以的，默认即使数字。
    if (!! $_rows = _fetch_array("select
                                        tg_username,
                                        tg_id,
                                         tg_type,
                                         tg_title,
                                         tg_date,
                                         tg_readcount,
                                         tg_content,
                                         tg_last_modify_time,
                                         tg_commendcount
                                    from tg_article
                                    where tg_reid=0 and tg_id='{$_GET['id']}'"))
    {
        
        //加载出来主题帖以后，累计阅读量。然后才进行数组赋值等
        _query("update tg_article set tg_readcount=tg_readcount+1 where tg_id='{$_GET['id']}'");
        
        
        $_html = array();
        $_html['username'] = $_rows['tg_username'];
        $_html['reid'] = $_rows['tg_id'];
        $_html['type'] = $_rows['tg_type'];
        $_html['title'] = $_rows['tg_title'];
        $_html['date'] = $_rows['tg_date'];
        $_html['last_modify_time'] = $_rows['tg_last_modify_time'];
        $_html['content'] = $_rows['tg_content'];
        $_html['readcount'] = $_rows['tg_readcount'];
        $_html['commendcount'] = $_rows['tg_commendcount'];

        
        //读出主题贴数据库中的数据还不够，同时需要读出用户表中的一些数据，以填充主题帖html文件中的字段。拿出用户表中的其他信息，需要填写进下边的html中，例如邮件等，只有在用户表中有
        if (!!$_rows=_fetch_array("select
                                            
                                            tg_sex,
                                            dg_id,
                                            tg_email,
                                            tg_url,
                                            tg_face,
                                            tg_switch,
                                            tg_autograph
                                        from tg_user
                                        where tg_username='{$_html['username']}'")){
        //不需要重复写$_html = array()
            $_html['sex'] = $_rows['tg_sex'];
            $_html['id'] = $_rows['dg_id'];
            $_html['email'] = $_rows['tg_email'];
            $_html['url'] = $_rows['tg_url'];
            $_html['face'] = $_rows['tg_face'];
            $_html['switch'] = $_rows['tg_switch'];
            $_html['autograph'] = $_rows['tg_autograph'];
            

        //到此为止，第一遍加载完毕，渲染主题帖html文件。下边回帖的页面等不会加载出来
        
            
            
            
            
        //此内容是用到回帖的分页，放到的是回帖的html，所以自然是第二遍显示出来。创建一个全局变量，做个带参数的分页
        global $_id;
        $_id='id='.$_html['reid'].'&';
        
        //第一遍和第二遍都可以读出。主题帖修改
        if ( $_html['username']==$_COOKIE['username']){
            //变量等于文字的格式；点击事件，直接加a标签就好了。onclick是js中使用。
            //a标签调转到新的页面，进行修改。跟member_modify类似。用帖子的id传值。
            //sql语句中，变量用花口号加上单引号；
            // echo以及其他php等语句中，常量需要引号，变量不需要引号，只需要点跟常量内容连接。如果字符串加php语句，直接写就好了,例如：title="回复<?php echo $i+(($_page-1)*$_pagesize);楼的<?php echo $_html1['username']"
            // php语言中，输出值肯定是需要带echo的。如果是html，直接写就好了（参见下边修改时间的显示内容）。
            //在来一个格式的案例：$_html['re']='<span>[<a href="#ree" name="re" title="回复'.$i+(($_page-1)*$_pagesize).'楼的'.$_html1['username'].'">回复</a>]</span>';
            // $_html['last_modify_time_string']='本帖已由['.$_html['username'].']于'.$_html['last_modify_time'].'修改过。';
            $_html['subect_modify']='[<a href="article_modify.php?id='.$_html['reid'].'">修改</a>]';
        }
        
        //读取最后修改时间. 排除没有修改的
        if ($_html['last_modify_time']!='0000-00-00 00:00:00'){
            $_html['last_modify_time_string']='本帖已由['.$_html['username'].']于'.$_html['last_modify_time'].'修改过。';
        }
        
        
        //对楼主的回复
        if ($_COOKIE['username']){
            $_html['re']='<span>[<a href="#ree" name="re" title="回复楼主'.$_html['username'].'">回复</a>]</span>';
        }
        
        
        //个性签名是否显示.如果等于0就不用再elseif了。
        if ($_html['switch']==1){
            $_html['autograph_html']='<p class="autograph">'.$_html['autograph'].'</p>';
        }
            
        
        
        

        
        //第二遍加载，读出回帖内容。因为只有第二遍的时候，tg_reid='{$_html['reid']}'才会有值（第一遍的时候，reid没有写进数据库，是空的）
        //这个全局变量如果没有也不影响，但是下面$_result= _query会警告（应该是类似$_conn）
        global $_pagesize,$_pagenum,$_page;
        //第一个参数获取总条数，第二个指定每页多少条。函数在global页面
        _page("select tg_id from tg_article where tg_reid='{$_html['reid']}'",2);
        $_result= _query("SELECT
                                    tg_username,tg_type,tg_title,tg_content,tg_date
                            FROM
                                    tg_article
                            WHERE
                                    tg_reid='{$_html['reid']}'
                            ORDER BY
                                    tg_date ASC
                            limit
                                    $_pagenum,$_pagesize
            ");
                
        }else {
           //该用户已经被删除，具体做法以后补充
        }
        
     
    }else {
        _alert_close("不存在此帖子");
    }

  
}else {
    _alert_close("非法操作");
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />


<!-- 调用css公共库文件，不用每个页面都写<link ref> 这个函数 -->
<?php 
require root_path.'/includes/title.inc.php';
?>


</head>
<body>
<?php 
require root_path.'/includes/header.inc.php';
?>


<div id="article">
	<h2>文章详情</h2>
	
	
	
	
	
	
	
<!-- 	只有在第一页的时候显示主题帖。只需要做如果是，默认如果不是就不会显示 -->
	<?php 
	if ($_page==1){
	    
	?>
	
	<div id="subject">
    	<dl>     
    		<dd class='user'><?php echo $_html['username']?>(<?php echo $_html['sex'] ?>)［楼主］</dd>
    		<dt><img src="face/m01.png" alt="炎日" /></dt>
    		<dd class='message'><a href ="javascript:;"  name = "message" onclick = "javascript:window.open('message.php?id=<?php echo $_html['id'] ?>','message','width=700px,height=400px')">发消息</a></dd>
        	<dd class='friend'><a href ="javascript:;"  name = "friend" onclick = "javascript:window.open('friend.php?id=<?php echo $_html['id'] ?>','friend','width=700px,height=400px')">加为好友</a></dd>
        	<dd class='guest'>写留言</dd>
        	<dd class='flower'><a href ="javascript:;"  name = "flower" onclick = "javascript:window.open('flower.php?id=<?php echo $_html['id'] ?>','flower','width=700px,height=400px')">给他送花</a></dd>
    		<dd class='email'>邮件：<a href="mailto:"><?php echo $_html['email']?></a></dd>
        	<dd class='url'>网址：<a href="<?php echo $_html['url']?>" target="_blank"><?php echo $_html['url']?></a></dd>
    	</dl>
        <div class="content">
        	<div class="user">
        		<span><?php echo $_html['subect_modify']?> #1楼</span><?php echo $_html['username']?>发表于 <?php echo $_html['date']?>
    		</div>
    		<h3><?php echo $_html['title'];echo $_html['re']?></h3>
    		<div id="detail">
    			<?php echo  $_html['content']?>
    			<?php echo  $_html['autograph_html']?>
    		</div>
    		<div id="read">
<!--     		内容输出换行可以用<p>标签。同时注意格式。文字是直接写的，不需要任何标点，变量也同样。但是echo里边的中文，是需要加引号的。 -->
    		<p><?php echo $_html['last_modify_time_string']?> </p>
    			 阅读量:（<?php echo  $_html['readcount']?>）评论量:（<?php echo  $_html['commendcount'] ?>)
    		
    		</div>

    	</div>

    </div>
    <?php }
	?>
    
    
    
    
    
    
   
    <?php 

    $i=2; // 设置楼层。此处与最底下的$i++,以及中部的<span># php echo $i+(($_page-1)*$_pagesize);楼</span>
    
    $_html1 = array();
    while (!!$_rows =_fetch_array_list($_result)) {
    	$_html1['username']=$_rows['tg_username'];
    	$_html1['type']=$_rows['tg_type'];
    	$_html1['content']=$_rows['tg_content'];
    	$_html1['date']=$_rows['tg_date'];
    	$_html1['title']=$_rows['tg_title'];
    	  	
    	//拿出用户表中的其他信息，需要填写进下边的html中，例如邮件等，只有在用户表中有
    	if (!!$_rows=_fetch_array("select
                                        	tg_sex,
                                        	dg_id,
                                        	tg_email,
                                        	tg_url,
                                        	tg_face,
                                            tg_switch,
                                            tg_autograph
                                    	from tg_user
                                    	where tg_username='{$_html1['username']}'")){
    	//不需要重复写$_html = array()
    	$_html1['sex'] = $_rows['tg_sex'];
    	$_html1['id'] = $_rows['dg_id'];
    	$_html1['email'] = $_rows['tg_email'];
    	$_html1['url'] = $_rows['tg_url'];
    	$_html1['face'] = $_rows['tg_face'];
    	$_html1['switch'] = $_rows['tg_switch'];
    	$_html1['autograph'] = $_rows['tg_autograph'];
    	
    	
    	//两个判断条件的，就设置两个if；设置回帖是否楼主或者沙发
    	//如果没有$_page==1，则每个月第二条都会是沙发
        	if ($_page==1&&$i==2){
        	    if ($_html1['username']==$_html['username']){
        	        $_html2['username']= $_html1['username'].'[楼主]';
        	    }else {
        	        $_html2['username']= $_html1['username'].'[沙发]';
        	    }

        	}else{
        	    $_html2['username']= $_html1['username'];
        	}
        	

            	//个性签名是否显示.如果等于0就不用再elseif了。
            	if ($_html['switch']==1){
            	    $_html['autograph_html']='<p class="autograph">'.$_html['autograph'].'</p>';
            	}
            	
    	
    	}else {
    	    //该用户已经被删除，具体做法以后补充
    	}
    	

    	//跟帖回复。可以直接在html中做,但是那样容易报错。所以还是需要在php中写好，然后变量赋值过去
    	if ($_COOKIE['username']){
    	    $_html['re']='<span>[<a href="#ree" name="re" title="回复'.($i+(($_page-1)*$_pagesize)).'楼的'.$_html1['username'].'">回复</a>]</span>';
    	}

       ?>
    <div class="re">
    	<dl>     
    		<dd class='user'><?php echo $_html2['username']?>(<?php echo $_html1['sex'] ?>)</dd>
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
    		
<!--     		锚点设置：先在需要跳转的地方设置一个a标签，跳转地址为：＃名字（此处a标签中的name、title都不是必须的－－这两个是做另一个回复替换标题的功能）。然后在跳转落地地方设置一个a标签，名字跟这里相同 -->
               <!-- 给回复帖回复，自动替换主题，需要用到js，暂时不再做， -->
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








    <div id="line">
<!--         必需登陆才能回复 -->
    <?php if (isset($_COOKIE['username'])){?>
    
<!--     锚点设置 -->
    <a name="ree"></a>
    
    <form method="post" action="?action=reaticle">
           <input type="hidden" name="reid" value="<?php echo $_html['reid']?>"/>
           <input type="hidden" name="type" value="<?php echo $_html['type']?>"/>
    	<dl>

			<dd>标题：<input type="text" name="title" class="text2" value="RE:<?php echo $_html['title']?> "/>(＊必填，2位到20位)</dd>
			<dd>

				<textarea name="content" ></textarea>
			</dd>
			<dd>验证码：<input type="text" name="code" class="text4" /> <img src="code.php" id='code' onclick="javascript:this.src='code.php?tm='+Math.random()"/></dd>
			<dd><input type="submit" name="submit" value="发表帖子" class="submit"/></dd>
    	</dl>
    </form>
    <?php }?>
	</div>
	
</div>








<?php 
require root_path.'/includes/footer.inc.php';	


?>

</body>
</html>