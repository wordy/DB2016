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

<h1><?php 

    $view = $this->Session->read('Auth.User.Compile.view_type');
    
    if($view != 500){
        echo Configure::read('EventShortName'). ' Compiled Plan'; 
    }
    elseif($view == 500){
        echo Configure::read('EventShortName'). ' Action Items';
    }
 

?></h1>
<span>Compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/><br/>
    <?php
        if($view == 10){
            echo '<p>Showing '.$cntShown.' tasks <b>lead by</b> '.implode(', ', $cs_teams).' from ' .date('M d\/Y', strtotime($cSettings['start_date'])). ' to '.date('M d\/Y', strtotime($cSettings['end_date'])).'</p>';    
        } 
        elseif($view != 500){
            echo '<p>Showing '.$cntShown.' tasks for '.implode(', ', $cs_teams).' from ' .date('M d\/Y', strtotime($cSettings['start_date'])). ' to '.date('M d\/Y', strtotime($cSettings['end_date'])).'</p>';    
        } 
    ?> 

<div class="row">
    <div class="col-xs-12">
        <div class="row">
            <div class='col-md-12'>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 7px; padding:0px;"></th>
                            <th style="width: 150px;">Date</th>
                            <th style="width: 150px;">Teams</th>
                            <th style="width: 750px;">Description</th>
                        </tr>
                    </thead>
                    <tbody>    
                        <?php
                            $pST = null;
                            foreach($tasks as $task){
                                $hide_det = false;
                                
                                if(in_array($task['Task']['id'], $pht)){
                                    continue;
                                }
                                if(in_array($task['Task']['id'], $phd)){
                                    $hide_det = true;
                                }
                                
                                $cST = date('Y-m-d', strtotime($task['Task']['start_time']));
                                //$cET = date('Y-m-d', strtotime($task['Task']['end_time']));
                                $sameDay = ($cST == $pST)? true : false;
                                
                                echo '<tr>';
                                echo '<td style="padding:0px; background: '.$task['Task']['task_color_code'].'"></td>';
                                echo '<td>';
                                echo ($sameDay)? $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true, false): $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true);
                                
                                echo '</td><td><b>'.$task['Task']['task_type'].'</b><br>';
                                echo $this->Ops->pdfSig2016($task['TasksTeam'], $zoneTeamCodeList).'</td>';
                                echo '<td>'.$task['Task']['short_description'];
                                
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

    




