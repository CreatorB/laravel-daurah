<?php
$filename = $_GET['f'] ?? '';
if (empty($filename) || strpos($filename, '..') !== false) {
    http_response_code(400);
    exit('Invalid file');
}

$file = __DIR__ . '/storage/bukti_undangan/' . $filename;
if (!file_exists($file)) {
    http_response_code(404);
    exit('File not found');
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file);
finfo_close($finfo);

header('Content-Type: ' . $mimeType);
header('Content-Length: ' . filesize($file));
header('Cache-Control: no-cache, private');
header('X-Content-Type-Options: nosniff');

readfile($file);
exit;
