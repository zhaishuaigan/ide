<?php

/**
 * 作用: 核心处理文件, 用来处理文件列表获取, 修改, 删除, 等
 * @作者 翟帅干  zhaishuaigan@qq.com
 */
header("Content-Type:text/html; charset=utf-8");
$config = include 'config.php';
$ide = new Helper();
$config['baseDir'] = str_replace('\\', '/', $config['baseDir']);
$config['baseDir'] = str_replace('//', '/', $config['baseDir'] . '/');
$ide->baseDir = $config['baseDir'];
$act = I('act');

switch ($act) {
    case 'getDir':
        echo json_encode($ide->getDir(I('dir')));
        break;

    case 'getFile':
        echo $ide->getFile(I('path'));
        break;

    case 'saveFile':
        echo $ide->saveFile(I('path'), I('content'));
        break;

    case 'newFile':
        echo $ide->newFile(I('path'));
        break;

    case 'newDir':
        echo $ide->newDir(I('path'));
        break;

    case 'moveFile':
        echo $ide->moveFile(I('path'), I('newPath'));
        break;

    case 'moveDir':
        echo $ide->moveDir(I('path'), I('newPath'));
        break;

    case 'removeFile':
        echo $ide->removeFile(I('path'));
        break;

    case 'removeDir':
        echo $ide->removeDir(I('path'));
        break;

    case 'uploadFile':
        echo json_encode($ide->uploadFile('file', I('path')));
        break;

    case 'zipextract':
        echo $ide->zipextract(I('zip'), I('to'));
        break;
    default:
        echo '操作不存在!';
        break;
}

class Helper {

    // 管理代码的根目录
    public $baseDir = '../';

    /**
     * 获取指定目路径的子目录和文件
     * @param string $dir 要获取的目录名
     * @return array[][] 返回此目录中包含的子目录和文件 
     */
    public function getDir($dir) {
        $dirs = array();
        $files = array();
        $path = $dir;
        $dir = $this->baseDir . $dir . '/';
        $dir = $this->trimPath($dir);
        $handler = opendir($dir);
        while (($filename = readdir($handler)) !== false) {
            if ($filename != "." && $filename != ".." && !is_hidden($path . $filename)) {
                if (is_dir($dir . $filename)) {
                    $dirs[] = $filename;
                } else {
                    $files[] = $filename;
                }
            }
        }
        closedir($handler);
        $dir = $this->trimPath($dir);
        $dir = str_replace($this->baseDir, '/', $dir);
        return array(
            'path' => $dir,
            'dirs' => $dirs,
            'files' => $files
        );
    }

    /**
     * 作用: 保存文件
     * @param string $path 文件路径
     * @param string $conent 文件内容
     * @return int [1: 保存成功, 0:保存失败]
     */
    public function saveFile($path, $conent) {
        checkpath($path);
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        $isBOM = checkBOM($path);
        if ($isBOM) {
            writeUTF8WithBOMFile($path, $conent);
            return 1;
        } else {
            file_put_contents($path, $conent);
            return 1;
        }
        return 0;
    }

    /**
     * 作用: 新建文件
     * @param string $path 文件名
     * @return string [成功返回ok, 失败返回错误信息]
     */
    public function newFile($path) {
        checkpath($path);
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        if (file_exists($path)) {
            return '文件已存在!';
        }
        if (!is_dir(dirname($path))) {
            return '目录不存在';
        }
        file_put_contents($path, '');
        return 'ok';
    }

    /**
     * 新建目录
     * @param string $path 新目录名
     * @return string [成功返回ok, 失败返回错误信息]
     */
    public function newDir($path) {
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        if (!is_dir(dirname($path))) {
            return '不支持多级创建目录!';
        }
        if (is_dir($path)) {
            return '目录已存在!';
        }
        mkdir($path);
        chmod($path, 0777);
        return 'ok';
    }

    /**
     * 作用: 获取文件源代码
     * @param string $path 文件路径
     * @return text 文件内容
     */
    public function getFile($path) {
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        return file_get_contents($path);
    }

    /**
     * 作用: 移动文件
     * @param string $path 要移动的文件
     * @param string $newPath 移动到目录或文件
     * @return string [成功返回ok, 失败返回详细信息]
     */
    public function moveFile($path, $newPath) {
        checkpath($path);
        checkpath($newPath);
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        $newPath = $this->baseDir . $newPath;
        $newPath = $this->trimPath($newPath);
        if (rename($path, $newPath)) {
            return 'ok';
        } else {
            return '没有权限';
        }
    }

    /**
     * 作用: 移动文件夹
     * @param string $path 要移动的目录
     * @param string $newPath 新目录
     * @return string [成功返回ok, 失败返回详细信息]
     */
    public function moveDir($path, $newPath) {
        checkpath($path);
        checkpath($newPath);
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        $newPath = $this->baseDir . $newPath;
        $newPath = $this->trimPath($newPath);
        if (rename($path, $newPath)) {
            return 'ok';
        } else {
            return '没有权限';
        }
    }

    /**
     * 作用: 删除文件
     * @param string $path 要删除的文件
     * @return string [成功返回ok, 失败返回详细信息]
     */
    public function removeFile($path) {
        checkpath($path);
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        return unlink($path);
    }

    /**
     * 作用: 删除文件夹
     * @param string $path 要删除的目录
     * @return string [成功返回ok, 失败返回详细信息]
     */
    public function removeDir($path) {
        checkpath($path);
        $path = $this->baseDir . $path;
        $path = $this->trimPath($path);
        rrmdir($path);
        return 'ok';
    }

    /**
     * 作用: 将相对于根的路径转换为相对于本文件或baseDir的路径
     * @param string $path 要转换的路径
     * @return string 相对于本文件或baseDir的路径
     */
    public function trimPath($path) {
        $path = str_replace('\\', '/', $path);
        $path = str_replace('//', '/', $path);
        $path = str_replace('/./', '/', $path);
        return $path;
    }

    /**
     * 作用: 上传文件
     * @param string $fileElementName 表单文件域的名称
     * @param string $path 上传到的目录 
     * @return array 包括error和msg字段
     */
    public function uploadFile($fileElementName, $path) {
        $error = "";
        $msg = "";
        if (!empty($_FILES[$fileElementName]['error'])) {
            switch ($_FILES[$fileElementName]['error']) {
                case '1':
                    $error = '上传文件超过php.ini中upload_max_filesize的设置。';
                    break;

                case '2':
                    $error = '上传文件超过HTML表单中指定的MAX_FILE_SIZE。';
                    break;

                case '3':
                    $error = '上传文件只是部分上传。';
                    break;

                case '4':
                    $error = '没有上传文件。';
                    break;

                case '6':
                    $error = '缺少一个临时文件夹。';
                    break;

                case '7':
                    $error = '未能将文件写入磁盘。';
                    break;

                case '8':
                    $error = 'File upload stopped by extension';
                    break;

                case '999':
                default:
                    $error = '没有错误代码的倍率。';
            }
        } elseif (empty($_FILES[$fileElementName]['tmp_name']) || $_FILES[$fileElementName]['tmp_name'] == 'none') {
            $error = '没有文件上传。';
        } else {
            $msg .= ' 文件名: ' . $_FILES[$fileElementName]['name'] . ', ';
            $msg .= ' 文件大小: ' . @filesize($_FILES[$fileElementName]['tmp_name']);
            $path = $this->baseDir . $path;
            $path = $this->trimPath($path);
            move_uploaded_file($_FILES[$fileElementName]['tmp_name'], $path . $_FILES[$fileElementName]['name']);
            chmod($path . $_FILES[$fileElementName]['name'], 0777);
        }
        return array('error' => $error, 'msg' => $msg);
    }

    /**
     * 作用: 解压文件
     * @param string $zip zip文件路径
     * @param string $to 解压到目录
     */
    public function zipextract($zip, $to) {
        $zip = $this->baseDir . $zip;
        $zip = $this->trimPath($zip);
        $to = $this->baseDir . $to;
        $to = $this->trimPath($to);
        $zipobj = new ZipArchive;
        $res = $zipobj->open($zip);
        if ($res === TRUE) {
            echo 'ok';
            $zipobj->extractTo($to);
            $zipobj->close();
        } else {
            echo '解压失败: ' . $res;
        }
    }

}

/**
 * 作用: 检测文件或目录是否允许写如[这里检测的是配置文件中的路径]
 * @param string $path 要检测的路径
 */
function checkpath($path) {
    $config = include 'config.php';
    foreach ($config['readOnly'] as $val) {
        if (substr($path, 0, strlen($val)) === $val) {
            die('禁止访问: ' . $path);
        }
    }
}

/**
 * 作用: 检测文件或目录是否显示[这里检测的是配置文件中的路径]
 * @param type $path
 * @return boolean
 */
function is_hidden($path) {
    $config = include 'config.php';
    foreach ($config['hidden'] as $val) {
        if (substr($path, 0, strlen($val)) === $val) {
            return true;
        }
    }
    return false;
}

/**
 * 作用: 写入包含bom头信息的文件
 * @param type $filename 要写入的文件名
 * @param type $content 要写入的内容
 */
function writeUTF8WithBOMFile($filename, $content) {
    $f = fopen($filename, 'w');
    fwrite($f, pack("CCC", 0xef, 0xbb, 0xbf));
    fwrite($f, $content);
    fclose($f);
}

/**
 * 作用: 检测文件是否包含bom头信息
 * @param string $filename 要检测的文件
 * @return boolean 如果包含返回true, 否则返回false
 */
function checkBOM($filename) {
    $contents = file_get_contents($filename);
    $charset[1] = substr($contents, 0, 1);
    $charset[2] = substr($contents, 1, 1);
    $charset[3] = substr($contents, 2, 1);
    if (ord($charset[1]) == 239 && ord($charset[2]) == 187 && ord($charset[3]) == 191) {
        return true;
    } else {
        return false;
    }
}

/**
 * 作用: 删除非空目录
 * @param string $dir 要删除的目录
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != '.' && $object != '..') {
                if (filetype($dir . '/' . $object) == 'dir') {
                    rrmdir($dir . '/' . $object);
                } else {
                    unlink($dir . '/' . $object);
                }
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

/**
 * 作用: 接收参数
 * @param string $name 参数名, get或post
 */
function I($name) {
    if (isset($_GET[$name])) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($_GET[$name]);
        } else {
            return $_GET[$name];
        }
    }
    if (isset($_POST[$name])) {
        if (get_magic_quotes_gpc()) {
            return stripslashes($_POST[$name]);
        } else {
            return $_POST[$name];
        }
    }
    return null;
}

/* 文件结束 */