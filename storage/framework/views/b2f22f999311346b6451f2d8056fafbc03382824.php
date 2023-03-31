<a href='javascript:void(0);' class='text-muted inline-upload-trigger' data-target="<?php echo e($target, false); ?>">
    <i class="fa fa-upload"></i>&nbsp;<?php echo $value; ?>

</a>
<div class="hide">
  <input type="file" class="inline-upload" id="<?php echo e($target, false); ?>" data-key="<?php echo e($key, false); ?>" <?php echo e($multiple ? 'multiple' : '', false); ?>/>
</div>

<script>
$('.inline-upload-trigger').click(function () {
    $('#'+$(this).data('target')).trigger('click');
});

$('input.inline-upload').on('change', function () {

    var formData = new FormData();

    <?php if($multiple): ?>
        event.target.files.forEach(function (file) {
            formData.append("<?php echo e($name, false); ?>[]", file);
        });
    <?php else: ?>
    formData.append("<?php echo e($name, false); ?>", event.target.files[0]);
    <?php endif; ?>
    formData.append('_token', LA.token);
    formData.append('_method', 'PUT');

    $.ajax({
        url: "<?php echo e($resource, false); ?>/" + $(this).data('key'),
        type: "POST",
        processData: false,
        contentType: false,
        enctype: 'multipart/form-data',
        data: formData,
        success: function (data) {
            toastr.success(data.message);
            $.admin.reload();
        }
    });
});
</script>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/inline-edit/upload.blade.php ENDPATH**/ ?>