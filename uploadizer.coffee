input   = document.getElementById 'upload_file';
preview = document.getElementById 'image_preview';

changed = () ->
	Files      = input.files

	for File in Files
		load_file File

load_file = (File) ->
	reader = new FileReader();
	reader.onload = parse_file File
	reader.readAsDataURL File;

parse_file = (File) ->
	(e) ->
		image = '<img id="image" src="' + e.target.result + '" alt="" />';
		preview.innerHTML = image;
		return 1;

rotate = (Degree) ->




input.addEventListener 'change', changed, true
