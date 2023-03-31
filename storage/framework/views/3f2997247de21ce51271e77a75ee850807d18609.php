<?php $__env->startSection('field'); ?>
    <input class="form-control ie-input"/>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('assert'); ?>
    <script>
        <?php $__env->startComponent('admin::grid.inline-edit.partials.popover', compact('trigger')); ?>
            <?php $__env->slot('content'); ?>
            $template.find('input').attr('value', $trigger.data('value'));
            <?php $__env->endSlot(); ?>
            <?php $__env->slot('shown'); ?>
                $popover.find('.ie-input').focus();
                <?php if($mask): ?>
                $popover.find('.ie-input').inputmask(<?php echo json_encode($mask, 15, 512) ?>);
                <?php endif; ?>
            <?php $__env->endSlot(); ?>
        <?php echo $__env->renderComponent(); ?>
    </script>

    
    <script>
    <?php $__env->startComponent('admin::grid.inline-edit.partials.submit', compact('resource', 'name')); ?>
        $popover.data('display').html(val);
    <?php echo $__env->renderComponent(); ?>
    </script>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('admin::grid.inline-edit.comm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/inline-edit/input.blade.php ENDPATH**/ ?>