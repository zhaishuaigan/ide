<?php
header("Content-Type:text/html; charset=utf-8");
$index = file_get_contents('http://phpide.coding.io/IDE/admin.php?a=getFile&path=/IDE/index.html');
$admin = file_get_contents('http://phpide.coding.io/IDE/admin.php?a=getFile&path=/IDE/admin.php');
file_put_contents('index.html', $index);
file_put_contents('admin.php', $admin);
echo '更新成功, <a href="index.html">打开编辑器</a>';