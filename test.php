<?
echo urlencode("&");
?>

<hr />

<style type="text/css">
	.myDiv {
		width:100px;
		height:100px;
		border:1px solid #cacaca;
		margin:5px;
	}
	
	.myDiv2 {
		width:100px;
		height:100px;
		border:1px solid #111111;
		margin:5px;
	}
</style>
<script type="text/javascript">
	var allChildren = 0;
	var deletedChildren = 0;
	var currentAllChildren = 0;
	
	function insertDiv(){
		var myContainer = document.getElementById('myContainer');
		var newElement = document.createElement("div");
		
		while(currentAllChildren >= 5){
			var toRemove = document.getElementById("div"+deletedChildren);
			myContainer.removeChild(toRemove);
			deletedChildren++;
			currentAllChildren--;
		}
		
		newElement.className = "myDiv";
		newElement.setAttribute("id","div"+allChildren)
		newElement.innerHTML = "Div Number = "+allChildren;
		
		myContainer.insertBefore(newElement, myContainer.firstChild);
		
		allChildren++;
		currentAllChildren++;
	}
	
	var mystr = "There are @hello @world @honkเอว and @java2 in this string"
	document.write(mystr.replace(/\B@([_a-z0-9]+)/ig, function(reply) { return reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';}));
	document.write("<br/>");
	
</script>
<a href="javascript:insertDiv();">Create Div</a>
<div id="myContainer">
</div>