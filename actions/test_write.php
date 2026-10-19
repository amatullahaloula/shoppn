<?php
$dir = __DIR__ . '/../images/products/';
echo 'Resolved: ' . realpath($dir) . '<br>';
echo 'Writable: ' . (is_writable($dir) ? 'yes' : 'no') . '<br>';

$ok = @file_put_contents($dir . 'test.txt', 'hello');
echo 'Write test: ' . ($ok !== false ? 'worked' : 'FAILED') . '<br>';
if ($ok === false) { print_r(error_get_last()); }

echo 'tmp dir: ' . sys_get_temp_dir() . ' writable: ' . (is_writable(sys_get_temp_dir()) ? 'yes' : 'no') . '<br>';
echo 'file_uploads: ' . ini_get('file_uploads') . '<br>';
echo 'upload_max_filesize: ' . ini_get('upload_max_filesize') . ', post_max_size: ' . ini_get('post_max_size') . '<br>';
?>