<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>添加管理员</h5>
        </div>
        <div class="ibox-content">
            <a class="menuid btn btn-primary btn-sm" href="javascript:history.go(-1)">返回</a>
            <a href="<?php echo e(route('admins.index')); ?>"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 管理员管理</button></a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="<?php echo e(route('admins.update',$admin->id)); ?>" method="post" accept-charset="UTF-8" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                <?php echo e(method_field('PATCH')); ?>

                <div class="form-group">
                    <label class="col-sm-2 control-label">用户名：</label>
                    <div class="input-group col-sm-2">
                        <input type="text" class="form-control" name="name" value="<?php echo e($admin->name); ?>" required data-msg-required="请输入用户名">
                        <?php if($errors->has('name')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('name')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">密码：</label>
                    <div class="input-group col-sm-2">
                        <input type="password" class="form-control" name="password">
                        <?php if($errors->has('password')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('password')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">头像：</label>
                    <div class="input-group col-sm-2">
                        <input type="file" class="form-control" name="avatr">
                        <?php if($errors->has('avatr')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('avatr')); ?></span>
                        <?php endif; ?>
                        <span class="view picview ">
                           <img id="thumbnail-avatar" class="thumbnail img-responsive" src="<?php echo e($admin->avatr); ?>" width="100" height="100">
                        </span>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">所属角色：</label>
                    <div class="input-group col-sm-2">
                        <?php
                            $ruleids = $admin->roles->pluck('id')->toArray();
                        ?>
                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label><input type="checkbox" name="role_id[]" value="<?php echo e($item->id); ?>" <?php if(in_array($item->id,$ruleids)): ?> checked="checked" <?php endif; ?>> <?php echo e($item->name); ?></label><br/>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php if($errors->has('role_id')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('role_id')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="input-group col-sm-1">
                        <select class="form-control" name="status">
                            <option value="1" <?php if($admin->status == 1): ?> selected="selected" <?php endif; ?>>正常</option>
                            <option value="2" <?php if($admin->status == 2): ?> selected="selected" <?php endif; ?>>锁定</option>
                        </select>
                        <?php if($errors->has('status')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('status')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <div class="col-sm-12 col-sm-offset-2">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保 存</button>
                        <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                    </div>
                </div>
                <div class="clearfix"></div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>