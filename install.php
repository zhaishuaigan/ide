<?php
/**
 * 作用: 用于命令行安装代码到项目目录
 * @作者 翟帅干  zhaishuaigan@qq.com
 * 使用方法: php -r "copy('https://raw.githubusercontent.com/zhaishuaigan/ide/master/install.php', 'phpide-setup.php');"
 *          php phpide-setup.php
 *          php -r "unlink('phpide-setup.php');"
 */
if (PHP_SAPI !== 'cli') {
    die('此文件需要在cli下运行');
}

$url      = 'https://github.com/zhaishuaigan/ide/archive/master.zip';
$saveName = __DIR__ . '/ide.zip';
$extraDir = __DIR__;
copy($url, $saveName);
$zip = new ZipArchive();
if ($zip->open($saveName) !== true) {
    die('Could not open archive');
}
$zip->extractTo($extraDir);
$zip->close();
rename($extraDir . '/ide-master/IDE', $extraDir . '/ide');

array_map('unlink', glob($extraDir . '/ide-master/*/*.*'));
array_map('unlink', glob($extraDir . '/ide-master/*.*'));
rmdir($extraDir . '/ide-master/mini');
unlink($extraDir . '/ide-master/.gitignore');
unlink($extraDir . '/ide-master/Dockerfile');
rmdir($extraDir . '/ide-master');
unlink($saveName);
echo 'install ok';
echo "\n";
echo 'document: https://phpide.now.sh';