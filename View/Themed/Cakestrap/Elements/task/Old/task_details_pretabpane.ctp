<?php
    //echo $this->Html->script('form-controls', array("inline"=>false));
     if (AuthComponent::user('id')){
        $controlled_teams = AuthComponent::user('Teams');
    }
    
    
    $userControls = false;
    
    $currTeamId = $task_det['Task']['team_id'];
    
    if(in_array($currTeamId, $controlled_teams)){ $userControls = true;}
    
    
?>

        <div class="row">
            <div class="col-sm-2">
                <div class="list-group">
                  <a href="#" class="list-group-item active bg-yh"> Task Menu</a>
                  <a href="#edit_<?php echo $tid ?>" class="list-group-item" role="tab" data-toggle="tab"><i class="fa fa-edit"></i> Edit Task</a>
                  <a href="#changes_<?php echo $tid ?>" class="list-group-item" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i> Changes</a>
                </div>
            </div>

            <div class="col-sm-10">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane <?php echo ($userControls)? 'active':'';?>" id="edit_<?php echo $tid ?>">
                        <?php 
                            echo $this->element('task/edit_ajax', array('task'=>$task_det));
                            //echo $this->element('test/form_test', array('task'=>$task_det)); 
                        ?>
                    </div>
                    <div role="tabpanel" class="tab-pane <?php echo (!$userControls)? 'active':''?>" id="changes_<?php echo $tid ?>">
                        <div id="loaded_chg_<?php echo $tid ?>">
                        <?php echo $this->element('change/td_changes', array('task'=>$tid)); ?>
                        </div>
                    </div>
                </div>
            </div><!--outer panel-->
        </div>
<?php echo $this->Js->writeBuffer(); ?>
