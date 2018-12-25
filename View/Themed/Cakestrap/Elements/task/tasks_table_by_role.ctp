<?php

//$this->log($tasks);
    if(!empty($tasks)){
        $cntTasks = count($tasks);
        $t_ids = Hash::extract($tasks, '{n}.Task.id');
        
    }

    $start = isset($start_date)?$start_date:null;
    $end = isset($end_date)?$end_date:null;
    $userRoles = isset($userRoles)? $userRoles: array();
    $selected = $selected_short = array();
    
    if($userRoles){
        foreach ($userRoles as $role){
            $selected[$role] = $rolesList[$role];
        }
        if(count($userRoles)<6){
            $selected_short = $this->Ops->oxfordComma($selected);
        }
        $selected = $this->Ops->oxfordComma($selected);
    }
        
?>

<?php if(!empty($tasks)):?>
<h2><?php echo Configure::read('EventShortName');?>: Custom Plan <?php echo (!empty($selected_short))? 'for '.$selected_short:'By Role';?></h2> 
    
<p>Compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></p>
<?php echo '<p>Showing '.$cntTasks.' tasks assigned to <b>'.$selected.'</b> from '.date('M d\/Y', strtotime($start)).' to '.date('M d\/Y', strtotime($end)).'</p>';?> 

<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class='col-md-12'>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7px; padding:0px;"></th>
                            <th style="width: 80px;">Date</th>
                            <th style="width: 150px;">Type / Teams</th>
                            <th style="width: 820px;">Description</th>
                        </tr>
                    </thead>
                    <tbody>    
                        <?php
                            $pST = null;
                            foreach($tasks as $task){
                                $ass_handles = Hash::extract($task['Assignment'], '{n}.role_handle');
                                $hide_det = false;
                                
                                $cST = date('Y-m-d', strtotime($task['Task']['start_time']));
                                //$cET = date('Y-m-d', strtotime($task['Task']['end_time']));
                                $sameDay = ($cST == $pST)? true : false;
                                
                                echo '<tr>';
                                echo '<td style="padding:0px; background: '.$task['Task']['task_color_code'].'"></td>';
                                echo '<td>';
                                //echo ($sameDay)? $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true, false): $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true);
                                echo ($sameDay)? $this->Ops->durationGeneric($task['Task']['start_time'], $task['Task']['end_time'], array('show_zero'=>true, 'show_date'=>false)): $this->Ops->durationGeneric($task['Task']['start_time'], $task['Task']['end_time'], array('show_zero'=>true, 'show_date'=>true));
                                echo '</td><td><b>'.$task['Task']['task_type'].'</b><br>';
                                echo $this->Ops->pdfSig2016($task['TasksTeam'], $zoneTeamCodeList).'</td>';
                                echo '<td>'.$task['Task']['short_description'];
                                echo '&nbsp;&nbsp;';
                                echo $this->Ops->makeAssignmentButtons($ass_handles).'&nbsp;';
                                if($task['Task']['details'] && !$hide_det){
                                    echo '<hr class="hr-slim">';
                                    echo nl2br($task['Task']['details']);    
                                }
                                echo '</td>';
                                echo '</tr>';
                                
                                $pST = $cST;
                            }    
                        ?>
                    </tbody>
                </table>
                <span>Showing <?php echo $cntTasks.' tasks ';  ?></span>
            </div>
        </div>
    </div>
</div>
<?php endif;?>

<?php if(empty($tasks)):?>
    <div class="alert alert-warning"><i class="fa fa-info-circle"></i> <b>No Tasks</b> were found for your selected roles. Please try again or try searching for another role.</div>
<?php endif;?>