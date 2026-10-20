<?php

function generateIcon($size, $outputPath) {
    $img = imagecreatetruecolor($size, $size);
    imagesavealpha($img, true);
    
    // Background Dark Blue
    $bg = imagecolorallocate($img, 10, 17, 40); // #0A1128
    $yellow = imagecolorallocate($img, 250, 204, 21); // #FACC15
    $gold = imagecolorallocate($img, 234, 179, 8); // #EAB308
    $white = imagecolorallocate($img, 248, 250, 252);
    
    // Fill background
    imagefilledrectangle($img, 0, 0, $size, $size, $bg);
    
    // Shield / Frame
    $thickness = max(2, (int)($size * 0.04));
    imagesetthickness($img, $thickness);
    
    // Center Keylock Emblem
    $cx = $size / 2;
    $cy = $size / 2;
    $r = $size * 0.22;
    
    // Outer circle
    imageellipse($img, (int)$cx, (int)($cy - $size * 0.05), (int)($r * 2), (int)($r * 2), $yellow);
    // Inner filled keyhole
    imagefilledellipse($img, (int)$cx, (int)($cy - $size * 0.06), (int)($r * 0.6), (int)($r * 0.6), $gold);
    
    // Lower body of keyhole
    $kw = (int)($size * 0.08);
    $kh = (int)($size * 0.22);
    imagefilledrectangle($img, (int)($cx - $kw/2), (int)($cy - $size * 0.04), (int)($cx + $kw/2), (int)($cy + $kh), $gold);
    
    // Save PNG
    imagepng($img, $outputPath);
    imagedestroy($img);
}

$dir = __DIR__;
generateIcon(192, $dir . '/icon-192x192.png');
generateIcon(512, $dir . '/icon-512x512.png');
generateIcon(512, $dir . '/icon-maskable-512x512.png');
echo "Icons generated successfully!\n";
