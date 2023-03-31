<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link href="<?php echo e(mix('/css/app.css'), false); ?>" rel="stylesheet">
    <script src="<?php echo e(mix('/js/app.js'), false); ?>" defer></script>
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@4.x/css/materialdesignicons.min.css" rel="stylesheet">
    <script src="https://yubinbango.github.io/yubinbango-core/yubinbango-core.js" charset="UTF-8"></script>
    <meta name="app-config" content='<?php echo json_encode(array_merge(config("const.front")), 15, 512) ?>'/>
</head>
<body>
<?php echo app('Tightenco\Ziggy\BladeRouteGenerator')->generate(); ?>
<?php if (!isset($__inertiaSsr)) { $__inertiaSsr = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsr instanceof \Inertia\Ssr\Response) { echo $__inertiaSsr->body; } else { ?><div id="app" data-page="<?php echo e(json_encode($page), false); ?>"></div><?php } ?>
</body>
</html><?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/resources/views/app.blade.php ENDPATH**/ ?>