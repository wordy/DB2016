<?php

    if(!empty($failed)){ ?>
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p><b>Error</b> Send failed to the following:</p>
            <?php 
                echo $this->Html->nestedList($failed, array(), array(), 'ol');
            ?>   
        </div>
    <?php }
    elseif(empty($failed) && !empty($sent)){ ?>
        <div class="alert alert-success flash-success" role="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <p><b>Send Success!</b> Emails were sent to:</p>
            <?php 
                echo $this->Html->nestedList($sent, array(), array(), 'ol');
            ?>   
        </div>
    <?php 
    }
    ?>