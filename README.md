#phpIDE


一个php版的在线网页开发工具
直接把IDE目录放到你的网站根目录, 就可以通过访问 IDE/index.html 来在线编辑你的网站代码.

在线IDE操作说明:
1. 点击文件 打开编辑.
2. 点击目录 进入目录.
3. 按住shift+鼠标点选文件 可以删除文件或目录.
4. 按住ctrl+鼠标点选文件 可以重命名和移动文件或目录.
5. 在编辑器界面, 按下ctrl+,可以调出编辑器设置.
6. 上传zip文件后, 点击可以在线解压.
7. /IDE/config.php文件, 可以配置根目录, 只读文件或列表, 隐藏文件或目录列表, 有效防止误操作.
具体文档可以到 http://phpide.now.sh/IDE/doc 中查看

注: 在线编辑器并没有做过多的权限验证, 请不要搞破坏, 会导致别人无法测试.

## 快速安装方法

```
php -r "copy('https://raw.githubusercontent.com/zhaishuaigan/ide/master/install.php', 'phpide-setup.php');"
php phpide-setup.php
php -r "unlink('phpide-setup.php');"
```