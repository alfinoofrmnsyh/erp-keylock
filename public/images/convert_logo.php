<?php

$width = 500;
$height = 380;
$img = imagecreatetruecolor($width, $height);
imagesavealpha($img, true);

// Transparent background
$transparent = imagecolorallocatealpha($img, 0, 0, 0, 127);
imagefill($img, 0, 0, $transparent);

// Colors: Grey #707276 and Yellow #F8E738
$grey = imagecolorallocate($img, 112, 114, 118);
$yellow = imagecolorallocate($img, 248, 231, 56);

// Left Stem
imagefilledrectangle($img, 94, 55, 166, 325, $grey);

// Upper arm polygon
$upperPoints = [
    188, 190,
    280, 55,
    362, 55,
    246, 208
];
imagefilledpolygon($img, $upperPoints, 4, $grey);

// Lower arm polygon
$lowerPoints = [
    215, 168,
    366, 325,
    282, 325,
    170, 205
];
imagefilledpolygon($img, $lowerPoints, 4, $grey);

// Yellow Dot Circle
imagefilledellipse($img, 420, 85, 82, 82, $yellow);

imagepng($img, __DIR__ . '/logo.png');
imagepng($img, __DIR__ . '/keylock-logo.png');

// Also update PWA icons with the new logo
function makePwaIcon($size, $outputPath, $grey, $yellow) {
    $pwa = imagecreatetruecolor($size, $size);
    $bg = imagecolorallocate($pwa, 255, 255, 255);
    imagefill($pwa, 0, 0, $bg);
    $scale = $size / 500;
    
    $pwaGrey = imagecolorallocate($pwa, 112, 114, 118);
    $pwaYellow = imagecolorallocate($pwa, 248, 231, 56);
    
    // Left stem
    imagefilledrectangle($pwa, (int)(94 * $scale), (int)(55 * $scale), (int)(166 * $scale), (int)(325 * $scale), $pwaGrey);
    
    // Upper
    $up = [
        (int)(188 * $scale), (int)(190 * $scale),
        (int)(280 * $scale), (int)(55 * $scale),
        (int)(362 * $scale), (int)(55 * $scale),
        (int)(246 * $scale), (int)(208 * $scale),
    ];
    imagefilledpolygon($pwa, $up, 4, $pwaGrey);
    
    // Lower
    $low = [
        (int)(215 * $scale), (int)(168 * $scale),
        (int)(366 * $scale), (int)(325 * $scale),
        (int)(282 * $scale), (int)(325 * $scale),
        (int)(170 * $scale), (int)(205 * $scale),
    ];
    imagefilledpolygon($pwa, $low, 4, $pwaGrey);
    
    // Yellow dot
    imagefilledellipse($pwa, (int)(420 * $scale), (int)(85 * $scale), (int)(82 * $scale), (int)(82 * $scale), $pwaYellow);
    
    imagepng($pwa, $outputPath);
    imagedestroy($pwa);
}

makePwaIcon(192, __DIR__ . '/icons/icon-192x192.png', $grey, $yellow);
makePwaIcon(512, __DIR__ . '/icons/icon-512x512.png', $grey, $yellow);
makePwaIcon(512, __DIR__ . '/icons/icon-maskable-512x512.png', $grey, $yellow);

echo "Official logo and PWA icons generated successfully!\n";
