<div class="<?php echo e($viewClass['form-group'], false); ?> <?php echo !$errors->has($errorKey) ? '' : 'has-error'; ?>">

    <label for="<?php echo e($id, false); ?>" class="<?php echo e($viewClass['label'], false); ?> control-label"><?php echo e($label, false); ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?> picker-<?php echo e($column, false); ?>">

        <?php echo $__env->make('admin::form.error', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="picker-file-preview <?php echo e(empty($preview) ? 'hide' : '', false); ?>">
            <?php $__currentLoopData = $preview; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="file-preview-frame" data-val="<?php echo $item['value']; ?>">
                <div class="file-content">
                    <?php if($item['is_file']): ?>
                        <i class="fa fa-file-text-o"></i>
                    <?php else: ?>
                        <img src="<?php echo e($item['url'], false); ?>"/>
                    <?php endif; ?>
                </div>
                <div class="file-caption-info"><?php echo e(basename($item['url']), false); ?></div>
                <div class="file-actions">
                    <a class="btn btn-default btn-sm remove">
                        <i class="fa fa-trash"></i>
                    </a>
                    <a class="btn btn-default btn-sm" target="_blank" download="<?php echo e(basename($item['url']), false); ?>" href="<?php echo e($item['url'], false); ?>">
                        <i class="fa fa-download"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="input-group">
            <input <?php echo $attributes; ?> />
            <span class="input-group-btn">
              <?php echo $btn; ?>

            </span>
        </div>

        <?php echo $__env->make('admin::form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>

<template>
    <template id="file-preview">
        <div class="file-preview-frame" data-val="_val_">
            <div class="file-content">
                <i class="fa fa-file-text-o"></i>
            </div>
            <div class="file-caption-info">_name_</div>
            <div class="file-actions">
                <a class="btn btn-default btn-sm remove">
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-default btn-sm" target="_blank" download="_name_" href="_url_">
                    <i class="fa fa-download"></i>
                </a>
            </div>
        </div>
    </template>
    <template id="image-preview">
        <div class="file-preview-frame" data-val="_val_">
            <div class="file-content">
                <img src="_url_">
            </div>
            <div class="file-caption-info">_name_</div>
            <div class="file-actions">
                <a class="btn btn-default btn-sm remove">
                    <i class="fa fa-trash"></i>
                </a>
                <a class="btn btn-default btn-sm" target="_blank" download="_name_" href="_url_">
                    <i class="fa fa-download"></i>
                </a>
            </div>
        </div>
    </template>
</template>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/form/filepicker.blade.php ENDPATH**/ ?>