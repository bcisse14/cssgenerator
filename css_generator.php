<?php
if (isset($argv[1]) == false) {
    echo "Enter a valid folder \n";
}
function recursive($path, &$list, $arg)
{
    if (is_dir($path) == true) {
        if ($dh = opendir($path)) {
            while (($file = readdir($dh)) !== false) {
                if ($file != "." && $file != "..") {
                    $path2 = $path . "/" . $file;
                    if (str_ends_with($path2, ".png")) {
                        $list[] = $path2;
                    } elseif (is_dir($path2) && $arg == "-r" || is_dir($path2) && $arg == "--recursive") {
                        recursive($path2, $list, $arg);
                    }
                }
            }
        }
    }
}
$name = "";
$iteration = -1;
$state = false;
$style = "style.css";
$sprite = "sprite.png";
$path = "";
array_shift($argv);
$path = end($argv);
$list = [];
$padding = 0;
foreach ($argv as $arg) {
    $iteration += 1;
    if ($arg == "-i") {
        $sprite = $argv[$iteration + 1];
    }
    if (str_starts_with($arg, "--output-image=")) {
        $str = explode('=', $arg);
        $sprite = $str[1];
    }
    if ($arg == "-s") {
        $style = $argv[$iteration + 1];
    }
    if (str_starts_with($arg, "--output-style=")) {
        $str = explode('=', $arg);
        $style = $str[1];
    }
    if ($arg == "-r" || $arg == "--recursive") {
        $state = true;
        $name = $sprite;
    }
}
if ($state == true) {
    recursive($path, $list, "-r");
    test($list, $sprite, $style);
}
if ($state == false) {
    recursive($path, $list, "");
    test($list, $sprite, $style);
}


function test($list, $name, $style)
{
    $height = 0;
    $width = 0;
    $g = 0;
    $h = 0;

    foreach ($list as $file) {
        $DIR = getcwd();
        $filename = $DIR . "/" . $file;
        list($w, $h) = getimagesize($filename);
        $width += $w;
        if ($h > $height)
            $height = $h;
    }
    $dest = imagecreatetruecolor($width, $height);
    $i = imagecolorallocatealpha($dest, 0, 0, 0, 127);
    imagefill($dest, 0, 0, $i);
    imagealphablending($dest, false);
    imagesavealpha($dest, true);
    foreach ($list as $file) {
        list($w, $h) = getimagesize($file);
        $a = imagecreatefrompng($file);
        imagecopy($dest, $a, $g, 0, 0, 0, $w, $h);
        file_put_contents("$style", ".$file {background-position:$g px, 0 px; width: $w px; height:$h px} \n", FILE_APPEND);
        $g += $w;
    }
    imagepng($dest, $name);
    imagedestroy($dest);
}
