<?php

/**
 * 本文件可以配置你不想让别人编辑的目录和文件
 */
return array(
    // 编辑根目录, 相对于admin.php的路径, 也可以是绝对路径
    'baseDir' => dirname(dirname(__FILE__)),
    // 只读文件列表
    'readOnly' => array(
        '/index.php',
        '/index.html',
        '/IDE',
        '/proxy_temp',
        '/client_body_temp',
        '/uwsgi_temp',
        '/vendor',
        '/.profile.d',
        '/fastcgi_temp',
        '/local',
        '/scgi_temp',
        '/README.md',
        '/boot.sh',
        '/status.html',
        '/.java-buildpack.log',
    ),
    // 隐藏文件列表
    'hidden' => array(
        '/proxy_temp',
        '/client_body_temp',
        '/uwsgi_temp',
        '/vendor',
        '/.profile.d',
        '/fastcgi_temp',
        '/local',
        '/scgi_temp',
        '/README.md',
        '/boot.sh',
        '/status.html',
        '/.java-buildpack.log',
    ),
);
