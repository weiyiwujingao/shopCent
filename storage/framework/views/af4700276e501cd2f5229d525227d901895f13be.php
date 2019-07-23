<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="renderer" content="webkit">
    <meta http-equiv="Cache-Control" content="no-siteapp" />
    <title>后台管理中心 - <?php echo $__env->yieldContent('title', config('app.name', 'Laravel')); ?></title>
    <meta name="keywords" content="<?php echo e(config('app.name', 'Laravel')); ?>">
    <meta name="description" content="<?php echo e(config('app.name', 'Laravel')); ?>">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <link rel="shortcut icon" href="/favicon.ico">
    <link href="<?php echo e(loadEdition('/admin/css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(loadEdition('/admin/css/font-awesome.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(loadEdition('/admin/css/animate.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(loadEdition('/admin/css/style.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(loadEdition('/js/dialog/ui-dialog.css')); ?>" rel="stylesheet">
    <?php echo $__env->yieldContent('css'); ?>
</head>
<body class="fixed-sidebar full-height-layout gray-bg" style="overflow:hidden">
<div id="wrapper">
    <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('admin.commons._menu', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->make('admin.commons._wrapper', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<script src="<?php echo e(loadEdition('/js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/admin/js/bootstrap.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/admin/js/plugins/metisMenu/jquery.metisMenu.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/admin/js/plugins/slimscroll/jquery.slimscroll.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/js/plugins/layer/layer.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/admin/js/plugins/pace/pace.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/admin/js/content.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/js/dialog/artdialog.js')); ?>"></script>
<?php echo $__env->yieldContent('js'); ?>
<script>
    $(function(){$("#side-menu").metisMenu();})

    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>
<?php echo $__env->yieldContent('footer-js'); ?>
</body>
</html>
