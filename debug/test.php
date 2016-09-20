<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>无标题文档</title>
</head>

<body>
<?php
$mac = hash_hmac('sha1', 'eyJzY29wZSI6Im5jbWVtIiwiZGVhZGxpbmUiOjE0MzU3MTU2MzZ9', 's81HXCkwT6_8W9W1gzLOxiwedryqKkel3UU7npVR');
echo "hex值：$mac<br/>";
$mac = hash_hmac('sha1', 'eyJzY29wZSI6Im5jbWVtIiwiZGVhZGxpbmUiOjE0MzU3MTU2MzZ9', 's81HXCkwT6_8W9W1gzLOxiwedryqKkel3UU7npVR',true);
echo "hash_hmac值：$mac<br/>";
$mac = base64_encode($mac);
echo "线上SHA1值：Yx4nBSlS1oW+MpYAFNhz5redj+0=<br/>";
echo "本地SHA1值：$mac<br/>";
?>
</body>
</html>