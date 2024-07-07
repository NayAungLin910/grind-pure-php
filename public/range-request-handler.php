<?php

session_start();

if (!isset($_SESSION['auth']['id'])) {
    header("HTTP/1.1 405 Method Not Allowed");
    exit;
}

$file = '.' . $_GET['path'];

if (!file_exists("." . $pathToFile)) {
    header("HTTP/1.1 404 Not Found");
    exit;
}

// Get the file size
$size = filesize($file);

// Check if the client requested a specific range
$range = isset($_SERVER['HTTP_RANGE']) ? $_SERVER['HTTP_RANGE'] : null;

if ($range) {
    // Example: 'bytes=0-499'   
    list($unit, $range) = explode('=', $range, 2);
    if ($unit != 'bytes') {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        exit;
    }

    // Example: '0-499'
    list($start, $end) = explode('-', $range);
    $start = intval($start);
    $end = $end === '' ? ($size - 1) : intval($end);

    if ($start > $end || $end >= $size) {
        header('HTTP/1.1 416 Requested Range Not Satisfiable');
        exit;
    }

    // Calculate the length of the requested range
    $length = $end - $start + 1;

    // Send headers for partial content
    header('HTTP/1.1 206 Partial Content');
    header("Content-Type: video/mp4");
    header("Content-Length: $length");
    header("Content-Range: bytes $start-$end/$size");

    // Open the file and output the specified range
    $file = fopen($file, 'rb');
    fseek($file, $start);
    $bufferSize = 1024 * 8;
    while (!feof($file) && ($pos = ftell($file)) <= $end) {
        if ($pos + $bufferSize > $end) {
            $bufferSize = $end - $pos + 1;
        }
        echo fread($file, $bufferSize);
        flush();
    }
    fclose($file);
} else {
    // Send headers for full content
    header("Content-Type: video/mp4");
    header("Content-Length: $size");
    readfile($file);
}
