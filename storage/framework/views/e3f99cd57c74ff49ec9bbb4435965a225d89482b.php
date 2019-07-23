
<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="alert alert-warning alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            系统权限菜单，非专业技术人员请勿修改、增加、删除等操作。
        </div>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>权限列表</h5>
        </div>
        <div class="ibox-content">
            <a href="<?php echo e(route('rules.create')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加权限</button></a>
            <table class="table table-striped table-bordered table-hover m-t-md">
                <thead>
                    <tr>
                        <th>权限名称</th>
                        <th>权限方法</th>
                        <th class="text-center" width="100">图标</th>
                        <th class="text-center" width="100">排序</th>
                        <th class="text-center" width="100">是否显示</th>
                        <th class="text-center" width="250">操作</th>
                    </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($item['_name']); ?></td>
                    <td><?php echo e($item['route']); ?></td>
                    <td style="text-align:center"><i class="fa fa-<?php echo e(isset($item['fonts']) ? $item['fonts'] : 'desktop'); ?>"></i></td>
                    <td class="text-center"><?php echo e($item['sort']); ?></td>
                    <td class="text-center">
                        <?php if($item['is_hidden'] == 0): ?>
                            <span class="text-navy">显示</span>
                        <?php else: ?>
                            <span class="text-danger">不显示</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <a href="<?php echo e(route('rules.edit',$item['id'])); ?>">
                            <button class="btn btn-primary btn-xs" type="button"><i class="fa fa-paste"></i> 修改</button>
                        </a>
                        <?php if($item['is_hidden'] == 1): ?>
                         <a href="<?php echo e(route('rules.status',['status'=>0,$item['id']])); ?>">
                             <button class="btn btn-info btn-xs" type="button"><i class="fa fa-warning"></i> 显示</button>
                         </a>
                        <?php else: ?>
                        <a href="<?php echo e(route('rules.status',['status'=>1,$item['id']])); ?>">
                            <button class="btn btn-warning btn-xs" type="button"><i class="fa fa-warning"></i> 不显示</button>
                        </a>
                        <?php endif; ?>
                        <form class="form-common" action="<?php echo e(route('rules.destroy',$item['id'])); ?>" method="post">
                            <?php echo e(csrf_field()); ?>

                            <?php echo e(method_field('DELETE')); ?>

                            <button class="btn btn-danger btn-xs" type="submit"><i class="fa fa-trash-o"></i> 删除</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>