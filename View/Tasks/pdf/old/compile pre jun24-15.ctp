<?php
    $sd = $this->Session->read('Auth.User.Compile.show_details');
    
    $show_details = ($sd == 0)? false:true; 
    
    $PrintPrefs['hide_task'] = (!empty($PrintPrefs['hide_task']))?$PrintPrefs['hide_task'] : array();
    $PrintPrefs['hide_detail'] = (!empty($PrintPrefs['hide_task']))?$PrintPrefs['hide_task'] : array();
    
    //debug($printPrefs);
    $ztlist = array();
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
    
    $task_count = (count($tasks)>0)? count($tasks): 0;
    
    $tlist=array();
    foreach ($teams as $zone){
        foreach($zone as $tid=>$code){
            $tlist[$tid] = $code;
        } 
    }
    
    //debug($cSettings);
    
    $cs_teams=array();
    foreach ($cSettings['Teams'] as $tid){
        $cs_teams[] = $tlist[$tid];
    }
    
    
    //debug($userPrefs);
       
?>
<h2>DB2015 Compiled Plan</h2>
<span>Plan compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/>
         <?php
            echo '<p>Showing tasks for: '; 
            echo implode(', ', $cs_teams);
            echo ' from ' .date('M d Y', strtotime($cSettings['start_date'])). ' to '.date('M d Y', strtotime($cSettings['end_date']));
            echo '</p>';
         ?>   
<?php 
    if (!empty($tasks)){ 
        ?>
        <table>
            <thead>
                <tr>
                    <th width="6%">Start</th>
                    <th width="6%">End</th>
                    <th width="5%">Type</th>
                    <th width="4%">Lead</th>
                    <th width="8%">Assisting</th>
                    <th width="71%">Description</th>
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
            $hide_det = false;
            $tid = $task['Task']['id'];

            if(in_array($tid, $PrintPrefs['hide_task'])){
                continue;
            }
        
            if(in_array($tid, $PrintPrefs['hide_detail']) || !$show_details){
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

<?php 

            if(!$daysAreSame){
                echo '<tr class="dateTr"><td><b>'.date('M j', strtotime($curr_t_day)).'</b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>';
            }
            
            elseif($onEday && !$hoursAreSame){
                echo '<tr class="dateTr"><td><b>'.date('g A', strtotime($task['Task']['start_time'])).'</b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>';
            }
            
            elseif($onEday && !$hoursAreSame && !$daysAreSame){
                echo '<tr class="dateTr"><td><b>'.date('M j g A', strtotime($task['Task']['start_time'])).'</b></td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>';
            }
        ?>
       
        <tr style="border-left: 5px solid <?php echo $task['Task']['task_color_code'];?>;">

<!--
                    <td bgcolor="<?php echo $task['Task']['task_color_code'];?>">
                    </td> -->
                    <td style="border-left: 5px solid <?php echo $task['Task']['task_color_code'];?>;">
                        <?php echo $this->Time->format('H:i:s', $task['Task']['start_time']);?>    
                    </td>
                    <td>
                        <?php echo $this->Time->format('H:i:s', $task['Task']['end_time']); ?>
                    </td>
                    <td>
                        <b><?php echo $task['Task']['task_type']; ?></b>
                    </td>
                    <td>
                        <?php $teams = $this->Ops->makePdfTeamsSig2015($task['TasksTeam'], $ztlist);
                            echo '<b>'.$teams['lead'][0].'</b>';?>
                    </td>

                    <td>
                        <?php 
                        
                        $hidePush = false;
                        if(!empty($teams['assist'])){
                            if(count($teams['assist'])>=10){
                                echo 'ALL';
                                $hidePush = true;
                            }
                            else{
                                echo implode(', ', $teams['assist']);
                            }    
                        }
                        
                        if(!empty($teams['pushed']) && !$hidePush){
                            if(!empty($teams['assist'])){
                                echo ', ';
                            }
                            
                            echo implode(', ', $teams['pushed']);    
                        }
                    ?>
                    </td>

                    <td>
                    <?php 
                        echo $task['Task']['short_description'].'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
                            if(!$hide_det && !empty($task['Task']['details'])){
                                echo '<br/>';
                                echo nl2br($task['Task']['details']);
                            }
                    ?>
                    </td>
                </tr>

                    
                    
                
            
            
            <?php 
            
                $last_t_day = $curr_t_day;
    $last_t_hr = $curr_t_hr; 
    
    
    
    
    
            endforeach; ?>
            </tbody>            </table>



<?php
    echo 'Displaying '.$task_count. ' tasks.<br/>' ;
    }
    else {
        echo 'No tasks.';
    }
?>
