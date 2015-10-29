<?php
//print_r($assists);

    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
        
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
   
   
    if(!empty($assists)){
        
        foreach($assists as $as):?>
            <div class="linked_task" style="border-left: 5px solid <?php echo ($as['task_color_code'])? $as['task_color_code'] : '#555'; ?>">
                <div class="row">
                    <div class="col-sm-2  va-mid"><?php echo date('M d g:i A', strtotime($as['start_time']));?></div>
                    <div class="col-sm-2 va-mid"><?php echo ' <b>'.$as['task_type'].'</b>';?><br>
                        <?php
                            echo $this->Ops->makeTeamsSig2015($as['TasksTeam'], $ztlist);
                        ?>
                    </div>
                    <div class="col-sm-5"><?php echo $as['short_description'];?></div>
                    <div class="col-sm-3">
                        <div class="pull-right">
                        <?php 
                            echo $this->Html->link(__('View'), array(
                                'controller'=>'tasks',
                                'action'=>'view', $as['id']),
                                array(
                                    'class'=>'btn btn-xs btn-default')
                            );
                        ?>
                        </div>
                    </div>
                    
                </div>
            </div>            
            
    <?php 
        endforeach;
    
      



    }
    
    else{
        echo 'There are no linked tasks for this task.';
    }









?>