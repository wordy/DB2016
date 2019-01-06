
<div id="Task<?php echo $task['Task']['id'];?>" data-tid=<?php echo ($task['Task']['id']);?> data-task_id=<?php echo ($task['Task']['id']);?> data-team_id=<?php echo $task['Task']['team_id'];?> data-start_time="<?php echo $task['Task']['start_time'];?>" data-uconinv="<?php echo ($uControlsInvolved)?TRUE:FALSE;?>" data-cin='<?php echo $jsonCIN; ?>' id="tid<?php echo ($task['Task']['id']); ?>" class="panel panel-default task-panel" style="border-left: 8px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
    <div class="panel-heading task-panel-heading">
        <div class="row">
            <div class="col-xs-5 col-sm-3 col-md-3">
                <div class="sm-bot-marg">  
                 <?php
                    if($view_type != 100){
                        echo $this->Ops->startTimeFriendly($task['Task']['start_time'], $task['Task']['end_time'], array(
                            'date'=>true,
                            'line_break_duration'=>true, 
                            'line_break_multiday'=>false, 
                            'duration'=>true));
                    }
                    else { // When showing by created date, must show task date since overall tasks are sorted by created date (not task date)
                        echo date('M j g:i A', strtotime($task['Task']['start_time']));
                    } 
                ?>
                </div>
                <div><?php echo $this->Ops->makeTeamsSig($task['TasksTeam'], $zoneTeamCodeList, $userControls);?></div>
            </div>
            
            <div class="col-xs-7 col-sm-6 col-md-7">
                <div class="row">
                    <div class="csTaskDetails col-xs-12"><?php
                            echo '<span class="h5"><strong><em>'.$task['Task']['task_type'].'</em></strong>&nbsp;&nbsp;'.$task['Task']['short_description'].'</span><br/>';
                            
                            if($hasAssignment){
                                echo '<div class="sm-top-marg">';
                                foreach($task['Assignment'] as $n =>$ass){
                                    echo '<button type="button" class="btn btn-orange btn-xs noProp"><i class="fa fa-at"></i>&nbsp;'.$ass['role_handle'].'</button>';    
                                }
                                echo '</div>';
                            }
                       
                            if (!empty($task['Task']['details'])){
                                echo '<div class="divTaskDetails">';
                                echo '<hr align="left" style="width: 98%; margin-bottom:0.5em; margin-top:0.5em; border-top: 1px solid #999;"/>';
                                echo nl2br($task['Task']['details']);
                                echo '</div>';
                            }else{echo '';} 
                        ?>
                    </div>
                </div>

            </div>
            <div class="col-xs-12 col-sm-3 col-md-2">
                <div class="row">
                    <div class="col-md-12">
                        <div class="task-buttons <?php if($isTimeshiftable){echo 'canCollapse';};?>" style="text-align: right;">
                        <?php 
                            if($hasDueDate){
                                echo '<button type="button" class="btn btn-danger btn-xs noProp"><i class="fa fa-bell-o"></i>&nbsp;'.$this->Time->format('M d', $task['Task']['due_date']).'</button>';
                            }
                            if($hasActionable){
                                echo '<button type="button" class="btn btn-danger btn-xs noProp"><i class="fa fa-flag fa-lg"></i>&nbsp;'.$task['Task']['actionable_type'].'</button>';
                            }
                            if($hasChange && $hasNewChange){
                                echo '<button type="button" class="btn btn-success btn-xs noProp"><i class="fa fa-exchange"></i>&nbsp;'.$numChange.'</button>';
                            }
                            if($hasComment){
                                echo '<button type="button" class="btn btn-info btn-xs noProp"><i class="fa fa-comment-o"></i>&nbsp;'.$commentCount.'</button>';
                            }
                            if(($isTimeControlled) && ($taskTO != 0)){
                                if($taskTO_type == -1 || $taskTO_type == 1){
                                    echo '<button type="button" class="btn btn-xs btn-info noProp">'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).' <i class="fa fa-clock-o"></i>&nbsp;</button>';    
                                }
                                elseif ($taskTO_type == -2 || $taskTO_type == 2) {
                                    echo '<button type="button" class="btn btn-xs btn-info noProp"><i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).'</button>';    
                                }
                            }
                            else if(($isTimeControlled) && ($taskTO == 0)){
                                echo ($taskTO_type == -1 || $taskTO_type == 1)? '<button type="button" class="btn btn-xs btn-info noProp">'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).' <i class="fa fa-clock-o"></i>&nbsp;</button>':'<button type="button" class="btn btn-xs btn-info noProp"><i class="fa fa-clock-o"></i>&nbsp;'.$this->Ops->offsetToFriendly($taskTO, $taskTO_type).'</button>';    
                            }
                                
                            echo '<span class="taskTs"><span class="tsCheck id="hide'.$task['Task']['id'].'" data-tid="'.$task['Task']['id'].'" /><span class="taskTimeshift" data-taskid="hide'.$task['Task']['id'].'"></span></span>';
                        
/*
 *                             <!--<div class="btn-group" role="group">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></button>
                                <ul class="dropdown-menu">
                                  <li><a href="#">Dropdown link</a></li>
                                  <li><a href="#">Dropdown link</a></li>
                                </ul>
                            </div>-->
 * 
 */                        
                        ?> 
                        </div>
                    </div>
                </div>
                
            <?php if($isTimeshiftable):?>
                <div class="pull-right task-timeShift <?php echo (!$timeshift_mode)?'hide':'';?>">
                    <div class="tsButtonWrap">
                        <div class="row xs-bot-marg">
                            <div class="col-xs-12">
                                <span><i class="fa fa-clock-o"></i> <b>Shift Task</b></span><br/>
                                <div class="input-group xs-bot-marg">
                                    <input type="number" min="-120" max="120" class="form-control input-sm tsInputMag noProp" value="0">
                                    <span class="input-group-btn"><button type="button" class="btn btn-primary btn-sm noProp tsUnitBtn"><?php echo $timeshift_unit ?></button></span>
                                </div>
                            </div>
                            <div class="col-xs-12"><button type="button" class="btn btn-block btn-success btn-sm noProp tsSaveBtn"><i class="fa fa-save fa-lg"> </i> Save</button></div>
                        </div>
                    </div>
                </div>
            <?php endif;?>
            </div>
        </div><!--row-->
        <div class="taskLinkages"><?php 
            if (!empty($task['Parent']['id'])): ?>
                <div class="row xs-bot-marg sm-top-marg">
                    <div class="col-md-3"><h5><?php echo ($task['Parent']['id'] && ($task['Task']['time_control']==1))? '<i class="fa fa-history"></i>&nbsp;<b>Synced To</b>':'<i class="fa fa-link"></i>&nbsp; <b>Linked To</b>';?></h5></div>
                    <div class="col-md-9"><?php echo $this->Ops->subtaskRowSingleWithOffset($task['Parent']);?></div>
                </div>
            <?php endif; 
            if (!empty($task['Assist'])):
                $ass = Hash::combine($task['Assist'], '{n}.id', '{n}','{n}.time_control');

                if(isset($ass[1])){
                    // Re-sort Time Controlled tasks by offset to highlight timing (before, synced, after)
                    $arrAss = array();
                    
                    foreach($ass[1] as $k => $v){
                        $sign = 0;
                        // "most before" the task should be first in an ascending list
                        switch ($v['time_offset_type']) {
                            case -1:
                                $sign = -1;
                                break;
                            case -2:
                                $sign = 500;
                                break;
                            case 2:
                                $sign = 5000;
                                break;
                            default:
                                $sign = 1;                                                            
                                break;
                        }
                        
                       $v['time_sort'] = (int)$v['time_offset']*$sign;
                        $arrAss[$k] =  $v;
                    }
                    $tc_tasks = Hash::sort($arrAss, '{n}.time_sort', 'asc');
                    unset($arrAss);
            ?>
                    <div class="row xs-bot-marg sm-top-marg">
                        <div class="col-md-3"><h5><i class="fa fa-clock-o"></i>&nbsp;<b>Synced Tasks</b></h5></div>
                        <div class="col-md-9">
                            <?php
                                foreach($tc_tasks as $tid => $tsk){
                                    echo $this->Ops->subtaskRowSingleWithOffset($tsk);
                                }
                            ?>
                        </div>
                    </div>
                <?php
                }
                if(isset($ass[0])):?>
                    <div class="row xs-bot-marg sm-top-marg">
                        <div class="col-md-3"><h5><i class="fa fa-sitemap"></i>&nbsp; <b>Linked Tasks</b></h5></div>
                        <div class="col-md-9">
                            <?php
                                foreach($ass[0] as $tid => $tsk){
                                    echo $this->Ops->subtaskRowSingleWithOffset($tsk);
                                }
                            ?>
                        </div>
                    </div>
                <?php
                endif;
            endif;?>
        </div>
    </div>
    <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>" style="display:none;"></div>    
</div>
 
<?php echo $this->Js->writeBuffer(); ?>