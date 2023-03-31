<div class="<?php echo e($viewClass['form-group'], false); ?>">
    <label for="<?php echo e($id, false); ?>" class="<?php echo e($viewClass['label'], false); ?> control-label"><?php echo e($label, false); ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?>">
        <input type="text" id="<?php echo e($id, false); ?>" name="<?php echo e($name, false); ?>" value="<?php echo e($value, false); ?>" class="form-control" readonly <?php echo $attributes; ?> />

        <?php echo $__env->make('admin::form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div><?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/form/id.blade.php ENDPATH**/ ?>