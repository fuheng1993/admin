<?php /*a:1:{s:53:"/www/wwwroot/easy.rmrf.top/App/Views/index/index.html";i:1578317396;}*/ ?>
<!DOCTYPE html>
<!-- saved from url=(0019)https://696404.com/ -->
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <title>模板渲染</title>
</head>
<body>
<table border="1">
  <h1>模板渲染</h1>
 <?php echo var_dump($server); ?>
  <tr><th>ID</th><th>电话</th></tr>
  <?php if(is_array($json) || $json instanceof \think\Collection || $json instanceof \think\Paginator): $i = 0; $__LIST__ = $json;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$v): $mod = ($i % 2 );++$i;?>
    <tr>
      <td><?php echo htmlentities($v['id']); ?></td>
      <td><?php echo htmlentities($v['tel']); ?></td>
    </tr>
  <?php endforeach; endif; else: echo "" ;endif; ?>
  </table>
</body>

</html>