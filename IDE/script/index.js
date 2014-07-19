// 获取指定目录的文件列表
function getDir(dir) {
	$.ajax({
		type : "get",
		dataType : "json",
		url : $('#admin').val(),
		data : 'a=getDir&dir=' + dir,
		success : function(data) {
			refreshList(data);
		}
	});
}
// 刷新文件列表
function refreshList(data) {
	if ($("#selPath").val() != data.path) {
		$("#selPath").append(
				'<option value="' + data.path + '">' + data.path + '</option>');
		$("#selPath").val(data.path);
        document.title = data.path.length > 20 ? '...' + data.path.substr(-20) : data.path;
	}
	var dirs = [];
	var files = [];
	for (var i = 0; i < data.dirs.length; i++) {
		dirs.push('<li class="dir_close"><div><a href="javascript:void(0);">'
				+ data.dirs[i] + '</a></div></li>');
	}
	for (var i = 0; i < data.files.length; i++) {
		files.push('<li class="file"><div><a href="javascript:void(0);">'
				+ data.files[i] + '</a></div></li>');

	}
	$('.tree').html('<ul>' + dirs.join('') + files.join('') + '</ul>');
	$('.tree .dir_close').click(function(e) {
		if (e.shiftKey) {
			removeDir($('#selPath').val() + $(this).text());
		} else if (e.ctrlKey) {
			moveDir($('#selPath').val() + $(this).text());
		} else {
			getDir($('#selPath').val() + $(this).text());
		}
		return false;
	});
	$('.tree .file').click(function(e) {
		if (e.shiftKey) {
			removeFile($('#selPath').val() + $(this).text());
		} else if (e.ctrlKey) {
			moveFile($('#selPath').val() + $(this).text());
		} else {
            if (/zip$/.test($(this).text())) {
            	zip($('#selPath').val() + $(this).text());
            } else {
				getFile($('#selPath').val() + $(this).text());
            }
		}
		return false;
	});
}
// 删除文件
function removeFile(path) {
	if (confirm("确认删除这个文件吗?\n" + path)) {
		$.get($('#admin').val() + '?a=removeFile&path=' + path, function(msg) {
			getDir($('#selPath').val());
		});
	}
}
// 删除目录
function removeDir(path) {
	if (confirm("确认删除这个目录吗?\n" + path)) {
		$.get($('#admin').val() + '?a=removeDir&path=' + path, function(msg) {
			getDir($('#selPath').val());
		});
	}
}
// 获取文件并调用创建新标签页
function getFile(path) {
	path = path.replace('//', '/');
	var id = path.replace(/\//g, '_');
	id = id.replace(/\./g, '_');
	if ($('#' + id).length > 0) {
		changeFile(id);
		return;
	}
	var modes = [ {
		ext : 'php',
		mode : 'php'
	}, {
		ext : 'txt',
		mode : 'text'
	}, {
		ext : 'js',
		mode : 'javascript'
	}, {
		ext : 'css',
		mode : 'css'
	}, {
		ext : 'html',
		mode : 'html'
	}, {
		ext : 'xml',
		mode : 'xml'
	}, {
		ext : 'conf',
		mode : 'text'
	}, {
		ext : 'config',
		mode : 'xml'
	}, {
		ext : 'asp',
		mode : 'vbscript'
	}, {
		ext : 'aspx',
		mode : 'csharp'
	}, {
		ext : 'cs',
		mode : 'csharp'
	}, {
		ext : 'ashx',
		mode : 'csharp'
	}, {
		ext : 'htaccess',
		mode : 'text'
	}, {
		ext : 'ini',
		mode : 'ini'
	}, {
		ext : 'sql',
		mode : 'pgsql'
	} ];
	var mode = '';
	for ( var m in modes) {
		if (id.substr(id.length - modes[m].ext.length).toLowerCase() == modes[m].ext) {
			mode = modes[m].mode;
			break;
		}
	}
	if (mode == '') {
		// window.open();
        mode = 'text';
		//return;
	}

	var url = $('#admin').val() + '?a=getFile&path=' + path;
	$.get(url, function(contents) {
		newTab(id, path, mode, contents)
	});
}
function newTab(id, path, mode, contents) {
	var title = path.replace(/.*\//, '');
	var tabTitle = $('<li class="file" id="' + id + '" title=' + path + '><span>' + title
			+ '</span> <a href="javascript:void(0);"></a></li>');
	var tabContent = $('<pre class="content" id="' + id + '_contents"></pre>');
	$('.titles').append(tabTitle);
	tabTitle.click(function() {
		changeFile(id);
	});
	tabTitle.attr('path', path);
	$('.tabContent').append(tabContent);
	$('a', tabTitle).click(function() {
		tabTitle.hide(500, function() {
			if (tabTitle.hasClass('sel')) {
				if (tabTitle.prev().length >= 1) {
					tabTitle.prev().click();
				} else if (tabTitle.next().length >= 1) {
					tabTitle.next().click();
                } else {
                	$('.content').show();
                }
			}
			tabTitle.remove();
			tabContent.remove();
		})
		return false;
	});
	window.editor = window.editor ? window.editor : {};
	window.editor[id] = ace.edit(id + '_contents');
	window.editor[id].setTheme('ace/theme/eclipse');
	window.editor[id].getSession().setMode('ace/mode/' + mode);
	window.editor[id].setFontSize('18px');
	window.editor[id].setValue(contents);
	window.editor[id].scrollToRow(0);
	window.editor[id].clearSelection();
	window.editor[id].on('change', function() {
		tabTitle.css('color', '#F00');
	})
	changeFile(id);
    
    $('.titles:first').sortable()
    //$(".titles:first").dragsort({ dragSelector: "li", dragEnd: function() { }, dragBetween: false, placeHolderTemplate: "<li></li>" });
}
// 切换到指定的文件进行编辑
function changeFile(id) {
	window.selFile = id;
	$('.content').hide();
	$('#' + id + '_contents').show();
	$('.titles .file').removeClass('sel');
	$('#' + id).addClass("sel");
}
// 保存文件
function saveFile() {
    var selFile = window.selFile;
	if ($('#' + selFile).length == 0) {
		return;
	}
	var data = {
		path : $('#' + selFile).attr('path'),
		content : window.editor[selFile].getValue()
	};
	$.post($('#admin').val() + '?a=saveFile', data, function(msg) {
		// alert(msg);
		$('#' + selFile).css('color', '');
	});
}

function saveAllFile(){
    var sel = $('.titles .sel');
    $('.titles li').each(function () {
    	$(this).click();
        saveFile();
    });
    sel.click();
}

// 新建文件
function newFile() {
	var fileName = prompt('请输入文件名', $('#selPath').val() + 'newFile.php');
	if (fileName) {
		var url = $('#admin').val() + '?a=newFile&path=' + fileName;
		$.get(url, function(msg) {
			if (msg != 'ok') {
				alert(msg);
			} else {
				getDir($('#selPath').val());
				getFile(fileName);
			}
		});
	}
}
// 新建文件夹
function newDir() {
	var dirName = prompt('请输入目录名', $('#selPath').val() + 'newDir');
	if (dirName) {
		var url = $('#admin').val() + '?a=newDir&path=' + dirName;
		$.get(url, function(msg) {
			if (msg != 'ok') {
				alert(msg);
			} else {
				getDir($('#selPath').val());
			}
		});
	}
}
// 重命名或移动文件
function moveFile(oldName) {
	var newName = prompt('请输入新文件名', oldName);
	if (newName) {
		var url = $('#admin').val() + '?a=moveFile&path=' + oldName
				+ '&newPath=' + newName;
		$.get(url, function(msg) {
			if (msg != 'ok') {
				alert(msg);
			} else {
				getDir($('#selPath').val());
			}
		});
	}
}
// 重命名或移动目录
function moveDir(oldName) {
	var newName = prompt('请输入新目录名', oldName);
	if (newName) {
		var url = $('#admin').val() + '?a=moveDir&path=' + oldName
				+ '&newPath=' + newName;
		$.get(url, function(msg) {
			if (msg != 'ok') {
				alert(msg);
			} else {
				getDir($('#selPath').val());
			}
		});
	}
}
// 上传文件
function upload() {
	$.ajaxFileUpload({
		url : $('#admin').val() + '?a=uploadFile&path=' + $('#selPath').val(),
		secureuri : false,
		fileElementId : 'file',
		dataType : 'json',
		data : {
			path : $('#selPath').val()
		},
		success : function(data, status) {
			if (typeof (data.error) != 'undefined') {
				if (data.error != '') {
					alert(data.error);
				} else {
					// alert(data.msg);
					getDir($('#selPath').val());
				}
			}
		},
		error : function(data, status, e) {
			alert(e);
		}
	});
}
// 解压zip
function zip(zip) {
	var to = prompt('请输入解压到的目录', $('#selPath').val());
    if (to) {
    	url = $('#admin').val() + '?a=zipextract&zip=' + zip + '&to=' + to;
        $.get(url, function(msg) {
			if (msg != 'ok') {
				alert(msg);
			} else {
				getDir($('#selPath').val());
			}
		});
    }
}

// 加载事件
$(function() {
	$('#show_hide_left').click(function() {
		if ($('.left').is(":hidden")) {
			$(this).html('《');
			$('.left').show();
			$('.right').css('left', window.rightLeft);
		} else {
			$(this).html('》');
			$('.left').hide();
			window.rightLeft = $('.right').css('left');
			$('.right').css('left', 0);
		}
	});
	$('#title_left').click(function() {
		$('.titles .file:hidden:last').show(500);
	});
	$('#title_right').click(function() {
		$('.titles .file:visible:first').hide(500);
	});
	$(window).keydown(function(e) {
		if (e.keyCode == 83 && e.ctrlKey) {
			e.preventDefault();
			saveFile();
		}
	});
    $('.btn_save').click(function(){
    	saveFile();
    });
    
    $('.btn_saveall').click(function(){
    	saveAllFile();
    });
    $('.btn_closeall').click(function(){
    	$('.titles a').click();
    });
    
	$('#newFile').click(newFile);
	$('#newDir').click(newDir);
	getDir('');
	$("#selPath").change(function() {
		getDir($(this).val());
		while ($("#selPath option:last").val() != $(this).val()) {
			$("#selPath option:last").remove();
		}
		$("#selPath option:last").remove();
	});
	$('#upFile').click(function() {
		$('.upload').show();
	});
	$('#btn_close').click(function() {
		$('.upload').hide();
	});
	$('#btn_upload').click(function() {
		upload();
		$('.upload').hide();
	});
});
