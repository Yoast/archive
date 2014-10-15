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
		preview.innerHTML = ''
		canvas = document.createElement 'canvas'
		canvas.id = 'image'

		preview.appendChild canvas

	#preview.innerHTML = '<canvas id="image"></canvas>';

		#canvas = preview.getElementById 'image'
		canvas.src = create_canvas e.target.result, canvas ;


#		image = '<img id="image" src="' + e.target.result + '" alt="" />';


		return 1;

create_canvas = (image_string, canvas) ->

	context    = canvas.getContext '2d'

	base_image = new Image();
	base_image.src = image_string;

	width = base_image.width
	height = base_image.height

	set_canvas_dimensions(canvas, width, height)

	base_image.onload = ->
		context.drawImage base_image, 0 , 0, width, height


		return 1;


	return 1;

set_canvas_dimensions = (canvas, width, height) ->
	canvas.width = width
	canvas.height = height

rotate = (Degree) ->




input.addEventListener 'change', changed, true
