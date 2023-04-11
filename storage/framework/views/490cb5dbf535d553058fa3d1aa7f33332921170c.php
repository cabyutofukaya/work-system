<!-- Start of Telescope Toolbar widget !-->
<div id="sfwdt<?php echo e($token, false); ?>" class="sf-toolbar sf-display-none"></div>

<script <?php if(isset($csp_script_nonce) && $csp_script_nonce): ?> nonce="<?php echo e($csp_script_nonce, false); ?>" <?php endif; ?>>/*<![CDATA[*/
  (function () {
    Sfjs.loadToolbar('<?php echo e($token, false); ?>');
  })();
/*]]>*/</script>
<!-- End of Telescope Toolbar widget !-->
<?php /**PATH /Applications/MAMP/htdocs/grouptube-biz/group_tube/vendor/fruitcake/laravel-telescope-toolbar/src/../resources/views/widget.blade.php ENDPATH**/ ?>