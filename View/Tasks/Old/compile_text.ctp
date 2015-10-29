<?php
    $show_details = true;
    
    $printPrefs = array();
    if(!$printPrefs){
        $printPrefs['hide_task']=array();
        $printPrefs['hide_detail']=array();
    }
    
    $ztlist = array();
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
    
    $task_count = (count($tasks)>0)? count($tasks): 0;
    
       
?>
<h2>DB2015 Compiled Plan</h2>
<span>Plan compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/>
    <?php 
        $show = array();
        foreach ($cteams as $tid){
            $show[] = $teamsList[$tid];    
        }
        
        echo '<span><b>Showing Tasks For:</b> '. implode(', ',$show) . '</span><br><br>';
    ?>
<?php 
    if (!empty($tasks)){ 
        ?>
        <table>
            <thead>
                <tr>
                    <th width="9%">Start</th>
                    <th width="9%">End</th>
                    <th width="5%">Type</th>
                    <th width="4%">Lead</th>
                    <th width="8%">Assisting</th>
                    <th width="65%">Description</th>
                </tr>
            </thead>
            <tbody>
            <?php 
            // Hold days of tasks
    $cur_t_day = '';
    $prev_t_day = '';
    $last_t_day = '';
    $last_t_hr = '';
    $curr_t_day = '';            
    $curr_t_hr = '';
    
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  
            
        foreach ($tasks as $task): 
            $hide_task = false;
            $hide_det = false;
            $tid = $task['Task']['id'];

            if(in_array($tid, $printPrefs['hide_task'])){
                continue;
                $hide_task = true;
            }
        
            if(in_array($tid, $printPrefs['hide_detail'])){
                $hide_det = true;
            }
                
                
            
            $daysAreSame = false;
        $onEday = false;
        $hoursAreSame = false;
        
        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        
        
        
        $curr_t_hr = date('H', strtotime($task['Task']['start_time']));
       
        if($last_t_day == $curr_t_day){
            $daysAreSame = true;
        }
        
        if($curr_t_day == $eday){
            $onEday = true;
        }
        
        if($curr_t_hr == $last_t_hr){
            $hoursAreSame = true;
        }
            
            ?>

                <tr>
                    <td><?php echo $this->Time->format('Y-m-d H:i:s', $task['Task']['start_time']);?></td>        
                    <td><?php echo $this->Time->format('Y-m-d H:i:s', $task['Task']['end_time']); ?></td>
                    <td><?php echo '<b>'.$task['Task']['task_type']; ?></b></td>
                    <td><?php $teams = $this->Ops->makePdfTeamsSig2015($task['TasksTeam'], $ztlist);
                        echo $teams['lead'];?>
                    </td>
                    <td><?php echo $teams['pushed'];?>&nbsp;</td>
                    <td>
                    <?php 
                        echo $task['Task']['short_description'];
                            if($show_details && !empty($task['Task']['details'])){
                                echo '<hr>';
                                echo nl2br($task['Task']['details']);
                            }
                    ?>
                    </td>
                </tr>
            <?php 
            
                $last_t_day = $curr_t_day;
    $last_t_hr = $curr_t_hr; 
            endforeach; ?>
            </tbody>
        </table>



<?php
    echo 'Displaying '.$task_count. ' tasks.<br/>' ;
    }
    else {
        echo 'No tasks.';
    }
?>
