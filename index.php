<!DOCTYPE html>
<html>
<head lang="en">
	<meta charset="UTF-8">
	<title>Image Uploadizer</title>
	<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
</head>
<body>
<p>This is the Image Uploadizer.</p>

<div id="form">
	<form enctype='multipart/form-data'>
		<input id='upload_file' type="file" name="upload_file" accept="image/*" />
	</form>
</div>
<div id="image_preview"></div>

<script type="text/javascript" src="uploadizer.js"></script>
</body>
</html>