<!--左侧导航开始-->
<?php
    $admin = Auth::guard('admin')->user();
?>
<nav class="navbar-default navbar-static-side" role="navigation">
    <div class="nav-close"><i class="fa fa-times-circle"></i>
    </div>
    <div class="sidebar-collapse">
        <ul class="nav" id="side-menu">
            <li class="nav-header text-center">
                <div class="dropdown profile-element">
                                <span>
                                    <img alt="image" class="img-circle" src="<?php echo e($admin->avatr); ?>" width="64"/>
                                </span>
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                <span class="clear">
                                <span class="block m-t-xs">
                                    <strong class="font-bold"><?php echo e($admin->name); ?></strong>
                                </span>
                                <span class="text-muted text-xs block">
                                    <?php $__currentLoopData = $admin->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                      <?php echo e($role->name); ?>

                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <b class="caret"></b>
                                </span>
                                </span>
                                </a>
                    <ul class="dropdown-menu animated fadeInRight m-t-xs">
                        <li><a class="J_menuItem" href="form_avatar.html">修改头像</a></li>
                        <li><a class="J_menuItem" href="form_avatar.html">修改密码</a></li>
                        <li class="divider"></li>
                        <li><a href="<?php echo e(route('admin.logout')); ?>">安全退出</a></li>
                    </ul>
                </div>
                <div class="logo-element">YS+</div>
            </li>

            <?php $__currentLoopData = Auth::guard('admin')->user()->getMenus(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if($rule['route'] == 'index.index'): ?>
                    <li>
                        <a title="<?php echo e($rule['name']); ?>" href="<?php echo e(route($rule['route'])); ?>" target="_blank">
                            <i class="fa fa-<?php echo e($rule['fonts']); ?>"></i>
                            <span class="nav-label"><?php echo e($rule['name']); ?></span>
                        </a>
                        <?php if(isset($rule['children'])): ?>
                            <ul class="nav nav-second-level collapse">
                                <?php $__currentLoopData = $rule['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><a class="J_menuItem" href="<?php echo e(route($item['route'])); ?>" data-index="<?php echo e($item['id']); ?>"><?php echo e($item['name']); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    <li>
                <?php else: ?>
                    <li>
                        <a title="<?php echo e($rule['name']); ?>"> <i class="fa fa-<?php echo e($rule['fonts']); ?>"></i>
                            <span class="nav-label"><?php echo e($rule['name']); ?></span>
                            <span class="fa arrow"></span>
                        </a>
                        <?php if(isset($rule['children'])): ?>
                            <ul class="nav nav-second-level collapse">
                                <?php $__currentLoopData = $rule['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><a class="J_menuItem" href="<?php echo e(route($item['route'])); ?>" data-index="index_v1.html"><?php echo e($item['name']); ?></a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endif; ?>
                <li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
</nav>
<!--左侧导航结束-->