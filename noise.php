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
 * Image configuration
 */
$tiles = 50;       // $tiles x $tiles tiles
$tileSize = 7;     // Pixels per tile
$borderWidth = 3;  // Pixels
$x = ($tiles*$tileSize)+($tiles*$borderWidth);
$y = $x;

/**
 * Check if the script is called via CLI or via browser.
 */
if(php_sapi_name() == 'cli') {
  /**
   * Script is called via CLI.
   */
  $verbose = 1;
  $arg['r'] = ((isset($argv[1]) AND is_numeric($argv[1])) ? intval($argv[1]) : random_int(0, 255));
  $arg['g'] = ((isset($argv[2]) AND is_numeric($argv[2])) ? intval($argv[2]) : random_int(0, 255));
  $arg['b'] = ((isset($argv[3]) AND is_numeric($argv[3])) ? intval($argv[3]) : random_int(0, 255));
  echo "       __                                                             
      /\\ \\                                       __                   
 _____\\ \\ \\___   _____              ___     ___ /\\_\\    ____     __   
/\\ '__`\\ \\  _ `\\/\\ '__`\\  _______ /' _ `\\  / __`\\/\\ \\  /',__\\  /'__`\\ 
\\ \\ \\L\\ \\ \\ \\ \\ \\ \\ \\L\\ \\/\\______\\/\\ \\/\\ \\/\\ \\L\\ \\ \\ \\/\\__, `\\/\\  __/ 
 \\ \\ ,__/\\ \\_\\ \\_\\ \\ ,__/\\/______/\\ \\_\\ \\_\\ \\____/\\ \\_\\/\\____/\\ \\____\\
  \\ \\ \\/  \\/_/\\/_/\\ \\ \\/           \\/_/\\/_/\\/___/  \\/_/\\/___/  \\/____/
   \\ \\_\\           \\ \\_\\                                              
    \\/_/            \\/_/\n\n";
  // http://www.network-science.de/ascii/ Font: larry3d
  echo "PHP noise image generator\n\n";
  echo "Visit: https://github.com/RundesBalli/php-noise\n";
  echo "       https://RundesBalli.com\n\n";
} else {
  /**
   * Script is called via browser.
   */
  $verbose = 0;
  $arg['r'] = ((isset($_GET['r']) AND is_numeric($_GET['r'])) ? intval($_GET['r']) : random_int(0, 255));
  $arg['g'] = ((isset($_GET['g']) AND is_numeric($_GET['g'])) ? intval($_GET['g']) : random_int(0, 255));
  $arg['b'] = ((isset($_GET['b']) AND is_numeric($_GET['b'])) ? intval($_GET['b']) : random_int(0, 255));
}

/**
 * Every color should have 20 possible values around the provided parameter value.
 * If the value is too big or too small, it will be customized to the maximum or minimum values.
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
     * If the value is between the minimum and the maximum value, it gets 20 values around it.
     * It is randomly selected in which direction the minimum and maximum values are calculated.
     * Sorry, I don't know how to describe it otherways.
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
 * Draw border-grid between tiles
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
    imagefilledrectangle($im, $draw_x, $draw_y, $draw_x+$tileSize, $draw_y+$tileSize, $tileColor);
    $draw_y = $draw_y+$tileSize+$borderWidth;
  }
  $draw_x = $draw_x+$tileSize+$borderWidth;
}

/**
 * Save/Output the imagefile
 */
if($verbose == 1) {
  $filename = "./noise-r".$arg['r']."-g".$arg['g']."-b".$arg['b']."_".md5(date("Y-m-d_H-i-s").microtime()).".png";
  imagePNG($im, $filename);
  echo "Output to:\n".realpath($filename)."\n";
} else {
  header('Content-Type: image/png');
  imagePNG($im);
}
imagedestroy($im);
?>
