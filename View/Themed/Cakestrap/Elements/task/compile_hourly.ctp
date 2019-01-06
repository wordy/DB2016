<?php
    if (AuthComponent::user('id')){
        $userTeams = AuthComponent::user('Teams');
        
        //debug(AuthComponent::user('Timeline'));
        //debug(AuthComponent::user('Compile'));
    }
    $event_date_old = date('Y-m-d', strtotime('2018-02-10'));
    
    $this->Js->buffer("
        
    
    ");
    
    //debug(date('gA',strtotime('7:00:00')));
    //debug($timeline_hr);
    
    // Defaults to 12am event day
    
    $ishr = 0; 
    
    if(isset($timeline_hr)){
        $this->Js->buffer("var timeline_hr =".$timeline_hr);
        $this->Js->buffer("
        
        //console.log(timeline_hr);
        var timeline_moment = moment(DB_EVENT_DATE).add(timeline_hr*60*60,'s').format('YYYY-MM-DD HH:mm');
        //console.log(timeline_moment);
        
        
        
        ");
        
        //$ishr = $ishr%24;
        $ishr = $timeline_hr;    
    } 
    $iehr = $ishr + 1;
    
    //debug($ishr);
    $rng_start = date('Y-m-d H:i:s',strtotime($event_date_old)+$ishr*60*60);
    $rng_end = date('Y-m-d H:i:s', strtotime($event_date_old)+($ishr*60*60)+(59*60)+59);
    
    //debug($rng_start.'  '.$rng_end);
    
    $time_range = array('start'=>$rng_start, 'end'=>$rng_end);
   
    $rng_s_hr = date('H', strtotime($time_range['start']));
    $rng_s_time = date('g:i A', strtotime($time_range['start']));
    $rng_e_time = date('g:i A', strtotime($time_range['end']));
    $next_hr = date('g A', strtotime($time_range['start'])+3600);
    $cur_hr = date('g A', strtotime($time_range['start']));
    $prev_hr = date('g A', strtotime($time_range['start'])-3600);
          
    $teamAss = $roles = $tasksByTeamAndRole = array();
    
    //Sort tasks unassigned vs. assigned
     foreach ($tasks as $id => $t){
         $ass = $t['Assignment'];
         if(empty($ass)){
            $tasksByTeamAndRole[$tasks[$id]['Task']['team_code']]['Unassigned'][$tasks[$id]['Task']['team_code']][] = $tasks[$id];    
         }
         else{
             foreach($ass as $k => $role){
                 $tasksByTeamAndRole[$tasks[$id]['Task']['team_code']]['Assigned'][$role['role_handle']][] = $tasks[$id];
                 //$atrs[] = $role['role_handle'];
             }
         }
     }
     //unset($tasks);
     ksort($tasksByTeamAndRole);
     
     
     //debug($tasksByTeamAndRole);
     
     //debug(Hash::extract($tasks,'{n}.Task.id'));
     
     //Using task start/end & start/end of range, figure out % of bar that should be filled, and where.
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
    
    //Defaults for this view
    $single_task = 0;
    $search_term = null;
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));
    
    $this->Js->buffer("
        
        $('.tl-mark').data('tlhr', timeline_hr);  //save for handlers elsewhere
    
        $('#btnHrPrevious, #btnHrPrevious2').on('click', function(){
            $.ajax({
                url: '/tasks/compile?view=2&hr=".($ishr-1)."',
                type: 'post',
                success: function(data, textStatus){
                    if(textStatus == 'success'){
                        $('#taskListWrap').html(data);
                    }
                }
            });
            return false;
        });


        $('#btnHrNext,#btnHrNext2').on('click', function(){
            $.ajax({
                url: '/tasks/compile?view=2&hr=".($ishr + 1)."',
                type: 'post',
                success: function(data, textStatus){
                    if(textStatus == 'success'){
                        $('#taskListWrap').html(data);
                    }
                }
            });
            return false;        
        });

        $('#inputSelectHr,#inputSelectHr2').on('change', function(){
            //console.log('got change from input select hr in com_hrly.ctp');
            var val = $(this).val();
            $.ajax({
                url: '/tasks/compile?view=2&hr='+ val +'',
                type: 'post',
                success: function(data, textStatus){
                    if(textStatus == 'success'){
                        $('#taskListWrap').html(data);
                    }
                }
            });
            return false;
        });
        
        
                
    ");  
?>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger"><span class="lead"><b>**DEMO MODE**</b> Currently showing DragonBall 2018's Event Day Timeline. This will change to DB2019 shortly.</span></div>
    </div>
</div>

<p class="lead">Navigate forward and backwards in time, from 6am event day to 6am the day after.</p>
<div class="well">

    <div class="row">
        <div class="col-xs-5">
            <button class="btn btn-default btn-block <?php echo ($ishr-1 < 6)?'hidden':'';?>" id="btnHrPrevious"><i class="fa fa-arrow-left"></i> <?php echo $prev_hr; echo ($ishr-1<24)? ' Event Day <i class="fa fa-diamond"></i>':' Day After Event';?></button>
        </div>
    
        <div class="col-xs-2">
            <!--<button class="btn btn-danger btn-block" id="btnCurHr"><i class="fa fa-star"></i> <?php echo $cur_hr;?> </button>-->    
                <?php echo $this->Form->input('hours', array(
                        'empty'=>true,
                        'label'=>false,                         'id'=>'inputSelectHr',
                        'type'=>'select',
                        'empty'=>false,
                        'selected'=>$ishr,
                        'options'=>array(
                            'Event Day'=>array(//0=>'12 AM',1=>'1 AM', 2=>'2 AM', 3=>'3 AM', 4=>'4 AM', 5=>'5 AM', 
                                6=>'6 AM', 7=>'7 AM', 8=>'8 AM', 9=>'9 AM', 10=>'10 AM', 11=>'11 AM', 12=>'12 PM', 13=>'1 PM', 14=>'2 PM', 15=>'3 PM', 16=>'4 PM', 17=>'5 PM', 18=>'6 PM', 19=>'7 PM', 20=>'8 PM',  21=>'9 PM', 22=>'10 PM', 23=>'11 PM'),
                            'Day After'=>array(24=>'12 AM', 25=>'1 AM', 26=>'2 AM', 27=>'3 AM', 28=>'4 AM', 29=>'5 AM', 30=>'6 AM')
                        ),
                        'placeholder'=>'Hour',
                        
                        'class'=>'form-control input-date-notime')); 
                ?>
        </div>
    
        <div class="col-xs-5">
            <?php if($ishr+1 <> 31):?>
                <button class="btn btn-success btn-block" id="btnHrNext"><?php echo $next_hr; echo ($ishr+1<24)? ' Event Day <i class="fa fa-diamond"></i>':' Day After Event';?> <i class="fa fa-arrow-right"></i></button>
            <?php endif;?>
        </div>
    </div>        
</div>
<?php //debug('tasks : ');debug($tasks);

 
if (!empty($tasks)){ ?> 

<div class="tasks index">
    <?php
    $user_controls = $this->Session->read('Auth.User.Teams');
    
    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = $viewRange = $viewTeamsStr = '';
            
    if(!empty($comp_teams)){
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
    

?>

<?php 
//debug($ishr);
//debug($iehr);
    

?>

                                    

<?php if (!empty($tasks)){ ?>
<div class="tasks index">
    <div class="row">

        <div class="col-lg-12">
        <?php $m = 0; ?>
            <div class="row">
                <div class="col-xs-12"  style="margin-left:-0.61em">
                <?php
                    while ($m <= 60) {
                        //debug($m % 10);
                        if($m == 0){
                            echo '<span class="h4 bolder mml00">'.(date('gA', strtotime($rng_s_time))).'</span>';
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
                        elseif($m == 60){
                            $stime = date('gA', strtotime($time_range['start'])+60*60);
                            //debug($time_range['start']);
                            //debug($stime);
                            
                            echo '<span class="h4 bolder mml60">'.$stime.'</span>';
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

    foreach($tasksByTeamAndRole as $team => $roles){
        
        
        if(!empty($roles['Unassigned'])){
            foreach($roles['Unassigned'] as $k => $tasks){
                echo '<div class="panel panel-default"><div class="panel-body" style="padding: 10px 10px">';
                echo '<div class="row"><div class="col-lg-12"><span class="h3"><b><i class="fa fa-hashtag"></i>'.$k.'</b></span></div></div>';
                echo '<div class="row">';
                    echo '<div class="col-lg-12">';
    
                    foreach($tasks as $task){
                        $pos = pctOffsets($task['Task']['start_time'], $task['Task']['end_time'], $time_range['start'], $time_range['end']);                
                        $date_txt = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i A', strtotime($task['Task']['start_time'])) : date('g:i A', strtotime($task['Task']['start_time'])).' - '.date('g:i A', strtotime($task['Task']['end_time'])); 
                        $date_txt_a = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i', strtotime($task['Task']['start_time'])) : date('g:i', strtotime($task['Task']['start_time'])).' - '.date('g:i', strtotime($task['Task']['end_time']));
                        echo $this->Html->link('<span class="h5">'.$date_txt.' : <b>('.$task['Task']['task_type'].')</b> '.$task['Task']['short_description'].'</span>', array('action'=>'compile','?'=>array('task'=>$task['Task']['id'])),array('escape'=>false,'target'=>'_blank'));
                            
                        if($k == $team){
                            echo '<div class="progress md-bot-marg">';
                                echo '<div data-min="05" class="mm05 tl-mark"></div><div data-min="10" class="mm10 tl-mark"></div><div data-min="15" class="mm15 tl-mark tline-markers-q"></div><div data-min="20" class="mm20 tl-mark"></div><div data-min="25" class="mm25 tl-mark"></div><div data-min="30" class="mm30 tl-mark tline-markers-h"></div><div data-min="35" class="mm35 tl-mark"></div><div data-min="40" class="mm40 tl-mark"></div><div data-min="45" class="mm45 tl-mark tline-markers-q"></div><div data-min="50" class="mm50 tl-mark"></div><div data-min="55" class="mm55 tl-mark"></div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['left_pct'].'%"></div>';
                                echo '<div class="progress-bar '.$tstyles[$c%2].'" style="width: '.$pos['length_pct'].'%">'.$date_txt_a.'</div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['right_pct'].'%"></div>';
                            echo '</div>';
                        }else{
                            echo '<div class="progress md-bot-marg">';
                                echo '<div class="mm05 tl-mark"></div><div class="mm10 tl-mark"></div><div class="mm15 tl-mark tline-markers-q"></div><div class="mm20 tl-mark"></div><div class="mm25 tl-mark"></div><div class="mm30 tl-mark tline-markers-h"></div><div class="mm35 tl-mark"></div><div class="mm40 tl-mark"></div><div class="mm45 tl-mark tline-markers-q"></div><div class="mm50 tl-mark"></div><div class="mm55 tl-mark"></div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['left_pct'].'%"></div>';
                                echo '<div class="progress-bar '.$tstyles[$c%2].'" style="width: '.$pos['length_pct'].'%">'.$date_txt_a.'</div>';
                                echo '<div class="progress-bar no-color" style="width: '.$pos['right_pct'].'%"></div>';
                            echo '</div>';
                        }
                    }
                echo '</div></div></div></div>';   
            }            
        }

        $BOOL_all_assigned = false;
        
        if(isset($roles['Assigned'])){
            ksort($roles['Assigned']);   
            $a = 0;
            $styles = array(
                'progress-bar-info',
                'progress-bar-warning',
                'progress-bar-success',
                'progress-bar-danger',
                'progress-bar-info progress-bar-striped',
                'progress-bar-warning progress-bar-striped',
                'progress-bar-success progress-bar-striped',
                'progress-bar-danger progress-bar-striped',
            );
            
            //debug($roles);
            foreach($roles['Assigned'] as $k => $tasks){
                echo '<div class="panel panel-default sm-bot-marg"><div class="panel-body role_subtask"  style="padding: 10px 10px">';
                echo '<div class="row"><div class="col-lg-12">';
                echo '<span class="h3"><b><i class="fa fa-hashtag"></i>'.$team.' <i class="fa fa-caret-right"></i> </b><b><i class="fa fa-id-badge"></i> '.$k.'</b></span></div></div>';

                echo '<div class="row">';
                echo '<div class="col-lg-12">';
    
                foreach($tasks as $task){
                    $pos = pctOffsets($task['Task']['start_time'], $task['Task']['end_time'], $time_range['start'], $time_range['end']);                
                    $date_txt = (strtotime($task['Task']['end_time'])-strtotime($task['Task']['start_time'])<=60) ? date('g:i', strtotime($task['Task']['start_time'])) : date('g:i', strtotime($task['Task']['start_time'])).' - '.date('g:i', strtotime($task['Task']['end_time'])); 
                    echo $this->Html->link($date_txt.' : <b>('.$task['Task']['task_type'].')</b> '.$task['Task']['short_description'], array('action'=>'compile','?'=>array('task'=>$task['Task']['id'])),array('escape'=>false,'target'=>'_blank'));
                        
                    echo '<div class="progress md-bot-marg">';
                        echo '<div class="mm05 tl-mark"></div><div class="mm10 tl-mark"></div><div class="mm15 tl-mark tline-markers-q"></div><div class="mm20 tl-mark"></div><div class="mm25 tl-mark"></div><div class="mm30 tl-mark tline-markers-h"></div><div class="mm35 tl-mark"></div><div class="mm40 tl-mark"></div><div class="mm45 tl-mark tline-markers-q"></div><div class="mm50 tl-mark"></div><div class="mm55 tl-mark"></div>';
                        echo '<div class="progress-bar bg-white" style="width: '.$pos['left_pct'].'%"></div>';
                        echo '<div class="progress-bar '.$styles[$a%8].'" style="width: '.$pos['length_pct'].'%">'.$date_txt.'</div>';
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

    echo '<br/>';?>
    
    <div class="well">
        <div class="row">
            <div class="col-xs-5">
                <button class="btn btn-default btn-block <?php echo ($ishr-1 < 6)?'hidden':'';?>" id="btnHrPrevious2"><i class="fa fa-arrow-left"></i> <?php echo $prev_hr; echo ($ishr-1<24)? ' Event Day <i class="fa fa-diamond"></i>':' Day After Event';?></button>
            </div>

            <div class="col-xs-2">
                <?php echo $this->Form->input('hours', array(
                    'empty'=>true,
                    'label'=>false, 
                    'id'=>'inputSelectHr2',
                    'type'=>'select',
                    'empty'=>false,
                    'selected'=>$ishr,
                    'options'=>array(
                        'Event Day'=>array(//0=>'12 AM',1=>'1 AM', 2=>'2 AM', 3=>'3 AM', 4=>'4 AM', 5=>'5 AM', 
                            6=>'6 AM', 7=>'7 AM', 8=>'8 AM', 9=>'9 AM', 10=>'10 AM', 11=>'11 AM', 12=>'12 PM', 13=>'1 PM', 14=>'2 PM', 15=>'3 PM', 16=>'4 PM', 17=>'5 PM', 18=>'6 PM', 19=>'7 PM', 20=>'8 PM',  21=>'9 PM', 22=>'10 PM', 23=>'11 PM'),
                        'Day After'=>array(24=>'12 AM', 25=>'1 AM', 26=>'2 AM', 27=>'3 AM', 28=>'4 AM', 29=>'5 AM', 30=>'6 AM')
                    ),
                    'placeholder'=>'Hour',
                    'class'=>'form-control input-date-notime')); 
                ?>
            </div>

            <div class="col-xs-5">
                <?php if($ishr+1 <> 31):?>
                    <button class="btn btn-success btn-block" id="btnHrNext2"><?php echo $next_hr; echo ($ishr+1<24)? ' Event Day <i class="fa fa-diamond"></i>':' Day After Event';?> <i class="fa fa-arrow-right"></i></button>
                <?php endif;?>
            </div>
        </div>
    </div>  
    <?php
    
}
}
// No tasks
else { 
        ?>
        <div style = "margin-top: 20px; margin-bottom: 80px;" class="alert alert-warning" role="alert">
            <p><i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found!</b> No tasks matched your search parameters.</p>
        </div>
    <?php 
}
?>

</div><!-- /.index -->


<?php echo $this->Js->writeBuffer(); ?>