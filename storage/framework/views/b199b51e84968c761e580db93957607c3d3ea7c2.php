<?php $__env->startSection('field'); ?>
    <textarea class="form-control ie-input" rows="<?php echo e($rows, false); ?>"></textarea>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('assert'); ?>
    <script>
        <?php $__env->startComponent('admin::grid.inline-edit.partials.popover', compact('trigger')); ?>
            <?php $__env->slot('content'); ?>
                $template.find('textarea').text($trigger.data('value'));
            <?php $__env->endSlot(); ?>
            <?php $__env->slot('shown'); ?>
                $popover.find('.ie-input').focus();
            <?php $__env->endSlot(); ?>
        <?php echo $__env->renderComponent(); ?>
    </script>

    
    <script>
    <?php $__env->startComponent('admin::grid.inline-edit.partials.submit', compact('resource', 'name')); ?>
        $popover.data('display').html(val);
    <?php echo $__env->renderComponent(); ?>
    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin::grid.inline-edit.comm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/inline-edit/textarea.blade.php ENDPATH**/ ?>