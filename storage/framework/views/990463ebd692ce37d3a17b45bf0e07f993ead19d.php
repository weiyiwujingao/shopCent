<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>添加角色</h5>
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="<?php echo e(route('roles.index')); ?>"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 角色管理</button></a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="<?php echo e(route('roles.update',$role->id)); ?>" method="post">
                <?php echo csrf_field(); ?>

                <?php echo e(method_field('PATCH')); ?>

                <div class="form-group">
                    <label class="col-sm-2 control-label">角色名称：</label>
                    <div class="input-group col-sm-2">
                        <input type="text" class="form-control" name="name" value="<?php echo e($role->name); ?>" required data-msg-required="请输入角色名称">
                        <?php if($errors->has('name')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('name')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">角色描述：</label>
                    <div class="input-group col-sm-3">
                        <textarea name="remark" class="form-control" rows="5" cols="20" data-msg-required="请输入角色描述"><?php echo e($role->remark); ?></textarea>
                        <?php if($errors->has('remark')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('remark')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">排序：</label>
                    <div class="input-group col-sm-1">
                        <input type="text" class="form-control" name="order" value="<?php echo e($role->order); ?>" required data-msg-required="请输入排序">
                        <?php if($errors->has('order')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('order')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="input-group col-sm-1">
                        <select class="form-control" name="status">
                            <option value="1" <?php if($role->status == 1): ?> selected="selected" <?php endif; ?>>启用</option>
                            <option value="2" <?php if($role->status == 2): ?> selected="selected" <?php endif; ?>>禁用</option>
                        </select>
                        <?php if($errors->has('status')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('status')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <div class="col-sm-12 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>　<button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>