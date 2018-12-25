<?php

    if(!empty($errors)){ ?>
        <div class="alert alert-danger" role="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            
            <?php 
                echo '<b>'.$message.'</b><br/>';
                echo $this->Html->nestedList($errors, array(), array(), 'ol');
            ?>   
        </div>
    <?php }
    else{ ?>
        <div class="alert alert-success flash-success" role="alert">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <b>OK </b> 
            <?php 
                echo $message;
            ?>   
        </div>
    <?php 
    }
    ?>