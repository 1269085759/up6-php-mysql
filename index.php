<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>up6.3-mysql示例</title>
    <link href="js/up6.css" type="text/css" rel="Stylesheet"/>
    <script type="text/javascript" src="js/jquery-1.4.min.js"></script>
    <script type="text/javascript" src="js/json2.min.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/up6.config.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/up6.app.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/up6.edge.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/up6.file.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/up6.folder.js" charset="utf-8"></script>
    <script type="text/javascript" src="js/up6.js" charset="utf-8"></script>
    <script language="javascript" type="text/javascript">
    	var cbMgr = new HttpUploaderMgr();
		cbMgr.event.md5Complete = function (obj, md5) { /*alert(md5);*/ };
        cbMgr.event.fileComplete = function (obj) { /*alert(obj.pathSvr);*/ };
        cbMgr.Config["Cookie"] = 'PHPSESSID=<?php echo session_id() ?>';

    	$(function()
    	{
        	cbMgr.load_to("FilePanel");
            //上传指定文件
            $("#btnUpF").click(function () {
                var path = $("#filePath").val();
                cbMgr.app.addFile({ pathLoc: path });
            });
            //上传指定目录
            $("#btnUpFd").click(function () {
                var path = $("#folderPath").val();
                cbMgr.app.addFolder({ pathLoc: path });
            });
        });
    </script>
</head>
<body>
    <p>up6.3多文件上传演示页面</p>
    <p><a href="db/sql.php" target="_blank">初始化数据库</a>：用于创建数据表和存储过程，在首次使用时操作。需要先配置数据库连接信息。</p>
	<p><a target="_blank" href="db/clear.php">清空数据库</a></p>
    <p>
        文件路径：<input id="filePath" type="text" size="50" value="D:\\360safe-inst.exe" />&nbsp;
        <input id="btnUpF" type="button" value="上传本地文件" />
    </p>
    <p>
        目录路径：<input id="folderPath" type="text" size="50" value="C:\\Users\\Administrator\\Desktop\\test" />&nbsp;
        <input id="btnUpFd" type="button" value="上传本地目录" />
    </p>
	<div id="FilePanel"></div>
</body>
</html>
