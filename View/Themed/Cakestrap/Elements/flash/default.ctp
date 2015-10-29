<?php
    if(isset($message) && !empty($message)):?>
        <div class="alert alert-info">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
                <?php echo $message; ?>
        </div><!-- /.alert alert-info -->
<?php endif; ?>