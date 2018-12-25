<?php 
    if (AuthComponent::user('id')){
        $user_role = AuthComponent::user('user_role_id');
        $user_teams = AuthComponent::user('TeamsList');
    }
    
    $uControlledATeams = array(); 
    $level = 0;
    
    $ateams = (!empty($task['TasksTeam']))? Hash::extract($task['TasksTeam'], '{n}.team_id'):array();
    
    foreach($user_teams as $team_id => $tcode){
        if(in_array($team_id, $ateams)){
            $uControlledATeams[$team_id] = $tcode;
        }
    }
?>

<?php
    if(empty($task['Parent']['id']) && empty($task['Assist'])){
        echo '<div class="alert alert-info"><b>No Links: </b>There are currently no links to or from this task.</div>';
    }
    else{
        echo '<p>Displays the current task relative to its closest linked tasks. Shows a maximum of <u>2 levels up</u> and <u>1 level down</u> relative to the current task.</p>';
        
        if((!empty($task['Parent']['id']) && (!empty($task['Parent']['parent_id'])))):
            $level++;
            $pp = $this->requestAction('/tasks/getTaskById/'.$task['Parent']['parent_id']);
        ?>
            <div class="row">
                <div class="col-xs-12">
                    <h5><i class="fa fa-link"></i> <b>Linked Task's Linked Task (2 Levels Up)</b></h5>
                    <?php 
                        echo $this->Ops->subtaskMultiWithView($pp['Task'], true);
                    ?>  
                </div>
            </div>
        <?php 
        endif;
 
        if(!empty($task['Parent']['id'])): ?>
            <div class="row">
                <?php echo ($level > 0)? '<div class="col-xs-'.$level.'"></div>':''; ?>
                <div class="col-xs-<?php echo (12-$level);?>">
                    <?php 
                        if($task['Parent']['id'] && ($task['Task']['time_control']==1)){
                            $relType ='<i class="fa fa-clock-o"></i> <b>Time Linked To (1 Level Up)</b>' ; 
                        }
                        else{
                            $relType ='<i class="fa fa-external-link-square"></i> <b>Linked To (1 Level Up)</b>';
                        }
                    ?>
                    <h5><?php echo $relType;?></h5>
                    <?php 
                        echo $this->Ops->subtaskMultiWithView($task['Parent'], true);
                    ?>  
                </div>
            </div>
        <?php $level++; endif; ?>
        
    <div class="row" style="padding-bottom: 10px;" >
        <?php echo ($level > 0)? '<div class="col-xs-'.$level.'"></div>':''; ?>
        <div class="col-xs-<?php echo (12-$level);?>">
            <h5><i class="fa fa-map-marker"></i> <b>This Task</b></h5>
            <?php 
                echo $this->Ops->subtask($task['Task'], array('multi_line'=>true, 'show_offset'=>true, 'highlight'=>'highlight-thistask'));
                $level++;
            ?>
        </div>
    </div>

    <?php if (!empty($task['Assist'])):?>
        <div class="row">
            <?php echo ($level > 0)? '<div class="col-xs-'.$level.'"></div>':''; ?>
            <div class="col-xs-<?php echo (12-$level);?>">

        <?php
            $tcs = Hash::extract($task['Assist'],'{n}.time_control');
            $task['Assist'] = Hash::sort($task['Assist'], '{n}.time_offset', 'asc');
                    
            // At least one team is controlled
            if(in_array(1, $tcs)): ?>
                <div class="row xs-bot-marg">
                    <div class="col-xs-12">
                        <h5><i class="fa fa-clock-o"></i> <b>Time Controls (1 Level Down)</b></h5>
                    <?php 
                        foreach($task['Assist'] as $as){
                            if(!$as['time_control']){
                                continue;
                            }
                            echo $this->Ops->subtask($as, array('multi_line'=>true, 'show_offset'=>true));
                        }
                    ?>
                    </div>
                </div>
            <?php 
            endif;
            
            if(in_array(0, $tcs)): ?>
                <div class="row xs-bot-marg">
                    <div class="col-xs-12">
                        <h5><i class="fa fa-sitemap"></i> <b>Incoming Links (1 Level Down)</b></h5>
                    <?php 
                        foreach($task['Assist'] as $as){
                        if($as['time_control']==1){
                                continue;
                            }
                            echo $this->Ops->subtask($as, array('show_details'=>true, 'multi_line'=>true,'show_view'=>true));
                        }
                    ?>
                    </div>
                </div>
        <?php 
            endif;
        endif; 
        ?>
    </div>
</div>


<?php 
  
  }
 
 /*



    <div class="row">
        <div class="col-md-12">
            <?php 
                if(empty($task['Parent']['id']) && empty($task['Assist'])){
                    echo '<br><br><div class="alert alert-info"><b>No Links: </b>There are currently no links to or from this task.</div>';
                }
                if (!empty($task['Parent']['id'])): ?>
                    <div class="row xs-bot-marg">
                        <div class="col-xs-12">
                        <?php 
                            if($task['Parent']['id'] && ($task['Task']['time_control']==1)){
                                $off = ($task['Task']['time_offset'] <>0)? '('.$task['Task']['time_offset'].' min)': '';
                                $relType ='<i class="fa fa-clock-o"></i> <b>Time Synced To '.$off.'</b>' ; 
                            }
                            else{
                                $relType ='<i class="fa fa-level-up"></i> <b>Linked To</b>';
                            }
                            ?>
                            <h5><?php echo $relType;?></h5>
                        <?php 
                            echo $this->Ops->subtaskMultiWithView($task['Parent']);
                        ?>
                        </div>
                    </div>
                <?php endif; 
                
            
                
                if (!empty($task['Assist'])):
                    $tcs = Hash::extract($task['Assist'],'{n}.time_control');

                    // At least one team is controlled
                    if(in_array(1, $tcs)): ?>
                        <div class="row xs-bot-marg">
                            <div class="col-xs-12">
                            <h5><i class="fa fa-clock-o"></i> <b>Controls Start Of</b></h5>

                            <?php 
                                foreach($task['Assist'] as $as){
                                    if(!$as['time_control']){
                                        continue;
                                    }
                                    echo $this->Ops->subtaskMultiWithView($as);
                                }
                            ?>
                            </div>
                        </div>
                <?php 
                    endif;
                    
                    if(in_array(0, $tcs)): ?>
                        <div class="row xs-bot-marg">
                            <div class="col-xs-12">
                            <h5><i class="fa fa-level-down"></i><i class="fa fa-group"></i> <b>Incoming Links</b></h5>

                            <?php 
                                foreach($task['Assist'] as $as){
                                if($as['time_control']==1){
                                        continue;
                                    }
                                    echo $this->Ops->subtaskMultiWithView($as);
                                }
                            ?>
                            </div>
                        </div>
                <?php 
                    endif;
                endif; 
                ?>
        </div>
    </div>
*/
?>
