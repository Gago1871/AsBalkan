window.onload = function() {
	
	var closeButton = document.getElementById('closebutton');
	closeButton.addEventListener('click', function () {
		hideUploadForm();
	}, false);

	var closeLink = document.getElementById('closelink');
	closeLink.addEventListener('click', function () {
		hideUploadForm();
	}, true);
	
	// var showUploadButton = document.getElementById('show-upload');
	// showUploadButton.addEventListener('click', function () {
	// 	document.getElementById('upload').style.display = "block";
	// }, true);
	
}

function showUploadForm() {

	document.getElementById('upload').style.display = "block";
	return true;
}

function hideUploadForm() {

	document.getElementById('upload').style.display = "none";
	return false;
}