window.onload = function() {
	
	var closeButton = document.getElementById('close');
	closeButton.addEventListener('click', function () {
		document.getElementById('upload').style.display = "none";
	}, false);
	
	// var showUploadButton = document.getElementById('show-upload');
	// showUploadButton.addEventListener('click', function () {
	// 	document.getElementById('upload').style.display = "block";
	// }, true);
	
}

function showUploadForm() {

	document.getElementById('upload').style.display = "block";
	return true;
}