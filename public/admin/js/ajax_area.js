/*
id 区域ID，
name 表单NAME，默认构成的是 表单名[0] 如：<select name="area[0]"></select>
key 级联表单序号，默认是0
*/
function ajax_area(id,name) {
	//console.log(id);
	var ids= new Array();
	ids=id.split(",");
	var tid=0;
	for (i=0;i<ids.length ;i++ ){
		getarea(tid,name,i,ids[i]);
		tid=ids[i];
	} 
}
function getarea(id,name,key,def) {
	if(key==4) return;
	$.getJSON("/admin/index.php/index/getAreaJson/id-"+id+"", function(json) {
		if (key == null || id==0) key=0;
		if (json != null) {
			var selectdom,
				selectold = document.getElementById(name+"_"+key);
				//console.log("area"+key);
			if (selectold!=null){
				if (key==1){
					if (document.getElementById(name+"_1")!=null) document.getElementById("area1").removeChild(document.getElementById(name+"_1"));
					if (document.getElementById(name+"_2")!=null) document.getElementById("area2").removeChild(document.getElementById(name+"_2"));
					if (document.getElementById(name+"_3")!=null) document.getElementById("area3").removeChild(document.getElementById(name+"_3"));
				}else if(key==2){
					if (document.getElementById(name+"_2")!=null) document.getElementById("area2").removeChild(document.getElementById(name+"_2"));
					if (document.getElementById(name+"_3")!=null) document.getElementById("area3").removeChild(document.getElementById(name+"_3"));
				}else if(key==3){
					if (document.getElementById(name+"_3")!=null) document.getElementById("area3").removeChild(document.getElementById(name+"_3"));
				}
				if (document.getElementById(name+"_4")!=null) document.getElementById("area4").removeChild(document.getElementById(name+"_4"));
			}
			if (json.code==0){
				return;
			}
			selectdom = document.createElement('select'); 
			selectdom.id = name+"_"+key;   
			selectdom.name = name+"["+key+"]"; 
			document.getElementById("area"+key).appendChild(selectdom);
			/*
			var select_change = document.createAttribute("onchange");
			select_change.nodeValue = "getarea(this.value,'"+name+"',"+(key+1)+",this.value)"; 
			selectdom.setAttributeNode(select_change); 
			*/
			selectdom.add(new Option('请选择',''),0) 
			var _select = document.getElementById(name+"_"+key);
			_select.addEventListener("change",function(){getarea(this.value,name,key+1,this.value);},false); 
			for(var item in json.data) {
				_select.options.add(new Option(json.data[item].value,json.data[item].id)); 
			}
			for (var i = 0; i < _select.options.length; i++) { 
				if (_select.options[i].value == def) { 
					_select.options[i].selected=true; 
					break; 
				} 
			}
		}
	});
}