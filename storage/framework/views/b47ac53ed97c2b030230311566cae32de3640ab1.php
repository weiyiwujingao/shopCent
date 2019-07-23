<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>角色管理</h5>
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a> &nbsp;
            <a href="<?php echo e(route('roles.create')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加角色</button></a>
            <table class="table table-striped table-bordered table-hover m-t-md">
                <thead>
                <tr>
                    <th class="text-center" width="100">ID</th>
                    <th>角色名称</th>
                    <th>角色描述</th>
                    <th class="text-center" width="100">排序</th>
                    <th class="text-center" width="100">状态</th>
                    <th class="text-center" width="300">操作</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td  class="text-center" ><?php echo e($item->id); ?></td>
                        <td><?php echo e($item->name); ?></td>
                        <td><?php echo e($item->remark); ?></td>
                        <td class="text-center"><?php echo e($item->order); ?></td>
                        <td class="text-center">
                            <?php if($item->status == 1): ?>
                                <span class="text-navy">启用</span>
                            <?php else: ?>
                                <span class="text-danger">禁用</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <div class="btn-group">
                                <a href="<?php echo e(route('roles.access',$item->id)); ?>"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 权限设置</button></a>
                                <a href="<?php echo e(route('roles.edit',$item->id)); ?>"><button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button></a>
                                <form class="form-common" action="<?php echo e(route('roles.destroy', $item->id)); ?>" method="post">
                                    <?php echo e(csrf_field()); ?>

                                    <?php echo e(method_field('DELETE')); ?>

                                <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="clearfix"></div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>