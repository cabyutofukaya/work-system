<div class="<?php echo e($viewClass['form-group'], false); ?> <?php echo ($errors->has($errorKey['start']) || $errors->has($errorKey['end'])) ? 'has-error' : ''; ?>">

    <label for="<?php echo e($id['start'], false); ?>" class="<?php echo e($viewClass['label'], false); ?> control-label"><?php echo e($label, false); ?></label>

    <div class="<?php echo e($viewClass['field'], false); ?>">
        <div class="row">
            <div class="col-lg-12">
                <?php if($errors->has($errorKey['start'])): ?>
                    <?php $__currentLoopData = $errors->get($errorKey['start']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> <?php echo e($message, false); ?></label><br/>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text"
                           name="<?php echo e($name['start'], false); ?>"
                           value="<?php echo e(old($column['start'], $value['start'] ?? null), false); ?>"
                           class="form-control <?php echo e($class['start'], false); ?>"
                           style="width: 160px"
                           autocomplete="off"
                            <?php echo $attributes; ?>

                    />
                </div>
            </div>
        </div>

        <div class="row" style="margin-top: 5px">
            <div class="col-lg-12">
                <?php if($errors->has($errorKey['end'])): ?>
                    <?php $__currentLoopData = $errors->get($errorKey['end']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <label class="control-label" for="inputError"><i class="fa fa-times-circle-o"></i> <?php echo e($message, false); ?></label><br/>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

                <div class="input-group">
                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                    <input type="text"
                           name="<?php echo e($name['end'], false); ?>"
                           value="<?php echo e(old($column['end'], $value['end'] ?? null), false); ?>"
                           class="form-control <?php echo e($class['end'], false); ?>"
                           style="width: 160px"
                           autocomplete="off"
                            <?php echo $attributes; ?>

                    />
                </div>
            </div>
        </div>

        <?php echo $__env->make('admin::form.help-block', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    </div>
</div>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/form/datetimerange.blade.php ENDPATH**/ ?>