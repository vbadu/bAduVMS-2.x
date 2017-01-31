//在需要使用的地方加入下面的代码即可;
/*
<script language="javascript">
	showTime();
</script>
*/
function initArray(){
	for(i=0;i<initArray.arguments.length;i++)
	this[i]=initArray.arguments[i];
}
	var isnMonths=new initArray("1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月");
	var isnDays=new initArray("星期日","星期一","星期二","星期三","星期四","星期五","星期六","星期日");
	today=new Date();
	hrs=today.getHours();
	min=today.getMinutes();
	sec=today.getSeconds();
	clckh=""+((hrs>12)?hrs:hrs);
	clckm=((min<10)?"0":"")+min;
	clcks=((sec<10)?"0":"")+sec;
	clck=(hrs>=12)?"下午":"上午";
	var stnr="";
	var ns="0123456789";
	var a="";
function getFullYear(d){
	yr=d.getYear();if(yr<1000)
	yr+=1900;return yr;
};
function showTime(){
	document.write("<span id='timebox'></span>");
	getTimes();
	setInterval(getTimes,450);
}
function getTimes(){
	today=new Date();
	hrs=today.getHours();
	min=today.getMinutes();
	sec=today.getSeconds();
	clckh=""+((hrs>12)?hrs:hrs);
	clckm=((min<10)?"0":"")+min;
	clcks=((sec<10)?"0":"")+sec;
//	times=getFullYear(today)+"年 "+isnMonths[today.getMonth()]+today.getDate()+"日 "+isnDays[today.getDay()]+" "+clckh+":"+clckm+":"+clcks+"";
	times=getFullYear(today)+"年"+isnMonths[today.getMonth()]+today.getDate()+"日 "+isnDays[today.getDay()]+"";
	document.getElementById("timebox").innerHTML=times;
}