<?php
/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月28日/下午1:25:26
* 修改时间：2019年5月28日下午1:25:26
* 修改备注：   
* 版本：
*/


//这个文件全部放公共函数


function _time($_now_time,$_pre_time,$_second){
    if ($_now_time-$_pre_time<$_second){
        _alert_back("请阁下休息一会儿吧！");
    }
}


// 后台管理员登陆
function _manage_login(){
    if ((!isset($_COOKIE['username'])) || (!isset($_SESSION['admin']))){
        _alert_back(" 非法登陆");
    }
}


 /**
  * _runtime（）是用来获取执行耗时的
  * 目前这个函数是在common中调用了，但是在footer中似乎也可以使用
  */

function _runtime(){
    $_mtime = explode(' ', microtime()) ;
    return $_mtime[1]+$_mtime[0];
}



//设置弹窗
function _alert_back ($_info){
    echo "<meta http-equiv='Content-Type' content='text/html'; charset='UTF-8'/>";
    echo "<script type='text/javascript'>alert('".$_info."');history.back();</script>";
    exit();
}

//页面跳转
function _location($_info,$_url){
    if (!empty($_info)){
        echo "<meta http-equiv='Content-Type' content='text/html'; charset='UTF-8'/>";
        echo "<script type='text/javascript'>alert('".$_info."');location.href='$_url';</script>";
        exit();
        }else {
            header('location:'.$_url);
        }
}


//自动关闭
function _alert_close ($_info){
    echo "<meta http-equiv='Content-Type' content='text/html'; charset='UTF-8'/>";
    echo "<script type='text/javascript'>alert('".$_info."');window.close();</script>";
    exit();
}



//验证码
function _check_code($_first_code,$_end_code){
    if ($_first_code!=$_end_code){
        _alert_back ("验证码不正确");
    }
}

//session清除
function  _session_destroy(){
    if (session_start()){
        session_destroy();
    }
}

//退出（删除cookie）
function _unsetcookies(){
    setcookie('username','',time()-1);
    _session_destroy();
    _location(null,'index.php');
}


// 防止登陆以后又能注册和登陆
function _login_state(){
        if (isset($_COOKIE['username'])){
            _alert_back (" 登录状态无法进行本操作");
        }
    }
    
    
    
    
    
    
//分页参数    
    function _page($_sql,$_size){
    global $_page,$_pagesize,$_pagenum,$_page,$_pageabsolute,$_id,$_num;
    //分页模块。同时容错处理：当没有get的时候，是空值、小数等情况时候
    if (isset($_GET['page'])){
        $_page = $_GET['page'];
        if (empty($_page)|| $_page<=0 || !is_numeric($_page)){
            $_page=1;
        }else {
            $_page = intval($_page);
        }
    }else {
        $_page=1;
    }
    
    
    $_pagesize = $_size;
    //首页要得到所有的数据的总和.num等于0，表示数据库是空的
    $_num = mysqli_num_rows(_query($_sql));
    
    if ($_num == 0){
        $_pageabsolute = 1;
    }else {
        //具体有多少页，同时进一取整数
        $_pageabsolute = ceil($_num / $_pagesize);
    } 
    // 写的页面数大于实际页数的时候
    if ($_page > $_pageabsolute){
        $_page = $_pageabsolute;
    }
    //每页从第几条开始
    $_pagenum = ($_page-1)*$_pagesize;
  }






//分页函数
function _paging($_type){
    global $_page,$_pageabsolute,$_num,$_id;
      
    if ($_type == 1){  
   echo '<div id="page_num">';
    echo '<ul>';
         for ($i=0;$i<$_pageabsolute;$i++){
		    if ($_page==($i+1)){
		        echo '<li><a href="'.scrirp.'.php?'.$_id.'page='.($i+1).'"  class="selected">'.($i+1).'</a></li>';
		    }else{
		    echo '<li><a href="'.scrirp.'.php?'.$_id.'page='.($i+1).'">'.($i+1).'</a></li>';
		    }
         }
    echo ' </ul>';
    echo '</div>';
    
    }elseif ($_type == 2) {
        global $_page,$_pageabsolute,$_num;
        
        echo '<div id = "page_text">';
        	echo '<ul>';
        		echo '<li> '.$_page.'/'.$_pageabsolute.'页|</li>';
        		echo '<li>共有<strong>'.$_num.'</strong>条数据｜</li>';	
        		     if ($_page == 1){
        		         echo '<li>首页|</li>';
        		         echo '<li>上一页|</li>';
        		     }else {
        		         echo '<li><a href="'.$_SERVER["SCRIPT_NAME"].'?'.$_id.'page=1">首页</a>｜</li>'; 
        		         echo '<li><a href="'.$_SERVER["SCRIPT_NAME"].'?'.$_id.'page='.($_page-1).'">上一页</a>｜</li>'; 
        		     }
        		
        		     if ($_page == $_pageabsolute){
        		         echo '<li>下一页|</li>';
        		         echo '<li>尾页|</li>';
        		     }else {
        		         echo '<li><a href="'.scrirp.'.php?'.$_id.'page='.($_page+1).'">下一页</a>｜</li>';
        		         echo '<li><a href="'.scrirp.'.php?'.$_id.'page= '.$_pageabsolute.'">尾页</a>｜</li>';	         
        		     }			
                	echo '</ul>';
            echo '</div>';
    }else {
        
        //自己调用自己，非1、2的数字的时候
        _paging(2);
    }
}


// 判断唯一标示符是否异常
function _uniqid($_mysqli_uniqid,$_cookie_uniqid){
    if ($_mysqli_uniqid!=$_cookie_uniqid){
        _alert_back('唯一标识符异常');
    }
}


// 截取字符数函数

function _title($_string){
    if (mb_strlen($_string,'utf-8')>14){
        $_string= mb_substr($_string, 1,14,'utf-8').'...';
    }
    return $_string;
}


//创建xml文件

function _set_xml($_xmlfile,$_clean){
    $_fp=fopen($_xmlfile, 'w');
    if (!$_fp){
        exit("文件不存在！");
    }
    flock($_fp, LOCK_EX);
    
    $_string="<?xml version=\"1.0\" encoding=\"utf-8\"?>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="<vip>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="\t<id>{$_clean['id']}</id>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="\t<username>{$_clean['username']}</username>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="\t<sex>{$_clean['sex']}</sex>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="\t<face>face/m01.png</face>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="\t<email>{$_clean['email']}</email>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="\t<url>{$_clean['url']}</url>\r\n";
    fwrite($_fp,$_string,strlen($_string));
    $_string="</vip>";
    fwrite($_fp,$_string,strlen($_string));
    
    flock($_fp, LOCK_UN);
    fclose($_fp);
}


//读取xml文件
function _get_xml($_xmlfile){
    //读取xml文件
    $_html=array();
    if (file_exists($_xmlfile)){
        //取出文件内容
        $_xml= file_get_contents($_xmlfile);
        
        //     echo $_xml;——用打印做测试
        //筛选出一定范围的内容，正则表达式。s表示包含空额和换行.\是转义字符
        preg_match_all('/<vip>(.*)<\/vip>/s', $_xml,$_dom);
        //     print_r($_dom);——用打印做测试,二维数组
        foreach ($_dom[1] as $_value){
            preg_match_all('/<id>(.*)<\/id>/s', $_value,$_id);
            //       print_r($_id);————测试到依然是二维数组，所以有下边的打印方式
            //      echo $_id[1][0];----打印二维数组中的值
            preg_match_all('/<username>(.*)<\/username>/s', $_value,$_username);
            preg_match_all('/<sex>(.*)<\/sex>/s', $_value,$_sex);
            preg_match_all('/<email>(.*)<\/email>/s', $_value,$_email);
            preg_match_all('/<face>(.*)<\/face>/s', $_value,$_face);
            preg_match_all('/<url>(.*)<\/url>/s', $_value,$_url);
            
            $_html['id']=$_id[1][0];
            $_html['username']=$_username[1][0];
            $_html['sex']=$_sex[1][0];
            $_html['email']=$_email[1][0];
            $_html['face']=$_face[1][0];
            $_html['url']=$_url[1][0];
            
        }
        
    }else {
        echo "文件不存在";
    }
    return $_html;
}

function _thumb($_filename,$_percent){
    //引入生成png的标头文件
    header('Content-type:image/png');
    
    $_n=explode('.', $_filename);
    
    //获取文件的信息，长度和高度
    list($_width,$_height)=getimagesize($_filename);
    
    //生成微缩长和高
    $_new_width=$_width*$_percent;
    $_new_height=$_height*$_percent;
    
    //创建一个以0.3百分比新长度的画布
    $_new_image=imagecreatetruecolor($_new_width, $_new_height);
    
    //按照已有的图片创建一个画布
    switch ($_n[1]){
        case 'jpg': $_image=imagecreatefrompng($_filename);
        break;
        case 'png': $_image=imagecreatefrompng($_filename);
        break;
        case 'gif': $_image=imagecreatefrompng($_filename);
        break;
        
    }
    
    //将原图采集后重新复制到新图上，就缩略了
    imagecopyresampled($_new_image, $_image, 0, 0, 0, 0, $_new_width, $_new_height, $_width, $_height);
    imagepng($_new_image);
    imagedestroy($_new_image);
    imagedestroy($_image);
    
}




?>

