<?php

    $show_threaded = $this->request->data('Task.show_threaded');
    $show_details = $this->request->data('Task.show_details');
    $color_team = $this->request->data('Task.color_team');
    $showteams = $this->request->data('Task.Teams');
    $privateteams = $this->request->data('Task.Private');
    
    
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

<h2>DB2014 Compiled Plan</h2>
<span>Plan compiled on <?php echo date('D M d, Y \a\t g:iA'); ?></span><br/>

    <?php 
        $sta = array();
        foreach ($showteams as $st){
            $sta[] = $teamsList[$st];
        }
        echo '<span><b>Showing Tasks For:</b> '. implode(', ', $sta) . '</span>';
    
        if(!empty($privateteams)){
            $pta = array();
            foreach ($privateteams as $pt){
                $pta[] = $teamsList[$pt];
            }
        echo '<br/><span><b>Including Private Tasks For:</b> '. implode(', ', $pta) . '</span>';
        }
        

        
        //$this->log($this->pdfConfig);
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
                    <td 
                    <?php 
                        if ($color_team){
                            echo 'style="background:'.$task['Task']['task_color_code'].'"';
                        }
                     ?>>
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
                        $tt_c = Hash::extract($tt, '{n}[task_role_id=2]');
                        
                        
                        $tt_lp = Hash::extract($tt, '{n}[task_role_id=3].team_code');
    
                        $tc = array();
                                
                        if(!empty($tt_c)){
                            foreach ($tt_c as $cteam){
                                $tc[] = $cteam['team_code'];
                            }
                        }
                        
                                
                        $buttons = '';
                            if (!empty($tt_l)){
                                $buttons.= '<span class="btn-lt">'.$tt_l[0].' </span>&nbsp;';
                            }
                            if (!empty($tt_lp)){
                                $buttons.= '&nbsp;&gt;&gt; <span class="btn-lpt">'.$tt_lp[0].' </span>&nbsp;';
                            }

                            foreach ($tc as $ct_code){
                                $buttons.= '<span class="btn-ctu">'.$ct_code.' </span>&nbsp;';
                            }
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
                            
                            if(!empty($task['Subtask']) && $show_threaded){
                                echo '<hr style="margin-bottom:0;" />';
                                echo '<u>Subtasks:</u><br/>'; 
                        
                                foreach ($task['Subtask'] as $st){
                                    $tSig = makeIts($st['TasksTeam']);
                                    echo date('M d H:i', strtotime($st['start_time'])) .' '; 
                                    echo $tSig.'  ';
                                    echo $st['short_description'];
                                    echo '<br/>';
                                }
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
