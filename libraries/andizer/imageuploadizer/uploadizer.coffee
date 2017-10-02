class Uploadizer

	steps   : 1
	context : null
	canvas  : null

	constructor: () ->
		@input   = document.getElementById 'upload_file';
		@preview = jQuery '#image_preview';

		jQuery(@input).change (event) => @onchange event

	onchange: (event) =>
		@load_files event

	load_files: () =>
		Files = event.target.files
		for File in Files
			@load_file File

	load_file : ( File ) =>
		reader = new FileReader();
		reader.onload = ( File ) => @parse_file File
		reader.readAsDataURL File;

	parse_file : ( event ) =>
		@canvas = jQuery '<canvas/>', { id : 'image' }
		@preview.html ''
			. append @canvas

		@canvas.src = @create_canvas event.target.result ;

		return 1

	create_canvas : (image_string) =>
		@context       = @canvas[0].getContext '2d'

		base_image     = new Image()
		base_image.src = image_string

		width  = base_image.width
		height = base_image.height

		@set_canvas_dimensions(width, height)

		base_image.onload = =>
			@context.drawImage base_image, 0 , 0, width, height
			@canvas.click (event) => @rotate base_image
			return 1

	set_canvas_dimensions: (width, height) ->
		@canvas.attr 'width',  width
		@canvas.attr 'height', height

	rotate : (base_image) =>
		cx = 0
		cy = 0
		cw = @canvas.attr('width')
		ch = @canvas.attr('height')

		# Rotates the canvas
		@set_canvas_dimensions(ch, cw);

		new_width  = ch;
		new_height = cw;



		cw = new_width;
		ch = new_height

		console.log new_width

		if @steps is 1 || @steps is 3
			cw = new_height
			ch = new_width


		if @steps is 1
			cy     = ch * (-1)
			degree = 90
		else if @steps is 2
			cx = cw * (-1)
			cy = cw * (-1)
			degree = 180
		else if @steps is 3
			cx = ch * (-1)
			degree = 270
		else if @steps is 4
			degree = 0
			@steps = 0

		@steps = @steps + 1;

#		console.log 'degree ' + degree
		console.log 'stappen ' + @steps
		console.log 'breedte ' + cw
		console.log 'hoogte ' + ch

		@context.clearRect 0,0, cw, ch;
		@context.save()
		@context.rotate (degree * Math.PI) / 180
		@context.drawImage base_image, cx, cy, cw, ch
		@context.restore()


test = new Uploadizer;
