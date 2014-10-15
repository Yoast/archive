input   = document.getElementById 'upload_file';
preview = document.getElementById 'image_preview';
steps = 0

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

		canvas.src = create_canvas e.target.result, canvas ;

		return 1

create_canvas = (image_string, canvas) ->

	context    = canvas.getContext '2d'

	base_image = new Image()
	base_image.src = image_string

	width = base_image.width
	height = base_image.height

	set_canvas_dimensions(canvas, width, height)

	base_image.onload = ->
		context.drawImage base_image, 0 , 0, width, height
		canvas.addEventListener 'click', () -> rotate base_image , true


set_canvas_dimensions = (canvas, width, height) ->
	canvas.width = width
	canvas.height = height


rotate = (base_image, degree = 90) ->

	canvas  = document.getElementById 'image'
	context = canvas.getContext '2d'

	cx = 0
	cy = 0
	if steps is 0
		cw = base_image.height
		ch = base_image.width
		cy = cw * (-1)

		steps = 1
	else if steps is 1
		cx = base_image.width * (-1)
		cy = base_image.height * (-1)
		degree = 180
		steps = 2
	else if steps is 2
		cw = base_image.height
		ch = base_image.width
		cx = base_image.width * (-1)
		degree = 270
		steps = 0


	canvas.setAttribute 'width', cw
	canvas.setAttribute 'height', ch
	context.rotate (90 * Math.PI) / 180
	context.drawImage base_image, cx, cy, cw, ch


input.addEventListener 'change', changed, true