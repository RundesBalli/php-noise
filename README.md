# :milky_way: php-noise
A script for generating (random) noise background images.  
:globe_with_meridians: Homepage: [php-noise.com](https://php-noise.com) - :octocat: [GitHub Repository](https://github.com/RundesBalli/php-noise.com/)  

## :wrench: Requirements
`php-gd` is required for this script to work.  
You can install it with:  
```
sudo apt install php-gd
```
Create a directory `mkdir images` and `chmod 0777 ./images/` it.

## :computer: Platform
You can either use this script via CLI or in the Browser.  
The Browser script has several high-caps to prevent too big values.  

### :clipboard: Parameters
$`php noise.php --help` and $`php noise.php -h`, and `./noise.php?help` in the browser will show all possible parameters.  
All parameters are optional. A script call in the browser requires passing parameters via GET.

```
-h, --help
	Shows this help text and exits the script.
--hex <value>
	Color HEX Code
	Possible values: #000000-#FFFFFF
	The hash (#) must not be provided. If the parameter is provided, the -r -g -b parameters will be ignored.
	If the Hex-Code is invalid, a random color will be generated.
-r <value>, -g <value>, -b <value>
	Red, green, blue
	Possible values: 0-255
	If one of the parameters is invalid or not provided, it will be generated randomly.
	If the --hex parameter is provided, all three of these parameters will be ignored.
--tiles <value>
	Number of tiles per row and column.
	The image is square, therefore it hast $tiles x $tiles tiles.
	Default: 50
	In CLI this value isn't capped. Outside of the CLI its capped to 50.
--tileSize <value>
	Width and height of one tile in pixels.
	Default: 7
	In CLI this value isn't capped. Outside of the CLI its capped to 20.
--borderWidth <value>
	Width of the grid which is drawed between tiles in pixels.
	Default: 0
	In CLI this value isn't capped. Outside of the CLI its capped to 15.
--mode <value>
	Color calculation mode.
	1. brightness:	Calculates the colors by brightness adjustments based on the reference color.
	2. around:	Calculates the colors randomly around the reference color.
	Default: brightness
--json
	Saves the image and returns a JSON-String with the filename.
	Only via GET in browsermode.
```

## :bookmark_tabs: Examples
For several examples, see the [README.md](https://github.com/RundesBalli/php-noise/blob/master/examples/README.md) in the examples directory.
