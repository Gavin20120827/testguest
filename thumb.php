<?php 


//定义常量授权调用
define("in_tg", true);

//定义常量，用来指定本页内容（调用公共css）
define("scrirp", "thumb");

// //引入公共文件[此处不能用root_path代替，因为这是第一次引入common文件，还没有定义root_path]
// require dirname(__FILE__).'/includes/common.inc.php';


// //生成缩略图

// _thumb('photo/1560527557/1560528449.png', 0.3)


function _thumb($_filename,$_percent){
    //引入生成png的标头文件
    header('Content-type:image/jpeg');
    
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
        case 'jpeg': $_image=imagecreatefromjpeg($_filename);
        break;
        case 'png': $_image=imagecreatefrompng($_filename);
        break;
        case 'gif': $_image=imagecreatefromgif($_filename);
        break;
        
    }
    
    //将原图采集后重新复制到新图上，就缩略了
    imagecopyresampled($_new_image, $_image, 0, 0, 0, 0, $_new_width, $_new_height, $_width, $_height);
    imagepng($_new_image);
    imagedestroy($_new_image);
    imagedestroy($_image);
    
}

_thumb($_GET['filename'],$_GET['percent'])



?>