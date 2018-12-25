<?php
    $this->Js->buffer("
        $('button.pidClearBut').on('click', function(e){
            e.preventDefault();
            if(!$(this).hasClass('disabled')){
                var p_sel = $(this).parents('div.form-group').find('.linkableParentSelect');
                p_sel.val('').trigger('change');
            }
        });
     ");
    
    if(!isset($current)){
        $current = (isset($task['Task']['parent_id']))? $task['Task']['parent_id']:null; 
    }
    if(!isset($child)){
        $child = (isset($task['Task']['id']))? $task['Task']['id']:   null; 
    }
    
    $unlink_enabled = '';;

//echo $current;
    
    //$this->log($team);

    // Fetch linkable parents if necessary
    if(!isset($linkable) && $team){
        $linkable = $this->requestAction(array(
            'controller' => 'tasks', 
            'action' => 'linkable', $team, $current, $child
        ));
    }
    
    //$this->log($linkable);
    
    if(!$current){
        $unlink_enabled = ' disabled';
    }
    
    if(isset($team) && !empty($linkable)){ ?>

        <div class="form-group">
            <?php 
                echo $this->Form->input('Task.parent_id', array(
                    'empty'=> '< Not Linked >', 
                    //'default'=>(isset($current))? $current: 'empty',
                    'selected'=>$current, 
                    'multiple'=>false, 
                    'options'=>$linkable,
                    'label'=>false, 
                    'class' => 'form-control linkableParentSelect',
                    'div'=>array(
                        'class'=>'input-group'),
                    'after'=>'<span class="input-group-btn"><button class="pidClearBut btn btn-danger'.$unlink_enabled.'"><i class="fa fa-unlink"></i> Unlink</button></span>',
                )); 
            ?>
            <span id="lpHelpBlock" class="help-block">Link tasks when they are related. For example, when responding to requests from other teams.</span>
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

