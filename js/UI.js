$(".button-collapse").sideNav();

function invertSelection() {
	$(".imageCheckBox").each(function (index) {
		invertImageCheckBox(this);
	})
}

function getSelectedRoutes() {
	var out = [];
	var i = 0;
	$(".imageCheckBox").each(function (index) {
		if (this.src.includes("unchecked")) {
			out[i++] = this.id;
		}
	});
	return out;
}

$(".imageCheckBox").click(function toggle() {
	invertImageCheckBox(this);
});

function invertImageCheckBox(box){
	$("#applyButton").disabled = false;
	if (box.src.includes("unchecked")) {
		box.src = box.src.replace("unchecked", "");
	} else {
		box.src = box.src.replace(".png", "unchecked.png");
	}
}

function apply(){
	$("#applyButton").disabled = true;
	getBuses();
}