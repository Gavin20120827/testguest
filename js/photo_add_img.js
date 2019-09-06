window.onload=function(){
	var up=document.getElementById('up');
	up.onclick=function(){
		centerWindow('upimg.php?dir='+this.title,'up','100','400');
	};
};


function centerWindow(url,name,height,width){
	var left=(screen.width-width)/2;
	var top=(screen.height-height)/2;
	window.open(url,name,'height='+height+',width='+width+',top='+top+',left='+left);
}
