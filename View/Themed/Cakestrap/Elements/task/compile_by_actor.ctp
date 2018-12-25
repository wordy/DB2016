<?php
    $userTeams = (AuthComponent::user('id'))? AuthComponent::user('id'): array();

    //debug($this->Session->read('Auth.User.Timeline.hour'));
//debug($tasks);

    function pctOffsets($start, $end, $rng_start, $rng_end){
        if(isset($start) && isset($end) && isset($rng_start) && isset($rng_end)){
            if(strtotime($start) < strtotime($rng_start)){
                $start = $rng_start;
            }
            if(strtotime($end) > strtotime($rng_end)){
                $end = $rng_end;
            }
            $length = strtotime($end) - strtotime($start);
            $len_mins = round($length/60,1);
            $start_s_offset = strtotime($start)-strtotime($rng_start); 
            $lenpct = ($len_mins*10/6 > 5)? round(100/60*$len_mins,1): 3;
            $lpct = round($start_s_offset/36,1);
            $rpct = ((100-$lpct - $lenpct)>=0)? (100- $lpct - $lenpct): 0;
            
            return array(
                'left_pct'=>$lpct,
                'length_pct'=>$lenpct,
                'right_pct'=>$rpct,
            );
        }
    }
    
     $actAss = $teamAss = $actors = $tasksByTeamAndRole = array();
     foreach ($tasks as $id => $t){
         $ass = $t['Assignment'];
         if(empty($ass)){
             $tasksByTeamAndRole[$tasks[$id]['Task']['team_code']]['Unassigned'][$tasks[$id]['Task']['team_code']][] = $tasks[$id];
         }
         else{
             foreach($ass as $k => $role){
                 $tasksByTeamAndRole[$tasks[$id]['Task']['team_code']]['Assigned'][$role['role_handle']][] = $tasks[$id];
                 $atrs[] = $actor['role_handle'];
             }
         }
     }
     //unset($tasks);
     ksort($tasksByTeamAndRole);
     
     //ksort($actAss[$tasks[$id]['Task']['team_code']]);
     //$actors = array_unique($atrs);
     //sort($actors);
     //debug($teamAss);
     
     
     //debug($actAss);
     
     //array_unshift($tByTeam, array('BLA'=>$teamAss));
     
     //$tasksByTeamAndActor = array_merge($teamAss, $actAss);
     //debug($tByTeam);
     //debug($tasksByTeamAndActor);
     
     
     
    $user_controls = $this->Session->read('Auth.User.Teams');
    $single_task = (isset($single_task))? $single_task:0;
    $search_term = (isset($search_term))? $search_term:null;
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    $view_links = ($this->Session->read('Auth.User.Compile.view_links'))? 1:0;
    $view_details = ($this->Session->read('Auth.User.Compile.view_details'))? 1:0;
    $view_threaded = ($this->Session->read('Auth.User.Compile.view_threaded'))? 1:0;
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    $timeshift_mode = $this->Session->read("Auth.User.Timeshift.Mode");
    $timeshift_unit = $this->Session->read("Auth.User.Timeshift.Unit");
    
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  

    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = $viewRange = $viewTeamsStr = '';
    $viewStartDate = date('M j, Y', strtotime($start_date));
    $viewEndDate = date('M j, Y', strtotime($end_date));
    $viewSort = (int)$sort;
    $viewSort = ($viewSort == 0) ? 'ascending start time':'descending start time';    
            
    if(!empty($comp_teams)){
        foreach($comp_teams as $k=>$tid){
            //$viewTeams[] = $teamIdCodeList[$tid];
        }
    
        // Oxford comma on teams list
        $viewTeamsCount = count($viewTeams);
        $last_t = array_pop($viewTeams);
        $noLastTeam = implode(', ', $viewTeams);
        
        if ($noLastTeam){
            if($viewTeamsCount > 2){
                $noLastTeam .= ', and ';    
            }
            elseif($viewTeamsCount == 2) {
               $noLastTeam .= ' and '; 
            }
        }
    
        $viewTeamStr = $noLastTeam.$last_t;
    }else{
        $viewTeamStr = '<No Teams>';
    }
    
    debug($time_range);
    $rng_s_hr = date('H', strtotime($time_range['start']));
    $rng_s_time = date('g:i A', strtotime($time_range['start']));
    $rng_e_time = date('g:i A', strtotime($time_range['end']));
    
    debug($rng_s_time);
    debug($rng_e_time);
    $next_hr = date('gA', strtotime($time_range['start'])+3600);
    $prev_hr = date('gA', strtotime($time_range['start'])-3600);
?>

<h1><i class="fa fa-tasks"></i> <?php echo $current_team_code;?> Tasks from <?php echo $rng_s_time;?> to <?php echo $rng_e_time;?></h1>
<div class="row">
    <div class="col-xs-12">
        <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'teamEventHourly', '?'=>array('team'=>$current_team_code,'hour'=>$prev_hr)));?>"><button class="btn btn-default" id="btnHrPrevious"><i class="fa fa-arrow-left"></i> <?php echo $prev_hr;?></button></a> &nbsp;
        <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'teamEventHourly', '?'=>array('team'=>$current_team_code,'hour'=>$next_hr)));?>"><button class="btn btn-primary" id="btnHrNext"><?php echo $next_hr;?> <i class="fa fa-arrow-right"></i></button></a> &nbsp;
    </div>
</div>

<?php if (!empty($tasks)){ ?>
<div class="tasks index">
    <div class="row">
        <div class="col-sm-1"></div>
        <div class="col-sm-11">
        <?php $m = 0; ?>
            <div class="row">
                <div class="col-xs-12"  style="margin-left:-0.5em">
                <?php
                    while ($m <= 55) {
                        //debug($m % 10);
                        if($m == 0){
                            echo '<span class="h4 bolder mml00">'.(date('g A', strtotime($rng_s_time))).'</span>';
                        }
                        elseif($m == 5){
                            echo '<span class="h4 bolder hidden-xs hidden-sm mml0'.$m.'">'.($rng_s_hr%12).':0'.$m.'</span>';
                        }
                        elseif($m % 10 == 5){
                            echo '<span class="h4 bolder hidden-xs hidden-sm mml'.$m.'">'.($rng_s_hr%12).':'.$m.'</span>';
                        }
                        elseif($m % 10 == 10){
                            echo '<span class="h4 bolder mml'.$m.'">'.($rng_s_hr%12).':'.$m.'</span>';
                        }
                        else{
                            //$m_txt = $m;
                            echo '<span class="h4 bolder mml'.$m.'">'.($rng_s_hr%12).':'.$m.'</span>';
                        }
                        $m = $m + 5;            
                    }
                ?>            
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <div class="row">
        <?php
            echo '</div></div>';
            $c = 0;
            $tstyles = array(
                'bg-charcoal',
                //'bg-charcoal',
                'bg-yh',
            );

    foreach($tasksByTeamAndRole as $team => $actors){
        //debug($tasksByTeamAndActor);
        foreach($actors['Unassigned'] as $k => $tasks){
            echo '<div class="panel panel-default"><div class="panel-body">';
            echo '<div class="row">';
                echo '<div class="col-sm-1"><h3><b><i class="fa fa-hashtag"></i>'.$k.'</b></h3></div>';
                echo '<div class="col-sm-11">';

                foreach($tasks as $task){
                    $pos = pctOffsets($task['Task']['start_time'], $task['Task']['end_time'], $time_range['start'], $time_range['end']);                
                    $date_txt = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i A', strtotime($task['Task']['start_time'])) : date('g:i A', strtotime($task['Task']['start_time'])).' - '.date('g:i A', strtotime($task['Task']['end_time'])); 
                    echo $date_txt.' : <b>('.$task['Task']['task_type'].')</b> '.$task['Task']['short_description'];
                        
                    if($k == $team){
                        echo '<div class="progress md-bot-marg">';
                            echo '<div class="mm05 tline-markers"></div><div class="mm10 tline-markers"></div><div class="mm15 tline-markers"></div><div class="mm20 tline-markers"></div><div class="mm25 tline-markers"></div><div class="mm30 tline-markers"></div><div class="mm35 tline-markers"></div><div class="mm40 tline-markers"></div><div class="mm45 tline-markers"></div><div class="mm50 tline-markers"></div><div class="mm55 tline-markers"></div>';
                            echo '<div class="progress-bar no-color" style="width: '.$pos['left_pct'].'%"></div>';
                            echo '<div class="progress-bar '.$tstyles[$c%2].'" style="width: '.$pos['length_pct'].'%">'.$date_txt.'</div>';
                            echo '<div class="progress-bar no-color" style="width: '.$pos['right_pct'].'%"></div>';
                        echo '</div>';
                    }else{
                        echo '<div class="progress md-bot-marg">';
                            echo '<div class="mm05 tline-markers"></div><div class="mm10 tline-markers"></div><div class="mm15 tline-markers"></div><div class="mm20 tline-markers"></div><div class="mm25 tline-markers"></div><div class="mm30 tline-markers"></div><div class="mm35 tline-markers"></div><div class="mm40 tline-markers"></div><div class="mm45 tline-markers"></div><div class="mm50 tline-markers"></div><div class="mm55 tline-markers"></div>';
                            echo '<div class="progress-bar no-color" style="width: '.$pos['left_pct'].'%"></div>';
                            echo '<div class="progress-bar '.$tstyles[$c%2].'" style="width: '.$pos['length_pct'].'%">'.$date_txt.'</div>';
                            echo '<div class="progress-bar no-color" style="width: '.$pos['right_pct'].'%"></div>';
                        echo '</div>';
                    }
                }
            echo '</div></div></div></div>';   
        }

        if(isset($actors['Assigned'])){
            ksort($actors['Assigned']);   
            $a = 0;
            $styles = array(
                'progress-bar-info',
                'progress-bar-warning',
                'progress-bar-success',
            );
            
            //debug($actors);
            foreach($actors['Assigned'] as $k => $tasks){
                echo '<div class="panel panel-default"><div class="panel-body bg-mdgrey">';
                
                echo '<div class="row">';
                echo '<div class="col-sm-1"><h3><b><i class="fa fa-id-badge"></i> '.$k.'</b></h3></div>';
                echo '<div class="col-sm-11">';
    
                foreach($tasks as $task){
                    $pos = pctOffsets($task['Task']['start_time'], $task['Task']['end_time'], $time_range['start'], $time_range['end']);                
                    $date_txt = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i A', strtotime($task['Task']['start_time'])) : date('g:i A', strtotime($task['Task']['start_time'])).' - '.date('g:i A', strtotime($task['Task']['end_time'])); 
                    echo $date_txt.' : <b>('.$task['Task']['task_type'].')</b> '.$task['Task']['short_description'];
                        
                    echo '<div class="progress md-bot-marg">';
                        echo '<div class="mm05 tline-markers"></div><div class="mm10 tline-markers"></div><div class="mm15 tline-markers"></div><div class="mm20 tline-markers"></div><div class="mm25 tline-markers"></div><div class="mm30 tline-markers"></div><div class="mm35 tline-markers"></div><div class="mm40 tline-markers"></div><div class="mm45 tline-markers"></div><div class="mm50 tline-markers"></div><div class="mm55 tline-markers"></div>';
                        echo '<div class="progress-bar bg-white" style="width: '.$pos['left_pct'].'%"></div>';
                        echo '<div class="progress-bar '.$styles[$a%3].'" style="width: '.$pos['length_pct'].'%">'.$date_txt.'</div>';
                        echo '<div class="progress-bar bg-white" style="width: '.$pos['right_pct'].'%"></div>';
                    echo '</div>';
                    }
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';   
                   
                $a++;
            }
        }
        $c++;
        
    }
?>        
        
<?php        

    echo '<br/>';
}
// No tasks
else { 
        ?>
        <div style = "margin-top: 20px; margin-bottom: 80px;" class="alert alert-danger" role="alert">
            <p><i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found! </b> No tasks matched your search parameters.  Please try modifying your Compile Options or search term.</p>
        </div>
    <?php 
}
?>
</div><!-- /.index -->
<?php echo $this->Js->writeBuffer(); ?>


