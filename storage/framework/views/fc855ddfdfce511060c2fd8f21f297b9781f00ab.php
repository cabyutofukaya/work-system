<div class="form-group">
    <label><?php echo e($label, false); ?></label>
    <input type="file" class="<?php echo e($class, false); ?>" name="<?php echo e($name, false); ?>" <?php echo $attributes; ?> />
    <?php echo $__env->make('admin::actions.form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
</div>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/actions/form/file.blade.php ENDPATH**/ ?>