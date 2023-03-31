<style>
    .quick-create .select2-selection--multiple {
        padding: 0 !important;
        height: 30px !important;
        width: 200px !important;
        min-height: 30px !important;
    }
</style>

<div class="input-group input-group-sm">
    <select class="form-control <?php echo e($class, false); ?>" style="width: 100%;" name="<?php echo e($name, false); ?>" <?php echo $attributes; ?> multiple data-placeholder="<?php echo e($label, false); ?>">

        <option value=""></option>
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $select => $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($select, false); ?>" <?php echo e($select == old($column, $value) ?'selected':'', false); ?>><?php echo e($option, false); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>

<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/quick-create/multipleselect.blade.php ENDPATH**/ ?>