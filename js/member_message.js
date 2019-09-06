window.onload=function(){
	var all = document.getElementById('all');
	var form = document.getElementsByTagName('form')[0];
	all.onclick= function(){
		//获取表单内的所有表单
		for(var i=0;i<form.elements.length;i++){
			if(form.elements[i].name!='chkall'){
				form.elements[i].checked=form.chkall.checked;
			}
		}
	};
	form.onsubmit=function(){
		if(confirm('确定要删除此数据吗？')){
			return true;
		}
		return false;
	};
};