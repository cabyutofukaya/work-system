<span data-toggle="modal" data-target="#grid-modal-<?php echo e($name, false); ?>" data-key="<?php echo e($key, false); ?>">
   <a href="javascript:void(0)"><i class="fa fa-clone"></i>&nbsp;&nbsp;<?php echo e($value, false); ?></a>
</span>

<div class="modal grid-modal fade" id="grid-modal-<?php echo e($name, false); ?>" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border-radius: 5px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo e($title, false); ?></h4>
            </div>
            <div class="modal-body">
                <?php echo $html; ?>

            </div>
        </div>
    </div>
</div>

<?php if($grid): ?>
<style>
    .box.grid-box {
        box-shadow: none;
        border-top: none;
    }

    .grid-box .box-header:first-child {
        display: none;
    }
</style>
<?php endif; ?>

<?php if($async): ?>
<script>
    var modal = $('#grid-modal-<?php echo e($name, false); ?>');
    var modalBody = modal.find('.modal-body');

    var load = function (url) {

        modalBody.html("<div class='loading text-center' style='height:200px;'>\
                <i class='fa fa-spinner fa-pulse fa-3x fa-fw' style='margin-top: 80px;'></i>\
            </div>");

        $.get(url, function (data) {
            modalBody.html(data);
        });
    };

    modal.on('show.bs.modal', function (e) {
        var key = $(e.relatedTarget).data('key');
        load('<?php echo e($url, false); ?>'+'&key='+key);
    }).on('click', '.page-item a, .filter-box a', function (e) {
        load($(this).attr('href'));
        e.preventDefault();
    }).on('submit', '.box-header form', function (e) {
        load($(this).attr('action')+'&'+$(this).serialize());
        return false;
    });
</script>
<?php endif; ?>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/components/column-modal.blade.php ENDPATH**/ ?>