$(".button-collapse").sideNav();
$("#applyButton").attr("disabled", "disabled");

$(".imageCheckBox").click(function toggle() {
	console.log("click");
	invertImageCheckBox(this);
});
function invertSelection() {
	$(".imageCheckBox").each(function () {
		invertImageCheckBox(this);
	})
}

function getSelectedRoutes() {
	var out = [];
	var i = 0;
	$(".imageCheckBox").each(function (index) {
		if (!this.src.includes("unchecked")) {
			out[i++] = this.id;
		}
	});
	return out;
}

function invertImageCheckBox(box){
	$("#applyButton").removeAttr("disabled");
	if (box.src.includes("unchecked")) {
		box.src = box.src.replace("unchecked", "");
	} else {
		box.src = box.src.replace(".png", "unchecked.png");
	}
}

function apply(){
	$("#applyButton").attr("disabled", "disabled");
	getBuses();
}