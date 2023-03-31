<?php $__env->startSection('field'); ?>
    <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="checkbox icheck">
            <label>
                <input type="checkbox" name='radio-<?php echo e($name, false); ?>[]' class="minimal ie-input" value="<?php echo e($option, false); ?>" data-label="<?php echo e($label, false); ?>"/>&nbsp;<?php echo e($label, false); ?>&nbsp;&nbsp;
            </label>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('assert'); ?>
    <style>
        .icheck.checkbox {
            margin: 5px 0 5px 20px;
        }

        .ie-content-<?php echo e($name, false); ?> .ie-container  {
            width: 150px;
            position: relative;
        }
    </style>

    <script>
        <?php $__env->startComponent('admin::grid.inline-edit.partials.popover', compact('trigger')); ?>
            <?php $__env->slot('content'); ?>
            $template.find('input[type=checkbox]').each(function (index, checkbox) {
                if($.inArray($(checkbox).attr('value'), $trigger.data('value')) >= 0) {
                    $(checkbox).attr('checked', true);
                }
            });
            <?php $__env->endSlot(); ?>
        <?php echo $__env->renderComponent(); ?>
    </script>

    <script>
    <?php $__env->startComponent('admin::grid.inline-edit.partials.submit', compact('resource', 'name')); ?>

        <?php $__env->slot('val'); ?>
            var val = [];
            var label = [];
            $popover.find('.ie-input:checked').each(function(){
                val.push($(this).val());
                label.push($(this).data('label'));
            });
        <?php $__env->endSlot(); ?>

        $popover.data('display').html(label.join(';'));

    <?php echo $__env->renderComponent(); ?>
    </script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('admin::grid.inline-edit.comm', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/inline-edit/checkbox.blade.php ENDPATH**/ ?>