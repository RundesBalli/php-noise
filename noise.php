<?php
/**
 * noise.php
 * 
 * A script for generating (random) noise background images.
 * 
 * @author    RundesBalli <github@rundesballi.com>
 * @copyright 2021 RundesBalli
 * @version   1.0
 * @see       https://github.com/RundesBalli/php-noise
 */

/**
 * Default configuration
 */
$tiles = 50;       // $tiles x $tiles tiles
$tileSize = 7;     // Pixels per tile
$borderWidth = 3;  // Pixels

/**
 * Into text
 */
$introText = "       __                                                             
      /\\ \\                                       __                   
 _____\\ \\ \\___   _____              ___     ___ /\\_\\    ____     __   
/\\ '__`\\ \\  _ `\\/\\ '__`\\  _______ /' _ `\\  / __`\\/\\ \\  /',__\\  /'__`\\ 
\\ \\ \\L\\ \\ \\ \\ \\ \\ \\ \\L\\ \\/\\______\\/\\ \\/\\ \\/\\ \\L\\ \\ \\ \\/\\__, `\\/\\  __/ 
 \\ \\ ,__/\\ \\_\\ \\_\\ \\ ,__/\\/______/\\ \\_\\ \\_\\ \\____/\\ \\_\\/\\____/\\ \\____\\
  \\ \\ \\/  \\/_/\\/_/\\ \\ \\/           \\/_/\\/_/\\/___/  \\/_/\\/___/  \\/____/
   \\ \\_\\           \\ \\_\\                                              
    \\/_/            \\/_/\n\n";
// http://www.network-science.de/ascii/ Font: larry3d
$introText.= "PHP noise image generator v1.0\n\n";
$introText.= "Visit: https://RundesBalli.com\n";
$introText.= "       https://github.com/RundesBalli/php-noise\n\n";

/**
 * Help text
 */
$help = "Usage:\n";
$help.= "All parameters are optional.\n";
$help.= "-h, --help\n\tShows this help text and exits the script.\n";
$help.= "-r <value>, -g <value>, -b <value>\n\tRed, green, blue\n\tPossible values: 0-255\n\tIf one of the parameters is invalid or not provided, it will be generated randomly.\n";
$help.= "--tiles <value>\n\tNumber of tiles per row and column.\n\tThe image is square, therefore it hast \$tiles x \$tiles tiles.\n\tDefault: ".$tiles."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 50.\n";
$help.= "--tileSize <value>\n\tWidth and height of one tile in pixels.\n\tDefault: ".$tileSize."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 20.\n";
$help.= "--borderWidth <value>\n\tWidth of the grid which is drawed between tiles in pixels.\n\tDefault: ".$borderWidth."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 15.\n";
$help.= "\n";

/**
 * colorPicker function
 * 
 * @param  array The provided $paramColors from the user.
 * 
 * @return array The validated or randomly generated colors.
 */
function colorPicker(array $paramColors) {
  $arg = array();
  $arg['r'] = ((isset($paramColors['r']) AND is_numeric($paramColors['r'])) ? intval($paramColors['r']) : random_int(0, 255));
  $arg['g'] = ((isset($paramColors['g']) AND is_numeric($paramColors['g'])) ? intval($paramColors['g']) : random_int(0, 255));
  $arg['b'] = ((isset($paramColors['b']) AND is_numeric($paramColors['b'])) ? intval($paramColors['b']) : random_int(0, 255));
  return $arg;
}


/**
 * Check if the script is called via CLI or via browser.
 */
if(php_sapi_name() == 'cli') {
  /**
   * Script is called via CLI.
   */
  $verbose = 1;
  echo $introText;

  /**
   * Read arguments provided by CLI script call.
   */
  $options = getopt("hr:g:b:", array("help", "tiles:", "tileSize:", "borderWidth:"));

  /**
   * If help is called, show help text and exit script.
   */
  if(isset($options['h']) OR isset($options['help'])) {
    die($help);
  }
  
  $arg = colorPicker($options);
  $tiles = ((isset($options['tiles']) AND is_numeric($options['tiles'])) ? intval($options['tiles']) : $tiles);
  $tileSize = ((isset($options['tileSize']) AND is_numeric($options['tileSize'])) ? intval($options['tileSize']) : $tileSize);
  $borderWidth = ((isset($options['borderWidth']) AND is_numeric($options['borderWidth'])) ? intval($options['borderWidth']) : $borderWidth);
  unset($options);
} else {
  /**
   * Script is called via browser.
   */
  $verbose = 0;
  if(isset($_GET['h']) OR isset($_GET['help'])) {
    header("Content-Type: text/plain");
    echo $introText;
    echo $help;
    echo "In a browser, you can simply provide those parameters via GET.\n\n";
    echo "Please report bugs to:\nhttps://github.com/RundesBalli/php-noise/issues\n";
    die();
  }
  
  $arg = colorPicker($_GET);
  $tiles = (((isset($_GET['tiles']) AND is_numeric($_GET['tiles'])) AND (intval($_GET['tiles']) > 0 AND intval($_GET['tiles']) <= 50)) ? intval($_GET['tiles']) : $tiles);
  $tileSize = (((isset($_GET['tileSize']) AND is_numeric($_GET['tileSize'])) AND (intval($_GET['tileSize']) > 0 AND intval($_GET['tileSize']) <= 20)) ? intval($_GET['tileSize']) : $tileSize);
  $borderWidth = (((isset($_GET['borderWidth']) AND is_numeric($_GET['borderWidth'])) AND (intval($_GET['borderWidth']) > 0 AND intval($_GET['borderWidth']) <= 15)) ? intval($_GET['borderWidth']) : $borderWidth);
}

/**
 * Image parameters
 */
$x = ($tiles*$tileSize)+($tiles*$borderWidth);
$y = $x;

/**
 * Every color should have 20 possible values around the provided parameter value.
 * If the value exceeds the minimum (0) or maximum (255), it will be set to the immediate maximum or minimum value instead.
 */
if($verbose == 1) {
  echo "Selected/generated colors (min|max):\n";
}
$color = array();
foreach($arg AS $key => $val) {
  if($verbose == 1) {
    echo strtoupper($key).": ".$val." ";
  }
  if($val < 10) {
    $color[$key]['min'] = 0;
    $color[$key]['max'] = 19;
  } elseif($val > 245) {
    $color[$key]['min'] = 236;
    $color[$key]['max'] = 255;
  } else {
    /**
     * If the given value is within the enforced minimum (0) / maximum (255) boundaries (including an additional
     * padding of 10 units from the min/max), a new boundary for the given value is calculated based on a random
     * approximation towards the initial lower or upper boundary, thus, yielding new minima/maxima for further
     * calculations.
     */
    if(random_int(0, 1)) {
      $color[$key]['min'] = $val-9;
      $color[$key]['max'] = $val+10;
    } else {
      $color[$key]['min'] = $val-10;
      $color[$key]['max'] = $val+9;
    }
  }
  if($verbose == 1) {
    echo "(".$color[$key]['min']."|".$color[$key]['max'].")\n";
  }
}
if($verbose == 1) {
  echo "\n";
}

/**
 * Generate the random border color.
 */
$borderColor = array();
if($verbose == 1) {
  echo "Generated border color:\n";
}
foreach($color AS $key => $val) {
  $borderColor[$key] = random_int($val['min'], $val['max']);
  if($verbose == 1) {
    echo strtoupper($key).": ".$borderColor[$key]."\n";
  }
}
if($verbose == 1) {
  echo "\n";
}

/**
 * Create image resource
 */
$im = imagecreatetruecolor($x, $y);

/**
 * Draw grid between tiles
 */
$draw_x = 0;
$draw_y = 0;
while($draw_x < $x) {
  $draw_x = $draw_x + $tileSize;
  imagefilledrectangle($im, $draw_x, 0, ($draw_x + ($borderWidth-1)), $y, imagecolorallocate($im, $borderColor['r'], $borderColor['g'], $borderColor['b']));
  $draw_x = $draw_x + $borderWidth;
}
while($draw_y < $y) {
  $draw_y = $draw_y + $tileSize;
  imagefilledrectangle($im, 0, $draw_y, $x, ($draw_y + ($borderWidth-1)), imagecolorallocate($im, $borderColor['r'], $borderColor['g'], $borderColor['b']));
  $draw_y = $draw_y + $borderWidth;
}

/**
 * Draw the tiles
 */
$draw_x = 0;
$draw_y = 0;
while($draw_x < $x) {
  $draw_y = 0;
  while($draw_y < $y) {
    $tileColor = imagecolorallocate($im, random_int($color['r']['min'], $color['r']['max']), random_int($color['g']['min'], $color['g']['max']), random_int($color['b']['min'], $color['b']['max']));
    imagefilledrectangle($im, $draw_x, $draw_y, $draw_x+$tileSize-1, $draw_y+$tileSize-1, $tileColor);
    $draw_y = $draw_y+$tileSize+$borderWidth;
  }
  $draw_x = $draw_x+$tileSize+$borderWidth;
}

/**
 * Save/Output the imagefile
 */
if($verbose == 1) {
  $filename = "./noise_r".$arg['r']."-g".$arg['g']."-b".$arg['b']."-t".$tiles."-tS".$tileSize."-bW".$borderWidth."_".md5(date("Y-m-d_H-i-s").microtime()).".png";
  imagePNG($im, $filename);
  echo "Output to:\n".realpath($filename)."\n\n";
  echo "Please report bugs to:\nhttps://github.com/RundesBalli/php-noise/issues\n";
} else {
  header('Content-Type: image/png');
  imagePNG($im);
}
imagedestroy($im);
?>
