<?php

/**
 * Script untuk generate placeholder images untuk menu
 * Jalankan: php database/seeders/GenerateMenuImages.php
 */

$menuImages = [
    'P001' => ['Kopi Susu Gula Aren', '#6B3A2A'],
    'P002' => ['Roti Bakar Cokelat', '#8B4513'],
    'P003' => ['Air Mineral 600ml', '#2196F3'],
    'P004' => ['Nasi Goreng Spesial', '#FF5722'],
    'P005' => ['Mie Ayam Bakso', '#FF9800'],
    'P006' => ['Es Teh Manis', '#4CAF50'],
    'P007' => ['Jus Alpukat', '#7CB342'],
    'P008' => ['Kentang Goreng', '#FFC107'],
    'P009' => ['Pisang Goreng', '#FFD54F'],
    'P010' => ['Cappuccino', '#795548'],
    'P011' => ['Matcha Latte', '#8BC34A'],
    'P012' => ['Croissant', '#D4A574'],
    'P013' => ['Salad Buah', '#FF8A65'],
    'P014' => ['French Fries', '#FFA000'],
    'P015' => ['Sosis Bakar', '#D32F2F'],
    'P016' => ['Donat Glazed', '#E91E63'],
    'P017' => ['Milkshake Strawberry', '#F06292'],
    'P018' => ['Teh Tarik', '#A1887F'],
    'P019' => ['Mochi Ice Cream', '#CE93D8'],
    'P020' => ['Espresso Doppio', '#3E2723'],
];

$outputDir = __DIR__ . '/../../public/images/menu';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

foreach ($menuImages as $id => [$name, $color]) {
    $filename = strtolower(str_replace(' ', '_', $name)) . '.jpg';
    $filepath = $outputDir . '/' . $filename;

    // Create image
    $img = imagecreatetruecolor(400, 300);

    // Parse hex color
    $r = hexdec(substr($color, 1, 2));
    $g = hexdec(substr($color, 3, 2));
    $b = hexdec(substr($color, 5, 2));

    $bgColor = imagecolorallocate($img, $r, $g, $b);
    $textColor = imagecolorallocate($img, 255, 255, 255);
    $shadowColor = imagecolorallocatealpha($img, 0, 0, 0, 60);

    // Fill background
    imagefill($img, 0, 0, $bgColor);

    // Add some gradient effect
    for ($i = 0; $i < 300; $i++) {
        $alpha = 40 - ($i / 300) * 40;
        $lineColor = imagecolorallocatealpha($img, 255, 255, 255, $alpha);
        imageline($img, 0, $i, 400, $i, $lineColor);
    }

    // Draw plate/circle
    $circleColor = imagecolorallocatealpha($img, 255, 255, 255, 30);
    imagefilledellipse($img, 200, 130, 180, 180, $circleColor);

    // Draw fork and knife icons (simple lines)
    $iconColor = imagecolorallocatealpha($img, 255, 255, 255, 50);
    // Fork
    imageline($img, 150, 80, 150, 180, $iconColor);
    imageline($img, 140, 80, 150, 80, $iconColor);
    imageline($img, 145, 70, 150, 80, $iconColor);
    imageline($img, 150, 80, 155, 70, $iconColor);
    // Knife
    imageline($img, 250, 80, 250, 180, $iconColor);
    imageline($img, 250, 80, 260, 100, $iconColor);

    // Add text shadow
    $fontSize = 5;
    $text = $name;
    $textWidth = strlen($text) * imagefontwidth($fontSize);
    $x = (400 - $textWidth) / 2;
    $y = 230;

    imagestring($img, $fontSize, $x + 1, $y + 1, $text, $shadowColor);
    imagestring($img, $fontSize, $x, $y, $text, $textColor);

    // Save as JPEG
    imagejpeg($img, $filepath, 85);
    imagedestroy($img);

    echo "Generated: $filename ($name)\n";
}

echo "\n✓ Semua " . count($menuImages) . " gambar menu berhasil dibuat!\n";