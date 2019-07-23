<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox-title">
            <h5><?php echo e($role->name); ?> - 授权</h5>
        </div>
        <div class="ibox-content">
            <a href="<?php echo e(route('roles.index')); ?>"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 角色管理</button></a>
            <a href="<?php echo e(route('roles.create')); ?>" link-url="javascript:void(0)"><button class="btn btn-primary btn-sm" type="button"><i class="fa fa-plus-circle"></i> 添加角色</button></a>

            <form class="form-horizontal m-t-md" action="<?php echo e(route('roles.group-access',$role->id)); ?>" method="post">
                <?php echo e(csrf_field()); ?>

                <div class="form-group">
                    <table class="table table-striped table-bordered table-hover table-condensed">
                        <?php $__currentLoopData = $datas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(empty($item->_data)): ?>
                                <tr class="b-group">
                                    <th width="10%">
                                        <label>
                                            &nbsp;&nbsp;<?php echo e($item->name); ?>&nbsp;
                                            <input type="checkbox" name="rule_id[]" value="<?php echo e($item->id); ?>" onclick="checkAll(this)" <?php if(in_array($item->id,$rules)): ?> checked="checked" <?php endif; ?>>
                                        </label>
                                    </th>
                                    <td></td>
                                </tr>
                            <?php else: ?>
                                <tr class="b-group">
                                    <th width="10%">
                                        <label>
                                            &nbsp;&nbsp;<?php echo e($item->name); ?>&nbsp;<input type="checkbox" name="rule_id[]" value="<?php echo e($item->id); ?>" <?php if(in_array($item->id,$rules)): ?> checked="checked" <?php endif; ?> onclick="checkAll(this)">
                                        </label>
                                    </th>
                                    <td class="b-child">
                                        <?php $__currentLoopData = $item->_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <table class="table table-striped table-bordered table-hover table-condensed">
                                                <tr class="b-group">
                                                    <th width="10%">
                                                        <label>
                                                            <?php echo e($value->name); ?>&nbsp;<input type="checkbox" name="rule_id[]" value="<?php echo e($value->id); ?>" <?php if(in_array($value->id,$rules)): ?> checked="checked" <?php endif; ?> onclick="checkAll(this)">
                                                        </label>
                                                    </th>
                                                    <td>
                                                        <?php if(!empty($value->_data)): ?>
                                                            <?php $__currentLoopData = $value->_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                                <label>
                                                                    &emsp;<?php echo e($val->name); ?> <input type="checkbox" name="rule_id[]" value="<?php echo e($val->id); ?>" <?php if(in_array($val->id,$rules)): ?> checked="checked" <?php endif; ?>>
                                                                </label>
                                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            </table>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <th></th>
                            <td>
                                <button class="btn btn-primary" type="submit"><i class="fa fa-check"></i>&nbsp;保存</button>
                                <button class="btn btn-white" type="reset"><i class="fa fa-repeat"></i> 重 置</button>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function checkAll(obj){
        $(obj).parents('.b-group').eq(0).find("input[type='checkbox']").prop('checked', $(obj).prop('checked'));
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.layouts.layout', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>