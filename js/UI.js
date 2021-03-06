$(".button-collapse").sideNav();
$("#applyButton").attr("disabled", "disabled");

function invertSelection() {
	$(".imageCheckBox").each(function () {
		invertImageCheckBox(this);
	})
}

$(".imageCheckBox").click(function toggle() {
	invertImageCheckBox(this);
});

function getSelectedRoutes() {
	var out = [];
	var i = 0;
	$(".imageCheckBox").each(function (index) {
		if (!this.src.includes("unchecked")) {
			out[i++] = this.id;
		}
	});
	if (out.length === 0)
		return getAllRoutes();
	else
		return out;
}

function getAllRoutes() {
	var out = [];
	var i = 0;
	$(".imageCheckBox").each(function (index) {
		out[i++] = this.id;
	});
	return out;
}

function invertImageCheckBox(box) {
	$("#applyButton").removeAttr("disabled");
	if (box.src.includes("unchecked")) {
		box.src = box.src.replace("unchecked", "");
	} else {
		box.src = box.src.replace(".png", "unchecked.png");
	}
}

function apply() {
	$("#applyButton").attr("disabled", "disabled");
	getBuses();
}

if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
	// some code that changes the side nav's css to make everything bigger
	//There's literally a CSS thing for doing this
}