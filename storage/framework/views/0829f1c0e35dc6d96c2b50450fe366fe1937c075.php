<div <?php echo $attributes; ?>>
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="panel box box-primary" style="margin-bottom: 0px">
        <div class="box-header with-border">
            <h4 class="box-title">
                <a data-toggle="collapse" data-parent="#<?php echo e($id, false); ?>" href="#collapse<?php echo e($key, false); ?>">
                    <?php echo e($item['title'], false); ?>

                </a>
            </h4>
        </div>
        <div id="collapse<?php echo e($key, false); ?>" class="panel-collapse collapse <?php echo e($key == 0 ? 'in' : '', false); ?>">
            <div class="box-body">
                <?php echo $item['content']; ?>

            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/widgets/collapse.blade.php ENDPATH**/ ?>