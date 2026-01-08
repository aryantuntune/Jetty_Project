<?php
/**
 * Script to download all images from carferry.in
 * Run this once to get all the assets
 *
 * Usage: php download_carferry_images.php
 */

$targetDir = __DIR__ . '/public/images/carferry/';

// Create subdirectories
$subdirs = ['logos', 'routes', 'timetables', 'ratecards', 'backgrounds', 'misc'];
foreach ($subdirs as $dir) {
    if (!is_dir($targetDir . $dir)) {
        mkdir($targetDir . $dir, 0755, true);
    }
}

// All images to download from carferry.in
$images = [
    // Logos
    'logos/logo.png' => 'https://carferry.in/wp-content/uploads/2021/03/224x150xlogo.png',
    'logos/logo-white.png' => 'https://carferry.in/wp-content/uploads/2021/03/LOGO-White-224x150-1.png',

    // Route Thumbnails
    'routes/dabhol-dhopave.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Dabhol-Dhopave-Thumb.jpg',
    'routes/jaigad-tawsal.jpg' => 'https://carferry.in/wp-content/uploads/2022/04/Mhapral-ferrry-page-image.jpg',
    'routes/dighi-agardande.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Dighi-Agardande-Thumb.jpg',
    'routes/veshvi-bagmandale.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Veshvi-Bagmandale-Thumb.jpg',
    'routes/vasai-bhayander.jpg' => 'https://carferry.in/wp-content/uploads/2024/03/Vasai-Bhayander-Ferry-Service-Card-Image.jpg',
    'routes/ambet-mahpral.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Ambet-Mahpral-Thumb.jpg',
    'routes/aarohi-ferry.png' => 'https://carferry.in/wp-content/uploads/2025/04/AAROHI-640x480-1.png',

    // Timetables
    'timetables/dabhol-dhopave.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Dabhol-Dhopave-timetable-1.jpg',
    'timetables/jaigad-tawsal.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Jaigad-tawsal-timetable.jpg',

    // Rate Cards
    'ratecards/dabhol.jpg' => 'https://carferry.in/wp-content/uploads/2022/04/Dabhol-pdf.jpg',
    'ratecards/jaigad.jpg' => 'https://carferry.in/wp-content/uploads/2022/04/Jaigad-pdf.jpg',

    // Background Images
    'backgrounds/water-ripples.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/water-ripples2.jpg',
    'backgrounds/transportation-services.png' => 'https://carferry.in/wp-content/uploads/2021/03/transportation-services-19.png',
    'backgrounds/cruise-services.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/cruise_services3-825x400-1.jpg',
    'backgrounds/inland-services.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/inland_services.jpg',

    // Misc
    'misc/map.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Map-for-web.jpg',
    'misc/team-photo.jpg' => 'https://carferry.in/wp-content/uploads/2021/03/Yogesh-with-Bhai.jpg',
];

echo "Starting download of carferry.in images...\n\n";

$success = 0;
$failed = 0;

foreach ($images as $localPath => $url) {
    $fullPath = $targetDir . $localPath;

    echo "Downloading: $url\n";
    echo "  -> $localPath ... ";

    // Download the file
    $context = stream_context_create([
        'http' => [
            'timeout' => 30,
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);

    $content = @file_get_contents($url, false, $context);

    if ($content !== false) {
        file_put_contents($fullPath, $content);
        echo "OK (" . strlen($content) . " bytes)\n";
        $success++;
    } else {
        echo "FAILED\n";
        $failed++;
    }
}

echo "\n========================================\n";
echo "Download complete!\n";
echo "Success: $success\n";
echo "Failed: $failed\n";
echo "========================================\n";
echo "\nImages saved to: $targetDir\n";
