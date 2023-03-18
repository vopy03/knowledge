<?php
	$pdo = new PDO('mysql:host=localhost; dbname=knowledge; charset=utf8', 'root', ''); // Приєднання до БД
	session_start(); // початок сесії користувача.

	date_default_timezone_set('Europe/Kiev');

function resizePicNSave($key, $picPath, $width, $height, $endPath) {
require_once("tinify/lib/Tinify/Exception.php");
require_once("tinify/lib/Tinify/ResultMeta.php");
require_once("tinify/lib/Tinify/Result.php");
require_once("tinify/lib/Tinify/Source.php");
require_once("tinify/lib/Tinify/Client.php");
require_once("tinify/lib/Tinify.php");

	\Tinify\setKey($key);
	$source = \Tinify\fromFile($picPath);
	$resized = $source->resize(array(
    "method" => 'thumb',
    "width" => $width,
    "height" => $height
	));
	$resized->toFile($endPath);
}

	if (!function_exists('str_contains')) {
    function str_contains(string $haystack, string $needle): bool
    {
        return '' === $needle || false !== strpos($haystack, $needle);
    }
	}
function remDir($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!remDir($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }

    }

    return rmdir($dir);
}
?>