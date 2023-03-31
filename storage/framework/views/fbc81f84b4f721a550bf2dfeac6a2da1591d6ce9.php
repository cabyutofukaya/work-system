<div class="box">
    <div class="box-header"></div>

    <?php echo $grid->renderFilter(); ?>


    <div class="box-body table-responsive no-padding">
        <ul class="image clearfix">
            <?php $__currentLoopData = $grid->rows(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <label>
                    <?php echo $row->column($key); ?>

                    <?php echo $row->column('__modal_selector__'); ?>

                </label>
            </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>

    <div class="box-footer clearfix">
        <?php echo $grid->paginator(); ?>

    </div>
    <!-- /.box-body -->
</div>

<style>
ul.image {
    padding: 0px;
}

.image li {
    float: left;
    margin: 10px;
    list-style-type:none;
    position: relative;
}

.image label {
    cursor: pointer;
}

.image .img-thumbnail {
    padding-right: 15px;
}

.image .iradio_minimal-blue,.image .icheckbox_minimal-blue {
    position: absolute;
    bottom: 5px;
    right: 1px;
    background-color: #ffffff;
}
</style>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/image.blade.php ENDPATH**/ ?>