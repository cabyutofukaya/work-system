
<tr style="height: 28px;">
    <td><strong><small><?php echo e($label, false); ?>:</small></strong>&nbsp;&nbsp;&nbsp;</td>
    <td><input type="checkbox" class="<?php echo e($class, false); ?>" <?php echo e($checked, false); ?> data-key="<?php echo e($key, false); ?>" /></td>
</tr>

<script>
    $('.<?php echo e($class, false); ?>').bootstrapSwitch({
        size:'mini',
        onText: '<?php echo e($states['on']['text'], false); ?>',
        offText: '<?php echo e($states['off']['text'], false); ?>',
        onColor: '<?php echo e($states['on']['color'], false); ?>',
        offColor: '<?php echo e($states['off']['color'], false); ?>',
        onSwitchChange: function(event, state){

            $(this).val(state ? <?php echo e($states['on']['value'], false); ?> : <?php echo e($states['off']['value'], false); ?>);

            var key = $(this).data('key');
            var value = $(this).val();
            var _status = true;

            $.ajax({
                url: "<?php echo e($resource, false); ?>/" + key,
                type: "POST",
                data: {
                    "<?php echo e($name, false); ?>": value,
                    _token: LA.token,
                    _method: 'PUT'
                },
                success: function (data) {
                    if (data.status)
                        toastr.success(data.message);
                    else
                        toastr.warning(data.message);
                },
                complete:function(xhr,status) {
                    if (status == 'success')
                        _status = xhr.responseJSON.status;
                }
            });

            return _status;
        }
    });
</script>
<?php /**PATH /home/shin-gt/grouptube-biz/production/releases/17/vendor/encore/laravel-admin/resources/views/grid/inline-edit/switch-group.blade.php ENDPATH**/ ?>