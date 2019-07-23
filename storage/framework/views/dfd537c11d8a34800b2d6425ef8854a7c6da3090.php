

<?php $__env->startSection('css'); ?>
    <style>
        .animated{-webkit-animation-fill-mode: none;}
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5>编辑权限</h5>
        </div>
        <div class="ibox-content">
            <a href="<?php echo e(route('rules.index')); ?>">
                <button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 权限管理
                </button>
            </a>
            <div class="hr-line-dashed m-t-sm m-b-sm"></div>
            <form class="form-horizontal m-t-md" action="<?php echo e(route('rules.update',$rule->id)); ?>" method="POST">
                <div class="form-group">
                    <label class="col-sm-2 control-label">上级权限：</label>
                    <div class="col-sm-2">
                        <select name="parent_id" class="form-control">
                            <option value="0">顶级权限</option>
                            <?php $__currentLoopData = $rules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($item['id']); ?>" <?php if($rule->parent_id == $item['id']): ?> selected="selected"<?php endif; ?> ><?php echo e($item['_name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php if($errors->has('parent_id')): ?>
                                <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('parent_id')); ?></span>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">权限名称：</label>
                    <div class="col-sm-3">
                        <input type="text" name="name" value="<?php echo e($rule->name); ?>" class="form-control" required data-msg-required="请输入权限名称">
                        <?php if($errors->has('name')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('name')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">菜单图标：</label>
                    <div class="col-sm-3">
                        <input type="text" name="fonts" id="fonts" onclick="showicon()" value="<?php echo e($rule->fonts); ?>"  placeholder="菜单图标" class="form-control">
                        <?php if($errors->has('fonts')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('fonts')); ?></span>
                        <?php else: ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i> 采用Font Awesome字体图标</span> </div>
                        <?php endif; ?>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">权限路径：</label>
                    <div class="col-sm-3">
                        <input type="text" name="route" value="<?php echo e($rule->route); ?>" class="form-control">
                        <?php if($errors->has('route')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('route')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">排序：</label>
                    <div class="col-sm-1">
                        <input type="text" name="sort" value="<?php echo e($rule->sort); ?>" required class="form-control">
                        <?php if($errors->has('sort')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('sort')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">是否隐藏：</label>
                    <div class="col-sm-2">
                        <select name="is_hidden" class="form-control">
                            <option value="0" <?php if($rule->is_hidden == 0): ?>selected="selected" <?php endif; ?> >显示</option>
                            <option value="1" <?php if($rule->is_hidden == 1): ?>selected="selected" <?php endif; ?>>隐藏</option>
                        </select>
                        <?php if($errors->has('is_hidden')): ?>
                            <span class="help-block m-b-none"><i class="fa fa-info-circle"></i><?php echo e($errors->first('is_hidden')); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="hr-line-dashed m-t-sm m-b-sm"></div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态：</label>
                    <div class="col-sm-2">
                        <select name="status" class="form-control">
                            <option value="1" <?php if($rule->status == 1): ?>selected="selected" <?php endif; ?>>启用</option>
                            <option value="0" <?php if($rule->status == 0): ?>selected="selected" <?php endif; ?>>禁用</option>
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
                <?php echo e(method_field('PATCH')); ?>

                <?php echo e(csrf_field()); ?>

            </form>
        </div>
    </div>
</div>
<div id="functions" style="display: none;">
    <?php echo $__env->make('admin.rules.fonticon', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
</div>
<?php $__env->startSection('footer-js'); ?>
    <script>

        function showicon(){
            layer.open({
                type: 1,
                title:'点击选择图标',
                area: ['800px', '80%'], //宽高
                anim: 2,
                shadeClose: true, //开启遮罩关闭
                content: $('#functions')
            });
        }
        $('.fontawesome-icon-list .fa-hover').find('a').click(function(){
            var str=$(this).text();
            $('#fonts').val( $.trim(str));
            layer.closeAll();
        })
    </script>
<?php $__env->stopSection(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>