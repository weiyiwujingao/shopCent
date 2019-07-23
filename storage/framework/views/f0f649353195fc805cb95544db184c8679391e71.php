<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>管理员操作日志</h5>
        </div>
        <div class="ibox-content">
                <table class="table table-striped table-bordered table-hover m-t-md">
                    <thead>
                    <tr>
                        <th class="text-center" width="100">ID</th>
                        <th class="text-center" width="150">用户名</th>
                        <th class="text-center" width="150">拥有权限</th>
                        <th class="text-center" >操作内容</th>
                        <th class="text-center" width="200">操作地址</th>
                        <th class="text-center" width="150">登录时间</th>
                        <th class="text-center" width="100">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $actions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($item->type == 1): ?>
                        <tr>
                            <td class="text-center"><?php echo e($item->id); ?></td>
                            <td class="text-center"><?php echo e($item->admin->name); ?></td>
                            <td class="text-center">
                                <?php $__currentLoopData = $item->admin->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php echo e($role->name); ?>

                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </td>
                            <td><?php echo e($item->data['action']); ?></td>
                            <td class="text-center"><?php echo e($item->data['ip']); ?><br>来自：<?php echo e($item->data['address']); ?></td>
                            <td class="text-center"><?php echo e($item->created_at->diffForHumans()); ?></td>
                            <td class="text-center">
                                <form class="form-common" action="<?php echo e(route('actions.destroy',$item->id)); ?>" method="post">
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('DELETE')); ?>

                                    <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>
                                </form>
                            </td>
                        </tr>
                        <?php else: ?>
                            <tr>
                                <td class="text-center"><?php echo e($item->id); ?></td>
                                <td class="text-center"><?php echo e(isset($item->admin_id) ? $item->admin->name : '暂无'); ?></td>
                                <td class="text-center">
                                    <?php if($item->admin_id): ?>
                                        <?php $__currentLoopData = $item->admin->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <?php echo e($role->name); ?>

                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <?php else: ?>
                                        暂无
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item->data['action']); ?></td>
                                <td class="text-center"><?php echo e($item->data['ip']); ?><br>来自：<?php echo e($item->data['address']); ?></td>
                                <td class="text-center"><?php echo e($item->created_at->diffForHumans()); ?></td>
                                <td class="text-center">
                                    <form class="form-common" action="<?php echo e(route('actions.destroy',$item->id)); ?>" method="post">
                                        <?php echo e(csrf_field()); ?>

                                        <?php echo e(method_field('DELETE')); ?>

                                        <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            <div class="pull-right pagination m-t-no">
                <div class="text-center">
                    <?php echo e($actions->links()); ?>

                </div>
                <div>
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>