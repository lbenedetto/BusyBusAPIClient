$(".button-collapse").sideNav();


function invertSelection(){
	$(".filled-in").each(function (index){
		this.checked = !this.checked;
	})
}

function getSelectedRoutes(){
	var out;
	var i = 0;
	$(".filled-in").each(function (index){
		if(this.checked){
			out[i++] = this.id;
		}
	});
	return out;
}