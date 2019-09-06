<?php


/**   
* 项目名称：多用户留言系统
* 创建人：zhangwei
* 创建时间：2019年5月29日/下午4:39:45
* 修改时间：2019年5月29日下午4:39:45
* 修改备注：   
* 版本：
*/


session_start();



/**
 * _code（）是验证码函数
 * @access public
 * 
 */

function _code($_width=75,$_height=25,$_rnd_code = 4, $_flag = false){
    

    
//     //随机码个数
//     $_rnd_code = 4;
    
    //创建随即码
    $num="";
    for ($i=0;$i<$_rnd_code;$i++){
        $num.=dechex(mt_rand(0,15));
    }
    
    //保存在session
    
    $_SESSION['code']= $num;
    
    
//     //长和宽
//     $_width=150;
//     $_height=50;
    
    
    
    // 创建图片
    $_img=imagecreatetruecolor($_width,$_height);
    
    //白色
    $_white= imagecolorallocate($_img, 255, 255, 255);
    
    //填充
    imagefill($_img, 0, 0, $_white);
    
    //创建黑色边框
    
//     $_flag = false;
    
    if ($_flag){
    $_black=imagecolorallocate($_img, 0, 0, 0);
    imagerectangle($_img, 0, 0, $_width-1, $_height-1, $_black);
}
    
    //随机画出六个线条
    for ($i=0;$i<6;$i++){
        $_rnd_color=imagecolorallocate($_img, mt_rand(0,255), mt_rand(0,255), mt_rand(0,255));
        imageline($_img, mt_rand(0,$_width), mt_rand(0,$_height), mt_rand(0,$_width), mt_rand(0,$_height), $_rnd_color);
    }
    
    //随机雪花
    for ($i=0;$i<100;$i++){
        $_rnd_color=imagecolorallocate($_img, mt_rand(200,255), mt_rand(200,255), mt_rand(200,255));
        imagestring($_img, 1, mt_rand(1,$_width), mt_rand(1,$_height), "*", $_rnd_color);
    }
    
    
    //输出验证码
    for ($i=0;$i<strlen($_SESSION['code']);$i++){
        $_rnd_color=imagecolorallocate($_img, mt_rand(0,100), mt_rand(0,100), mt_rand(0,100));
        imagestring($_img, 5, $i*$_width/$_rnd_code+mt_rand(1,10), mt_rand(1,$_height/2), $_SESSION['code'][$i], $_rnd_color);
    }
    
    
    //生成图片
    header("content-type:image/png");
    imagepng($_img);
    
    //销毁图片
    imagedestroy($_img);
}

//默认验证码大小为75*25，默认位数为4位.此外，通过参数的方法，可以设置其他各种参数
_code( 75,25,4,false)



// $num="";
// for ($i=0;$i<4;$i++){
//     $num.=dechex(mt_rand(0,15));
// }

// $_SESSION['code']= $num;
// echo $_SESSION['code'];
// echo "<br/>";
// echo $_SESSION['code'][0];




// 网上的另一种写法，可以取消注释直接使用

// // 设置session
// session_start();
// // 设置验证码生成几位
// $num = 4;
// // 设置宽度
// $width = 100;
// // 设置高度
// $height = 30;
// //生成验证码，也可以用mt_rand(1000,9999)随机生成
// $Code = "";
// for ($i = 0; $i < $num; $i++) {
//     $Code .= mt_rand(0,9);
// }

// // 将生成的验证码写入session
// $_SESSION['Code'] = $Code;

// // 设置头部
// header("Content-type: image/png");

// // 创建图像（宽度,高度）
// $img = imagecreate($width,$height);

// //创建颜色 （创建的图像，R，G，B）
// $GrayColor = imagecolorallocate($img,230,230,230);
// $BlackColor = imagecolorallocate($img, 0, 0, 0);
// $BrColor = imagecolorallocate($img,52,52,52);

// //填充背景（创建的图像，图像的坐标x，图像的坐标y，颜色值）
// imagefill($img,0,0,$GrayColor);

// //设置边框
// imagerectangle($img,0,0,$width-1,$height-1, $BrColor);

// //随机绘制两条虚线 五个黑色，五个淡灰色
// $style = array ($BlackColor,$BlackColor,$BlackColor,$BlackColor,$BlackColor,$GrayColor,$GrayColor,$GrayColor,$GrayColor,$GrayColor);
// imagesetstyle($img, $style);
// imageline($img,0,mt_rand(0,$height),$width,mt_rand(0,$height),IMG_COLOR_STYLED);
// imageline($img,0,mt_rand(0,$height),$width,mt_rand(0,$height),IMG_COLOR_STYLED);

// //随机生成干扰的点
// for ($i=0; $i < 200; $i++) {
//     $PointColor = imagecolorallocate($img,mt_rand(0,255),mt_rand(0,255),mt_rand(0,255));
//     imagesetpixel($img,mt_rand(0,$width),mt_rand(0,$height),$PointColor);
// }

// //将验证码随机显示
// for ($i = 0; $i < $num; $i++) {
//     $x = ($i*$width/$num)+mt_rand(5,12);
//     $y = mt_rand(5,10);
//     imagestring($img,7,$x,$y,substr($Code,$i,1),$BlackColor);
// }

// //输出图像
// imagepng($img);
// //结束图像
// imagedestroy($img);


?>

