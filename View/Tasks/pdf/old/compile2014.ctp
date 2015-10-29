<?php

    $show_threaded = $this->request->data('Task.show_threaded');
    $show_details = $this->request->data('Task.show_details');
    $color_team = $this->request->data('Task.color_team');
    $showteams = $teams;
    //$this->request->data('Task.Teams');
    //$privateteams = $this->request->data('Task.Private');
    
    
    function s2human($s, $e) {
        $time1 = strtotime($s);
        $time2 = strtotime($e);
        $ss = $time2 - $time1;
        
        if($ss > 60){
            $m = floor(($ss%3600)/60);
            $h = floor(($ss%86400)/3600);

            if ($h>0){
                return "$h hr, $m min";    
            }
        
            else {
                return "$m min";
            }
        }
        
        return '';
    }
    
    function makeIts($tt=array()){
        $buttons = '';
        $lead_team = null;
        $linked_parent_team = null;

        foreach ($tt as $k=>$a):
            if ($a['task_role_id']==1){
                $lead_team = $a['team_code'];
            }
            elseif ($a['task_role_id'] == 3){
                $linked_parent_team = $a['team_code'];
            }   
        endforeach;

        if (!empty($lead_team)){
            $buttons.= '<span class="btn-lt">'.$lead_team.'</span>';
        }

        if (!empty($linked_parent_team)){
            $buttons.= '&nbsp;&gt;&gt; <span class="btn-lpt">'.$linked_parent_team.'</span>';
        }
        
        return $buttons;
    }
    
    
    
?>

<h2>DB2015 Compiled Plan</h2>
<span>Plan compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/>

    <?php 
        $show = array();
        //print_r($cteams);
        //print_r($teamsList);
        
        foreach ($cteams as $tid){
            
                $show[] = $teamsList[$tid];    
            
            
        }
        //echo ($teams);
        echo '<span><b>Showing Tasks For:</b> '. implode(', ', $show) . '</span>';
        
    ?>

    
<?php 
    if (!empty($tasks)){ ?>
        <table style="width:100%" class="table table-condensed table-bordered">
            <thead>
                <tr>
                    <th width="1%"> </th>
                    <th width="8%">Time</th>
                    <th width="19%">Task Type || Teams</th>
                    <th width="72%">Description</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td style="background-color:<?php echo $task['Task']['task_color_code'];?>">
                    </td> 
                    <td>
                        <?php
                            echo $this->Time->format('M d H:i', $task['Task']['start_time']);
                            echo '<br />';
                            echo s2human($task['Task']['start_time'], $task['Task']['end_time']);
                        ?>
                    </td>
                    <td>
                    <?php 
                        echo '<b>'.$task['Task']['task_type'].'</b>&nbsp;||&nbsp;';  
                        $tt = $task['TasksTeam'];
                        $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
                                    $tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_id');
                                    $tt_r = Hash::extract($tt, '{n}[task_role_id=3].team_id');

                                    $buttons13 = '';
                                    $buttons2 = '';
                            
                                foreach ($task['TasksTeam'] as $k => $tat) {
                                    if($tat['task_role_id'] == 1){
                                        $buttons13.= '<span class="btn btn-leadt">'.$tat['team_code'].'</span>';
                                    }    
                                    elseif ($tat['task_role_id']==2) {
                                        $buttons2.= '<span class="btn btn-default btn-xxs">'.$tat['team_code'].'</span>';
                                    }
                                    elseif ($tat['task_role_id']==3) {
                                        $buttons13.= '<span class="btn btn-danger btn-xxs">'.$tat['team_code'].'</span>';
                                    }
                                }
                                //This is a retarded way to show requests before pushes
                                $buttons = $buttons13.$buttons2;
                                echo $buttons;
                       
                       
                       ?>
                    </td>
    
                    <td>
                    <?php 
                        echo $task['Task']['short_description'];
                            if($show_details && !empty($task['Task']['details'])){
                                echo '<hr style="margin-bottom:0;" />';
                                echo '<u>Details:</u><br/>'; 
                                echo nl2br($task['Task']['details']);
                            }
                            
                    ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>



<?php
    echo 'Displaying '.$task_count. ' tasks.<br/>' ;
    }
    else {
        echo 'No tasks matched your search parameters.  Please try refining your search terms.';
    }
?>
