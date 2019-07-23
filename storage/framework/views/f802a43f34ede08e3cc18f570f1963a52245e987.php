<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <?php echo $__env->yieldContent('css'); ?>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <?php echo $__env->make('flash::message', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
    <?php echo $__env->yieldContent('content'); ?>
</div>
<script src="<?php echo e(loadEdition('/js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(loadEdition('/admin/js/bootstrap.min.js')); ?>"></script>
<?php echo $__env->yieldContent('js'); ?>
<script>
    $('div.alert').not('.alert-important').delay(3000).fadeOut(350);
</script>
<?php echo $__env->yieldContent('footer-js'); ?>
</body>
</html>