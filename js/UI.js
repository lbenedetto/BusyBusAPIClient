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
	if (out.length == 0)
		return getAllRoutes();
	else
		return out;
}

function getAllRoutes()
{
    var out = [];
    var i = 0;
    $(".imageCheckBox").each(function (index) {
        out[i++] = this.id;
    });
    return out;
}

//Finished: In the interest of a better UX, they should all be off by default.
//So, if all check boxes are off, it should show all buses.
//That way a user can just click on the one bus they actually care about, and it'll start tracking only that one
//Having to click invert first is poor design.
//TODO: make it more obvious to the user that it isn't an error that everything is shown when everything is unchecked.
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

if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
    // some code that changes the side nav's css to make everything bigger
	//There's literally a CSS thing for doing this
}