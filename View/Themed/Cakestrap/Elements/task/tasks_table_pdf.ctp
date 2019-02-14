<?php 

    $phd = $printPrefs['hide_detail'];
    $pht = $printPrefs['hide_task'];
    
    $cntTasks = count($tasks);
    $cntHidden = 0;
    
    $t_ids = Hash::extract($tasks, '{n}.Task.id');
    
    if(!empty($pht)){
        $cntHidden = count(array_intersect($t_ids, $pht));    
    }
    
    $cntShown = $cntTasks - $cntHidden;
    
    $cs_teams = array();
    foreach ($cSettings['Teams'] as $tid){
        $cs_teams[] = $teamIdCodeList[$tid];
    }
    
?>

<h2><?php echo Configure::read('EventShortName');?> Compiled Plan</h2>
<span>Compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/><br/>
    <?php 
        echo '<p>Showing '.$cntShown.' tasks for '.implode(', ', $cs_teams).' from ' .date('M j\/Y', strtotime($cSettings['start_date'])). ' to '.date('M j\/Y', strtotime($cSettings['end_date'])).'</p>';
    ?> 

<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class='col-md-12'>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 7px;"></th>
                                    <th style="width: 100px;">Date</th>
                                    <th style="width: 200px;">Teams</th>
                                    <th style="width: 750px;">Description</th>
                                </tr>
                            </thead>
                            <tbody>    
                                <?php
                                    $pST = null;
                                    foreach($tasks as $task){
                                        
                                        $ass_handles = Hash::extract($task['Assignment'], '{n}.role_handle');
                                        
                                        
                                        
                                        
                                        $hide_det = false;
                                        
                                        if(in_array($task['Task']['id'], $pht)){
                                            //$cntHidden++;
                                            continue;
                                        }
                                        if(in_array($task['Task']['id'], $phd)){
                                            $hide_det = true;
                                        }
                                        
                                        $cST = date('Y-m-d', strtotime($task['Task']['start_time']));
                                        //$cET = date('Y-m-d', strtotime($task['Task']['end_time']));
                                        $sameDay = ($cST == $pST)? true : false;
                                        
                                        
                                        echo '<tr>';
                                        echo '<td style="background: '.$task['Task']['task_color_code'].'"></td>';
                                        echo '<td>';
                                        if($sameDay){
                                            echo $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true, false);    
                                        }
                                        else{
                                            echo $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true);
                                            
                                        }
                                        
                                        echo '</td><td><b>'.$task['Task']['task_type'].'</b><br>';
                                        echo $this->Ops->pdfSig2016($task['TasksTeam'], $zoneTeamCodeList).'</td>';
                                        echo '<td>'.$task['Task']['short_description'].'&nbsp;'.$this->Ops->makeAssignmentButtons($ass_handles).'&nbsp;';
                                        
                                        
                                        if(isset($task['Task']['details']) && strlen($task['Task']['details'])>2 && !$hide_det){
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
                    <span>Showing <?php echo $cntShown.' tasks ';
                        if($cntHidden > 0){
                            echo '('.$cntHidden.' hidden due to user preferences)';
                        }
                        ?>
                    </span>
            </div>
        </div>
    </div>
</div>

    




