<?php
/**
 * noise.php
 * 
 * A script for generating (random) noise background images.
 * 
 * @author    RundesBalli <github@rundesballi.com>
 * @copyright 2022 RundesBalli
 * @version   1.2
 * @see       https://github.com/RundesBalli/php-noise
 */

/**
 * Default configuration
 */
$tiles = 50;          // $tiles x $tiles tiles
$tileSize = 7;        // Pixels per tile
$borderWidth = 0;     // Width of the grid between tiles
$mode = "brightness"; // Color calculation mode: brightness, around
$multiplicator = 1.5; // Multiplicator for calculation in brightness mode
$steps = 5;           // Number of colors above and below the reference color in brightness mode

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
$introText.= "PHP noise image generator v1.2\n\n";
$introText.= "Visit: https://RundesBalli.com\n";
$introText.= "       https://github.com/RundesBalli/php-noise\n\n";

/**
 * Help text
 */
$help = "Usage:\n";
$help.= "All parameters are optional.\n";
$help.= "-h, --help\n\tShows this help text and exits the script.\n";
$help.= "--hex <value>\n\tColor HEX Code\n\tPossible values: #000000-#FFFFFF\n\tThe hash (#) must not be provided. If the parameter is provided, the -r -g -b parameters will be ignored.\n\tIf the Hex-Code is invalid, a random color will be generated.\n";
$help.= "-r <value>, -g <value>, -b <value>\n\tRed, green, blue\n\tPossible values: 0-255\n\tIf one of the parameters is invalid or not provided, it will be generated randomly.\n\tIf the --hex parameter is provided, all three of these parameters will be ignored.\n";
$help.= "--tiles <value>\n\tNumber of tiles per row and column.\n\tThe image is square, therefore it hast \$tiles x \$tiles tiles.\n\tDefault: ".$tiles."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 50.\n";
$help.= "--tileSize <value>\n\tWidth and height of one tile in pixels.\n\tDefault: ".$tileSize."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 20.\n";
$help.= "--borderWidth <value>\n\tWidth of the grid which is drawed between tiles in pixels.\n\tDefault: ".$borderWidth."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 15.\n";
$help.= "--mode <value>\n\tColor calculation mode.\n\t1. brightness:\tCalculates the colors by brightness adjustments based on the reference color.\n\t2. around:\tCalculates the colors randomly around the reference color.\n\tDefault: ".$mode."\n";
$help.= "--multi <value> (only in brightness mode)\n\tIncreases or decreases the percentage spacing between colors.\n\tAllowed values are positive floating point numbers with one decimal place.\n\tDefault: ".$multiplicator."\n";
$help.= "--steps <value> (only in brightness mode)\n\tIncreases or decreases the number of possible colors above and below the reference color.\n\tDefault: ".$steps."\n\tIn CLI this value isn't capped. Outside of the CLI its capped to 50.\n";
$help.= "--json\n\tSaves the image and returns a JSON-String with the filename.\n\tOnly via GET in browsermode.";
$help.= "\n";

/**
 * colorPicker function
 * 
 * @param  array The provided $paramColors from the user.
 * 
 * @return array The validated or randomly generated colors.
 */
function colorPicker(array $paramColors = array()) {
  $arg = array();
  $arg['r'] = (((isset($paramColors['r']) AND is_numeric($paramColors['r'])) AND (intval($paramColors['r']) >= 0 AND intval($paramColors['r']) <= 255)) ? intval($paramColors['r']) : random_int(0, 255));
  $arg['g'] = (((isset($paramColors['g']) AND is_numeric($paramColors['g'])) AND (intval($paramColors['g']) >= 0 AND intval($paramColors['g']) <= 255)) ? intval($paramColors['g']) : random_int(0, 255));
  $arg['b'] = (((isset($paramColors['b']) AND is_numeric($paramColors['b'])) AND (intval($paramColors['b']) >= 0 AND intval($paramColors['b']) <= 255)) ? intval($paramColors['b']) : random_int(0, 255));
  return $arg;
}

/**
 * hex2rgb function
 * 
 * Calculates a hex code (3 or 6 digit) to RGB.
 * 
 * @author    RundesBalli <webspam@rundesballi.com>
 * @copyright 2020 RundesBalli
 * @version   1.0
 * @license   MIT-License
 * @see       https://gist.github.com/RundesBalli/32f5491df25abb7fe0864e6447a26b75
 * @see       https://www.php.net/manual/en/function.hexdec.php#99478
 * @see       https://stackoverflow.com/questions/1636350/how-to-identify-a-given-string-is-hex-color-format/1637260#1637260
 * 
 * @param string $hex The hex string to be calculated.
 * 
 * @return array or boolean The RGB array or false.
 */
function hex2rgb($hex) {
  if(preg_match("/^(?:(?:[0-9a-f]{2}){3}|(?:[0-9a-f]){3})$/i", preg_replace("/[^0-9a-f]/i", "", $hex), $result) === 1) {
    if(strlen($result[0]) == 6) {
      return array(
        "r" => hexdec(substr($result[0], 0, 2)),
        "g" => hexdec(substr($result[0], 2, 2)),
        "b" => hexdec(substr($result[0], 4, 2)),
        "hex" => $result[0]
      );
    } elseif(strlen($result[0]) == 3) {
      return array(
        "r" => hexdec(str_repeat(substr($result[0], 0, 1), 2)),
        "g" => hexdec(str_repeat(substr($result[0], 1, 1), 2)),
        "b" => hexdec(str_repeat(substr($result[0], 2, 1), 2)),
        "hex" => $result[0]
      );
    } else  {
      return FALSE;
    }
  } else {
    return FALSE;
  }
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
  $options = getopt("hr:g:b:", array("help", "tiles:", "tileSize:", "borderWidth:", "mode:", "json", "hex:", "multi:", "steps:"));

  /**
   * If the JSON parameter is provided via CLI, a note will be shown.
   */
  if(isset($options['json'])) {
    die("Error: JSON only in browsermode!\n");
  }

  /**
   * If help is called, show help text and exit script.
   */
  if(isset($options['h']) OR isset($options['help'])) {
    die($help);
  }

  /**
   * Checks the color calculation mode
   */
  if(!empty($options['mode'])) {
    if($options['mode'] == "brightness") {
      $mode = "brightness";
    } elseif($options['mode'] == "around") {
      $mode = "around";
    }
    // No else: Mode is set by default options.
  }

  /**
   * Check parameters for brightness mode.
   */
  if($mode == "brightness") {
    if(!empty($options['multi'])) {
      /**
       * Check if the provided multiplicator is above 0.
       */
      $newMulti = round(floatval($options['multi']), 1);
      if($newMulti > 0) {
        $multiplicator = $newMulti;
      }
    }
    if(!empty($options['steps'])) {
      /**
       * Check if the provided steps value is above 0.
       */
      $newSteps = intval($options['steps']);
      if($newSteps > 0) {
        $steps = $newSteps;
      }
    }
  }

  /**
   * If a hex code is provided, use the hex code and ignore the rgb values.
   */
  if(!empty($options['hex'])) {
    $hex = hex2rgb($options['hex']);
    if($hex) {
      $arg = colorPicker($hex);
      $filename = "noise_hex-".$hex['hex']."-t".$tiles."-tS".$tileSize."-bW".$borderWidth."-m".($mode == "around" ? "A" : "B-mu".$multiplicator."-st".$steps)."_".md5(date("Y-m-d_H-i-s").microtime()).".png";
    } else {
      $arg = colorPicker($options);
    }
  } else {
    $arg = colorPicker($options);
  }

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

  /**
   * Checks the color calculation mode
   */
  if(!empty($_GET['mode'])) {
    if($_GET['mode'] == "brightness") {
      $mode = "brightness";
    } elseif($_GET['mode'] == "around") {
      $mode = "around";
    }
    // No else: Mode is set by default options.
  }

  /**
   * Check parameters for brightness mode.
   */
  if($mode == "brightness") {
    if(!empty($_GET['multi'])) {
      /**
       * Check if the provided multiplicator is above 0.
       */
      $newMulti = round(floatval($_GET['multi']), 1);
      if($newMulti > 0) {
        $multiplicator = $newMulti;
      }
    }
    if(!empty($_GET['steps'])) {
      /**
       * Check if the provided steps value is above 0 and below or equal to 50.
       */
      $newSteps = intval($_GET['steps']);
      if($newSteps > 0 AND $newSteps <= 50) {
        $steps = $newSteps;
      }
    }
  }

  /**
   * If a hex code is provided, use the hex code and ignore the rgb values.
   */
  if(!empty($_GET['hex'])) {
    $hex = hex2rgb($_GET['hex']);
    if($hex) {
      $arg = colorPicker($hex);
      $filename = "noise_hex-".$hex['hex']."-t".$tiles."-tS".$tileSize."-bW".$borderWidth."-m".($mode == "around" ? "A" : "B-mu".$multiplicator."-st".$steps)."_".md5(date("Y-m-d_H-i-s").microtime()).".png";
    } else {
      $arg = colorPicker($_GET);
    }
  } else {
    $arg = colorPicker($_GET);
  }

  $tiles = (((isset($_GET['tiles']) AND is_numeric($_GET['tiles'])) AND (intval($_GET['tiles']) > 0 AND intval($_GET['tiles']) <= 50)) ? intval($_GET['tiles']) : $tiles);
  $tileSize = (((isset($_GET['tileSize']) AND is_numeric($_GET['tileSize'])) AND (intval($_GET['tileSize']) > 0 AND intval($_GET['tileSize']) <= 20)) ? intval($_GET['tileSize']) : $tileSize);
  $borderWidth = (((isset($_GET['borderWidth']) AND is_numeric($_GET['borderWidth'])) AND (intval($_GET['borderWidth']) >= 0 AND intval($_GET['borderWidth']) <= 15)) ? intval($_GET['borderWidth']) : $borderWidth);
  $json = (isset($_GET['json']) ? 1 : 0);
}

/**
 * Image parameters
 */
$x = ($tiles*$tileSize)+($tiles*$borderWidth);
$y = $x;

/**
 * Color calculation mode
 */
if($mode == "around") {
  /**
   * "around" mode
   * 
   * Calculates the colors randomly around the reference color.
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
   * Generate all possible color arrays.
   */
  $colors = array();
  for($i = $color['r']['min']; $i <= $color['r']['max']; $i++) {
    for($j = $color['g']['min']; $j <= $color['g']['max']; $j++) {
      for($k = $color['b']['min']; $k <= $color['b']['max']; $k++) {
        $colors[] = [$i, $j, $k];
      }
    }
  }
  if($verbose == 1) {
    echo "Possible colors generated: ".count($colors)."\n\n";
  }

  /**
   * Generate the random border color.
   */
  if($verbose == 1) {
    echo "Generated border color:\n";
  }
  $borderColor = array();
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
   * Delete the border color from the possible colors array.
   */
  if(isset($colors[array_search([$borderColor['r'], $borderColor['g'], $borderColor['b']], $colors, true)])) {
    unset($colors[array_search([$borderColor['r'], $borderColor['g'], $borderColor['b']], $colors, true)]);
    if($verbose == 1) {
      echo "Info: Border color deleted from possible colors array.\n\n";
    }
  }
} else {
  /**
   * "brightness" mode
   * 
   * Calculates the colors by brightness adjustments based on the reference color.
   */

  /**
   * User selected or random generated color
   */
  $r = $arg['r'];
  $g = $arg['g'];
  $b = $arg['b'];
  if($verbose == 1) {
    echo "Reference color:\nR: ".$r."\nG: ".$g."\nB: ".$b."\n\n";
  }

  $colors = array();

  /**
   * Generate color shades beyond the reference color.
   */
  for($i=$steps;$i>=1;$i--) {
    $r1 = round($r-($r/100*($i*$multiplicator)), 0);
    if($r1 < 0) {
      $r1 = 0;
    }
    $g1 = round($g-($g/100*($i*$multiplicator)), 0);
    if($g1 < 0) {
      $g1 = 0;
    }
    $b1 = round($b-($b/100*($i*$multiplicator)), 0);
    if($b1 < 0) {
      $b1 = 0;
    }
    $colors[] = [$r1, $g1, $b1];
  }

  /**
   * If there are no borders, the script can use the reference color in the color spectre.
   * Otherwise the reference color is used as $borderColor.
   */
  if($borderWidth == 0) {
    $colors[] = [$r, $g, $b];
  } else {
    if($verbose == 1) {
      echo "Border color: same as reference color.\nR: ".$r."\nG: ".$g."\nB: ".$b."\n\n";
    }
    $borderColor = ["r" => $r, "g" => $g, "b" => $b];
  }

  /**
   * Generate color shades above the reference color.
   */
  for($i=1;$i<=$steps;$i++) {
    $r1 = round($r+($r/100*($i*$multiplicator)), 0);
    if($r1 > 255) {
      $r1 = 255;
    }
    $g1 = round($g+($g/100*($i*$multiplicator)), 0);
    if($g1 > 255) {
      $g1 = 255;
    }
    $b1 = round($b+($b/100*($i*$multiplicator)), 0);
    if($b1 > 255) {
      $b1 = 255;
    }
    $colors[] = [$r1,$g1,$b1];
  }

  /**
   * It is possible by percentage calculation that there are duplicate values in the array. These are cleaned up here.
   */
  $colors = array_unique($colors, SORT_REGULAR);

  if($verbose == 1) {
    echo "Possible colors generated: ".count($colors)."\n\n";
  }
}

/**
 * Create image resource
 */
$im = imagecreatetruecolor($x, $y);

/**
 * Draw grid between tiles, if borderWidth > 0
 */
if($borderWidth > 0) {
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
}

/**
 * Draw the tiles
 */
$draw_x = 0;
$draw_y = 0;
while($draw_x < $x) {
  $draw_y = 0;
  while($draw_y < $y) {
    $randomColor = $colors[array_rand($colors)];
    $tileColor = imagecolorallocate($im, $randomColor[0], $randomColor[1], $randomColor[2]);
    imagefilledrectangle($im, $draw_x, $draw_y, $draw_x+$tileSize-1, $draw_y+$tileSize-1, $tileColor);
    $draw_y = $draw_y+$tileSize+$borderWidth;
  }
  $draw_x = $draw_x+$tileSize+$borderWidth;
}

/**
 * Save/Output the imagefile
 */
if(empty($filename)) {
  /**
   * If a valid hex value is provided, the filename will be generated above.
   */
  $filename = "noise_r".$arg['r']."-g".$arg['g']."-b".$arg['b']."-t".$tiles."-tS".$tileSize."-bW".$borderWidth."-m".($mode == "around" ? "A" : "B-mu".$multiplicator."-st".$steps)."_".md5(date("Y-m-d_H-i-s").microtime()).".png";
}
if($verbose == 1) {
  imagePNG($im, "./images/".$filename);
  echo "Output to:\n".realpath("./images/".$filename)."\n\n";
  echo "Please report bugs to:\nhttps://github.com/RundesBalli/php-noise/issues\n";
} else {
  if($json == 1) {
    imagePNG($im, "./images/".$filename);
    header('Content-Type: application/json');
    die(json_encode(array("uri" => "https://".$_SERVER['HTTP_HOST']."/images/".pathinfo($filename)['basename'])));
  } else {
    header('Content-Type: image/png');
    imagePNG($im);
  }
}
imagedestroy($im);
?>
