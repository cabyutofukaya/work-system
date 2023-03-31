<div class="form-group">
    <label><?php echo e($label, false); ?></label>
    <div>
    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <span class="icheck">
            <label class="checkbox-inline">
                <input type="checkbox" name="<?php echo e($name, false); ?>[]" value="<?php echo e($option, false); ?>" class="<?php echo e($class, false); ?>" <?php echo e(in_array($option, (array)old($column, $value)) || ($value === null && in_array($label, $checked)) ?'checked':'', false); ?> <?php echo $attributes; ?> />&nbsp;<?php echo e($label, false); ?>&nbsp;&nbsp;
            </label>
        </span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <input type="hidden" name="<?php echo e($name, false); ?>[]">
    <?php echo $__env->make('admin::actions.form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/actions/form/checkbox.blade.php ENDPATH**/ ?>