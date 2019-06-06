<?php

/**
 * 本文件可以配置你不想让别人编辑的目录和文件
 */
return array(
    // 编辑根目录, 相对于admin.php的路径, 也可以是绝对路径
    'baseDir' => dirname(dirname(__FILE__)),
    // 只读文件列表
    'readOnly' => array(
    ),
    // 隐藏文件列表
    'hidden' => array(
    ),
);
