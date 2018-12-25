<?php
    //$userTeams = (AuthComponent::user('id'))? AuthComponent::user('id'): array();
    //debug($this->Session->read('Auth.User.Timeline.hour'));
    //debug($time_range);

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
    
     $actAss = $teamAss = $roles = $tasksByTeamAndRole = array();
     foreach ($tasks as $id => $t){
         $ass = $t['Assignment'];
         if(empty($ass)){
             $tasksByTeamAndRole[$tasks[$id]['Task']['team_code']]['Unassigned'][$tasks[$id]['Task']['team_code']][] = $tasks[$id];
         }
         else{
             foreach($ass as $k => $role){
                 $tasksByTeamAndRole[$tasks[$id]['Task']['team_code']]['Assigned'][$role['role_handle']][] = $tasks[$id];
                 $roles[] = $role['role_handle'];
             }
         }
     }
     //unset($tasks);
     ksort($tasksByTeamAndRole);
     
     //debug($tasksByTeamAndRole);
     //debug($tasksByTeamAndActor);
     
   //debug($time_range);
    $rng_s_hr = date('H', strtotime($time_range['start']));
    $rng_s_time = date('g:i A', strtotime($time_range['start']));
    $rng_e_time = date('g:i A', strtotime($time_range['end']));
    
    //debug($rng_s_time.' - '.$rng_e_time.' Hour: '.$hour);
    //debug($rng_e_time);
    $next_hr = date('Y-m-d H:i:s', strtotime($time_range['start'])+3600);
    $prev_hr = date('Y-m-d H:i:s', strtotime($time_range['start'])-3600);
    //debug($hour);
    $prev_hr_24 = ($hour >= 0)? $hour-1:0;
    $next_hr_24 = ($hour <=30)? $hour+1:30;
    
    $next_dt = date('g A', strtotime($time_range['start'])+60*60);
    $prev_dt = date('g A', strtotime($time_range['start'])-60*60);
    
?>

<h1><i class="fa fa-tasks"></i> Event Timeline from <?php echo $rng_s_time;?> to <?php echo $rng_e_time;?></h1>
<div class="row">
    <div class="col-xs-12">
    </div>
</div>

<div class="row">
    <div class="col-xs-6">
        <?php if($prev_hr_24>=0):?>
            <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'eventHourly', $prev_hr_24));?>"><button class="btn btn-default btn-block" id="btnHrPrevious"><i class="fa fa-arrow-left"></i> <?php echo $prev_dt; echo ($prev_hr_24 >=24)?' (<i class="fa fa-diamond"></i>+1)':' <i class="fa fa-diamond"></i>';?></button></a>
        <?php endif;?>
    </div>
    <div class="col-xs-6">
        <?php if($next_hr_24<31):?>
            <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'eventHourly', $next_hr_24));?>"><button class="btn btn-primary btn-block" id="btnHrNext"><?php echo $next_dt;echo ($next_hr_24 >= 24)? ' (<i class="fa fa-diamond"></i>+1)':' <i class="fa fa-diamond"></i>';?> <i class="fa fa-arrow-right"></i></button></a>
        <?php endif;?><br/>
    </div>
</div>

<?php if (!empty($tasks)){ ?>
<div class="tasks index">
    <div class="row">
        <div class="col-sm-2"></div>
        <div class="col-sm-10">
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

    foreach($tasksByTeamAndRole as $team => $group){
        //debug($tasksByTeamAndActor);
        if(isset($group['Unassigned'])){
            foreach($group['Unassigned'] as $k => $tasks){
                echo '<div class="panel panel-default"><div class="panel-body">';
                echo '<div class="row">';
                echo '<div class="col-sm-2"><h4><b><i class="fa fa-hashtag"></i>'.$k.'</b></h4></div>';
                echo '<div class="col-sm-10">';

                    foreach($tasks as $task){
                        //debug($task);
                        //debug(date('Y-m-d H:i:s',strtotime($task['Task']['start_time'])));
                        //debug(date('Y-m-d H:i:s',strtotime($task['Task']['end_time'])));
                        //debug((strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])));
                        $pos = pctOffsets($task['Task']['start_time'], $task['Task']['end_time'], $time_range['start'], $time_range['end']);                
                        $date_txt = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i A', strtotime($task['Task']['start_time'])) : date('g:i A', strtotime($task['Task']['start_time'])).' - '.date('g:i A', strtotime($task['Task']['end_time'])); 
                        echo $date_txt.' : <b>('.$task['Task']['task_type'].')</b> '.$task['Task']['short_description'];
                            
                        if($k == $team){
                            echo '<div class="progress md-bot-marg">';
                                echo '<div class="mm05 tline-markers"></div><div class="mm10 tline-markers"></div><div class="mm15 tline-markers tline-markers-q"></div><div class="mm20 tline-markers"></div><div class="mm25 tline-markers"></div><div class="mm30 tline-markers tline-markers-h"></div><div class="mm35 tline-markers"></div><div class="mm40 tline-markers"></div><div class="mm45 tline-markers tline-markers-q"></div><div class="mm50 tline-markers"></div><div class="mm55 tline-markers"></div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['left_pct'].'%"></div>';
                                echo '<div class="progress-bar '.$tstyles[$c%2].'" style="width: '.$pos['length_pct'].'%">'.$date_txt.'</div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['right_pct'].'%"></div>';
                            echo '</div>';
                        }else{
                            echo '<div class="progress md-bot-marg">';
                                echo '<div class="mm05 tline-markers"></div><div class="mm10 tline-markers"></div><div class="mm15 tline-markers tline-markers-q"></div><div class="mm20 tline-markers"></div><div class="mm25 tline-markers"></div><div class="mm30 tline-markers tline-markers-h"></div><div class="mm35 tline-markers"></div><div class="mm40 tline-markers"></div><div class="mm45 tline-markers tline-markers-q"></div><div class="mm50 tline-markers"></div><div class="mm55 tline-markers"></div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['left_pct'].'%"></div>';
                                echo '<div class="progress-bar '.$tstyles[$c%2].'" style="width: '.$pos['length_pct'].'%">'.$date_txt.'</div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['right_pct'].'%"></div>';
                            echo '</div>';
                        }
                    }
                echo '</div></div></div></div>';   
            }
            
        }

        if(isset($group['Assigned'])){
            ksort($group['Assigned']);   
            $a = 0;
            $styles = array(
                'progress-bar-info',
                'progress-bar-warning',
                'progress-bar-success',
            );
            
            //debug($actors);
            foreach($group['Assigned'] as $k => $tasks){
                echo '<div class="panel panel-default"><div class="panel-body bg-mdgrey">';
                
                echo '<div class="row">';
                echo '<div class="col-sm-2"><h4><b><i class="fa fa-id-badge"></i> '.$k.'</b></h4></div>';
                echo '<div class="col-sm-10">';
    
                foreach($tasks as $task){
                    $pos = pctOffsets($task['Task']['start_time'], $task['Task']['end_time'], $time_range['start'], $time_range['end']);                
                    $date_txt = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i A', strtotime($task['Task']['start_time'])) : date('g:i A', strtotime($task['Task']['start_time'])).' - '.date('g:i A', strtotime($task['Task']['end_time'])); 
                    echo $date_txt.' : <b>('.$task['Task']['task_type'].')</b> '.$task['Task']['short_description'];
                        
                    echo '<div class="progress md-bot-marg">';
                        echo '<div class="mm05 tline-markers"></div><div class="mm10 tline-markers"></div><div class="mm15 tline-markers tline-markers-q"></div><div class="mm20 tline-markers"></div><div class="mm25 tline-markers"></div><div class="mm30 tline-markers tline-markers-h"></div><div class="mm35 tline-markers"></div><div class="mm40 tline-markers"></div><div class="mm45 tline-markers tline-markers-q"></div><div class="mm50 tline-markers"></div><div class="mm55 tline-markers"></div>';
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


