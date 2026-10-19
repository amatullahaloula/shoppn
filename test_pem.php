<?php
echo 'PHP runs as: ' . get_current_user() . ' / uid ' . (function_exists('posix_geteuid') ? posix_geteuid() : 'n/a') . '<br>';

$dir = __DIR__ . '/../images/products/';
echo 'Dir exists: ' . (is_dir($dir) ? 'yes' : 'no') . '<br>';
echo 'Dir writable: ' . (is_writable($dir) ? 'yes' : 'no') . '<br>';
echo 'Dir perms: ' . substr(sprintf('%o', @fileperms($dir)), -4) . '<br>';
echo 'Dir owner uid: ' . @fileowner($dir) . '<br>';
echo 'upload_tmp_dir: ' . (ini_get('upload_tmp_dir') ?: sys_get_temp_dir()) . '<br>';
echo 'open_basedir: ' . (ini_get('open_basedir') ?: 'none') . '<br>';

?>