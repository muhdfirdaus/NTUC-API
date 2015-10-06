<?php
/**
 * @author Paris Qian Sen <paris.qian@misatravel.com>
 * To redirect non-static files to index.php when using PHP built-in Server
 */
if (preg_match('/\.(?:png|jpg|jpeg|gif)$/', $_SERVER["REQUEST_URI"])) {
    return false;
} else {
    include __DIR__ . '/index.php';
}