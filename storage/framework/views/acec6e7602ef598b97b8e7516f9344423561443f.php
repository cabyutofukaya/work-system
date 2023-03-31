<div class="<?php echo e($viewClass['form-group'], false); ?> <?php echo !$errors->has($errorKey) ? '' : 'has-error'; ?>">

    <label for="<?php echo e($id, false); ?>" class="<?php echo e($viewClass['label'], false); ?> control-label"><?php echo e($label, false); ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?>">

        <?php echo $__env->make('admin::form.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="input-group" style="width: 150px">
            <input type="text" id="<?php echo e($id, false); ?>" name="<?php echo e($name, false); ?>" value="<?php echo e(old($column, $value), false); ?>" class="form-control <?php echo e($class, false); ?>" placeholder="0" style="text-align:right;" <?php echo $attributes; ?> />
            <span class="input-group-addon">%</span>
        </div>

        <?php echo $__env->make('admin::form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/form/rate.blade.php ENDPATH**/ ?>