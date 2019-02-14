<?php
    //$this->Js->buffer("");
    
    if(!isset($current)){
        $current = (isset($task['Task']['parent_id'])) ? $task['Task']['parent_id'] : null;
    }
    if(!isset($child)){
        $child = (isset($task['Task']['id'])) ? $task['Task']['id'] : null;
    }
    
    $unlink_enabled = (!$current) ? 'disabled': '';

    // Fetch linkable parents if necessary
    if(!isset($linkable) && $team){
        $linkable = $this->requestAction(array(
            'controller' => 'tasks', 
            'action' => 'linkable', $team, $current, $child
        ));
        //$this->log($linkable);
    }
    
    //$this->log('linkable was empty?: '.empty($linkable));
    if(isset($team) && !empty($linkable)){ ?>
        <div class="form-group">
            <?php 
                echo $this->Form->input('Task.parent_id', array(
                    'empty'=> '< Not Linked >', 
                    'selected'=>$current,
                    'data-childTID'=>$child, 
                    'multiple'=>false, 
                    'options'=>$linkable,
                    'label'=>false, 
                    'class' => 'form-control linkableParentSelect',
                    'div'=>array(
                        'class'=>'input-group'
                    ),
                    'after'=>'<span class="input-group-btn"><button class="pidClearBut btn btn-danger '.$unlink_enabled.'"><i class="fa fa-unlink"></i> Unlink</button></span>',
                )); 
            ?>
        </div><!-- .form-group -->
    <?php 
    }
    elseif (isset($team) && empty($linkable)){
        echo '<div class="alert alert-info" role="alert"><i class="fa fa-info-circle"></i> Selected lead team has no linkable tasks.</div>';
    }
    else{
        echo '<div class="alert alert-info" role="alert"><i class="fa fa-info-circle"></i> Select a lead team first</div>';
    }

    echo $this->Js->writeBuffer(); 
?>