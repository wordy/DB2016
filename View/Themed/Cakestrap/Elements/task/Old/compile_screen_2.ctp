<?php
    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    $show_comments = true; //$this->Session->read('Auth.User.Compile.show_comments');
    $single_task = (isset($single_task))? $single_task:0;
    $view_type = $this->Session->read('Auth.User.Compile.view_type');
    $user_shift = $this->Session->read('Auth.User.Timeshift');
    $user_controls = $this->Session->read('Auth.User.Teams');
    $sort = $this->Session->read('Auth.User.Compile.sort');
    $start_date = $this->Session->read('Auth.User.Compile.start_date');
    $end_date = $this->Session->read('Auth.User.Compile.end_date');
    $comp_teams = $this->Session->read('Auth.User.Compile.Teams');
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var));  

    //$this->Paginator->settings['paramType'] = 'querystring';
    $this->Paginator->options(array(
        'update' => '#taskListWrap',
        'evalScripts' => true,
        'before' => $this->Js->get('.csSpinner')->effect('fadeIn', array('buffer' => false)),
        //'before' => $this->Js->get('.pagination')->append('hi')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('.csSpinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'tasks', 'action' => 'compile')
    ));

$this->Js->buffer("

   $('.task-panel-heading').on('mouseenter', function(){
        //alert('over');
        $(this).append('<span class=\"addHere\"><button class=\"btn btn-info btn-sm \"><i class=\"fa fa-sort fa-lg\"></i> Add Task After</button></span>').fadeIn('fast');
                
    });
    
    $('.task-panel-heading').on('mouseleave', function(){
        $(this).find('span.addHere').remove();
    });
                
    // Prevents buttons & links from triggering the details slidedown
    $('.task-panel-heading').find('button, a').on('click', function(e){
        e.stopPropagation();
        e.preventDefault();
    });
    
");


    if($view_type == 2){
        $this->Js->buffer("
            var shtml = '<i class=\"fa fa-eye\"></i> Show Past Due';
            var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Past Due';                                   
            
            $('#hidePastDue').on('click', function(){
                if($(this).hasClass('pd_hidden')){
                    $('div.past_due').show();
                    $(this).removeClass('pd_hidden').addClass('pd_shown');
                    $(this).html(hhtml);    
                }
                else{
                    $('div.past_due').hide();
                    $(this).removeClass('pd_shown').addClass('pd_hidden');
                    $(this).html(shtml); 
                }
            });
        ");
    }    
    
    if($view_type == 5){
        $this->Js->buffer("
            $('div.acomp').hide();
    
            var shtml = '<i class=\"fa fa-eye\"></i> Show Completed';
            var hhtml = '<i class=\"fa fa-eye-slash\"></i> Hide Completed';                                   
            
            $('#aiHideComp').on('click', function(){
                if($(this).hasClass('ai_hidden')){
                    $('div.acomp').show();
                    $(this).removeClass('ai_hidden').addClass('ai_shown');
                    $(this).html(hhtml);    
                }
                else{
                    $('div.acomp').hide();
                    $(this).removeClass('ai_shown').addClass('ai_hidden');
                    $(this).html(shtml); 
                }
            });
            
            
            
  
        ");
        
        
        
    }

    // Variables for what's currently being shown
    $viewTeams = array();
    $viewMessage = '';
    $viewRange = '';
    $viewTeamsStr = '';
    $viewStartDate = date('M j', strtotime($start_date));
    $viewEndDate = date('M j', strtotime($end_date));
    $viewSort = (int)$sort;
    $viewSort = ($viewSort == 0) ? '<b>ascending start time</b>':'<b>descending start time</b>';    
            
    if(!empty($comp_teams)){
        foreach($comp_teams as $k=>$tid){
            $viewTeams[] = $teamIdCodeList[$tid];
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

    // Threaded
    if($single_task == 1){
        $viewMessage = '<b>Note:</b> You\'re currently viewing a single task.';
    }
    elseif($view_type == 0){
        $viewMessage = 'Viewing tasks for <b>'.$viewTeamStr.'</b> in a <b>threaded</b> view from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by '.$viewSort;    
    }
    // Rundown
    elseif($view_type == 1){
        $viewMessage = 'Viewing tasks for <b>'.$viewTeamStr.'</b> in a <b>rundown</b> view from <b>'.$viewStartDate.'</b> to <b>'.$viewEndDate.'</b> ordered by '.$viewSort;    
    }
    // Due Date
    elseif($view_type == 2){
        $viewMessage = '<b>Note:</b> You\'re viewing <b>tasks with due dates</b> involving <b>'.$viewTeamStr.'</b> from <b>ANY</b> date, ordered by <b>ascending due date</b>';
    }
    // Assisting
    elseif($view_type == 3){
        $viewMessage = '<b>Note:</b> You\'re viewing tasks where <b>'.$viewTeamStr.'</b> are <b>assisting</b> from <b>ANY</b> date, ordered by '.$viewSort.'</b>';
    }
    // Assisting
    elseif($view_type == 4){
        $viewMessage = '<b>Note:</b> You\'re viewing <b>tasks due within 2 weeks</b> where <b>'.$viewTeamStr.'</b> are <b>assisting</b>, ordered by '.$viewSort.'</b>';
    }
    // Action Items
    elseif($view_type ==  5){
        $viewMessage = '<b>Viewing Action Items.</b> Showing tasks from <b>ALL</b> teams from <b>ANY</b> date, ordered by <b>ascending start date</b>';
    }
    // Recently Created
    elseif($view_type ==  6){
        $viewMessage = '<b>Note:</b> You\'re viewing <b>recently created</b> tasks involving <b>'.$viewTeamStr.'</b> from <b>ANY</b> date, ordered by <b>descending created date</b> (i.e newest first)';
    }
}
      
    if (!empty($tasks)){ 
        //echo $single_task;
    ?>
        <div class="tasks index">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="alert alert-info" role="alert">
                        <?php 
                        if ($single_task == 1){ ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <?php echo $viewMessage; ?>    
                                </div>
    
                                <div class="col-md-3">
                                    <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right">
                                        <i class="fa fa-gears"></i> Back to Compiled Tasks                                   
                                    </a>
                                </div>
                            </div>
                    <?php                        
                        }
                        elseif ($view_type == 2){ ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <?php echo $viewMessage; ?>    
                                </div>
    
                                <div class="col-md-3">
                                    <button type="button" id="hidePastDue" class="btn btn-default">
                                        <i class="fa fa-eye-slash"></i> Hide Past Due                                   
                                    </button>
                                </div>
                            </div>
                    <?php                        
                        }
                        elseif ($view_type == 5){ ?>
                            <div class="row">
                                <div class="col-md-9">
                                    <?php echo $viewMessage; ?>    
                                </div>
    
                                <div class="col-md-3">
                                    
                                    <button type="button" id="aiHideComp" class="btn btn-default pull-right ai_hidden">
                                        <i class="fa fa-eye"></i> Show Completed                                   
                                    </button>
                                </div>
                            </div>
                    <?php                        
                        }
                        else {
                            echo $viewMessage;                
                        }
                    ?>
                    </div>
                </div>
            </div>
                    <div class="row">
            <div class="col-md-8 col-md-offset-2" id="cErrorStatus">
                <?php 
                    echo $this->Session->flash('compile');
                ?>
            </div>
        </div> 
            
    <?php if(!$single_task && $this->Paginator->param('current')):?>
        <div class="row">
            <div class=" col-xs-12" style="margin-bottom: -10px;">
                <div>
                    <ul class="pagination" style="margin-top:0px; margin-left: auto; margin-right:auto">
                    <?php
                        echo '<span class="csSpinner" style="display: none; margin-left: 5px;">';
                        echo $this->Html->image('ajax-loader_old.gif');
                        echo '</span>'; 
                        echo $this->Paginator->prev('< ' . __('Earlier'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                        echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                        echo $this->Paginator->next(__('Later') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                    ?>
                    </ul><!-- /.pagination -->
                </div>
                <div style="text-align:right; margin-bottom:-40px;">
                    <p>
                    <?php
                        echo $this->Paginator->counter(array(
                            'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                        ));
                    ?>
                    </p>
                </div>
            </div><!-- /.index -->
        </div>
        
        <script>
            var plabs = [];
            <?php foreach ($pgLabels as $pnum => $label){echo 'plabs['.$pnum.'] = "'.$label.'";';}?>
            
            console.log(plabs);
                $('.pagination>li>a, .pagination>li>span').each(function(){
                    
                    var curPn = $(this).text();
                    //console.log(curPn);
                    
                    //if(curPn == '2015-02-21'){
                    //    $(this).addClass('highlight');
                    //}
                    
                    if(plabs[curPn]){
                        $(this).append('<br/>'+plabs[curPn]);

                        var str = plabs[curPn];
                        var n = str.indexOf("diamond");
                        
                        if(plabs[curPn] == '<i class="fa fa-bullseye"></i> Today'){
                            $(this).addClass('badge-success');    
                        }
                        else if(n>0){
                            $(this).addClass('bg-yh');    
                        }
                    }
                    
                });
                
                
                $('.task-panel-heading').on('mouseEnter', function(){
                    alert('over');
                });
                $('.task-panel-heading').on('mouseLeave', function(){
                    alert('out');
                });


                
        </script>

        
        <style>
        
        .pagination > li.bg-yh > a:link {
            color:#fff !important;
        }
        

.pagination{
    table-layout: fixed;
    display: table;
}

.pagination>li{
        height: 60px;
display: table-cell;
    vertical-align: middle;    


}
 
        .pagination>li>a{

    

            text-align: center !important;
            vertical-align: middle !important;

        } 

        .pagination>li>a{
        } 


            
        </style>
            
<?php endif; 


//debug($this->params['paging']);

?>
           

    <?php
    // Hold days of tasks
    $cur_t_day = '';
    $prev_t_day = '';
    $last_t_day = '';
    $last_t_hr = '';
    $curr_t_day = '';            
    $curr_t_hr = '';
    $last_c_day = '';
    
    foreach ($tasks as $k => $task):
        
        // This hides tasks with <<no incoming links>> in a threaded view
        // These will be displayed under their parent task. For tasks that DO have 
        // incoming links, show their task line so THEIR child tasks can be displayed
        if(($view_type == 0) && !$single_task && !empty($task['Task']['parent_id']) && empty($task['Assist'])){
            //continue;
        }
        
        // Figure out task start date & hr & created date.  Used to group tasks by relevant headers
        $daysAreSame = false;
        $cDaysAreSame = false;
        $onEday = false;
        $hoursAreSame = false;
        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        $curr_t_hr = date('H', strtotime($task['Task']['start_time']));
        $curr_c_day = date('Y-m-d', strtotime($task['Task']['created']));
        $isPastDue = false;

        if($last_t_day == $curr_t_day){
            $daysAreSame = true;
        }
        if($last_c_day == $curr_c_day){
            $cDaysAreSame = true;
        }
        if($curr_t_day == $eday){
            $onEday = true;
        }
        if($curr_t_hr == $last_t_hr){
            $hoursAreSame = true;
        }
        
        $tid = $task['Task']['id']; 

        //Hide/show elements based on permissions.
        $userControls = false;
        if(in_array($task['Task']['team_id'], $user_controls)){ $userControls = true; }
        
        $inUsrShift = false;
        if(in_array($task['Task']['id'], $user_shift)){ $inUsrShift = true; }
        
        $hasComment = $commentCount = $hasDueDate = false; $hasDueSoon = false; $hasActionable = false; $hasChange = false; $hasNewChange = false;
            
        if(!empty($task['Task']['due_date'])){
            $dueString = strtotime($task['Task']['due_date']);
            $hasDueDate = true;
        
            // Highlights due 1 week from now
            if(($dueString >= $today_str) && ($dueString < $owfn)){
                 $hasDueSoon = true; 
            }
            // Highlights past due tasks
            if($dueString < $today_str) {
                $hasDueSoon = true;
                $isPastDue = true; 
            }
        }
    
        if(!empty($task['Task']['actionable_type'])){
             $hasActionable = true; 
        }
        
        if(!empty($task['Comment'])){
            $hasComment = true;
            $commentCount = count($task['Comment']); 
        }
        
    
        if (!empty($task['Change'])){
            $hasChange = true;
            $numChange = 0;
            
            // Count for recent changes    
            foreach ($task['Change'] as $chg){
                if (strtotime($chg['created'])  > $owa){
                    $hasNewChange = true;
                    $numChange++;
                }
            }  
        }
    ?>
    
    <div class="row <?php echo ($task['Task']['actionable_type_id'] > 300)? 'acomp':'';?> <?php echo ($isPastDue)? 'past_due':'';?>">
    <div class="col-xs-12">
    <?php
        // For recently created, use "created date" as basis for grouping tasks for display
        if($view_type == 6 && !$cDaysAreSame){
            echo '<h4 class="great">';
            
            if($curr_c_day != $today){
                echo $this->Time->timeAgoInWords($curr_c_day, array(
                    'accuracy' => array('day' => 'day'),
                    'end' => '1 week',
                    'format' => 'M d'
                ));    
            }
            else{
                echo 'Today';
            }
                
            echo '</h4>';
            
        }
        // Otherwise, use task start date/time
        elseif(!$daysAreSame && $view_type != 6){
            echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
        }
        elseif($onEday && !$hoursAreSame && $view_type != 6){
            echo '<h4 class="eday">'.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
        elseif($onEday && !$hoursAreSame && !$daysAreSame && $view_type != 6){
            echo '<h4 class="great">'.date('M j g A', strtotime($task['Task']['start_time'])).'</h4>';
        }
    ?>
    </div></div>
    <div class="row">
    <div class="col-md-12">
        <div 
            data-taskid="<?php echo ($task['Task']['id']); ?>" 
            id="tid<?php echo ($task['Task']['id']); ?>" 
            class="panel panel-default task-panel" 
            style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
         >
            <div class="panel-heading task-panel-heading"
                data-tid="<?php echo ($task['Task']['id']); ?>">
                <div class="row sm-bot-marg">
                    <div class="col-xs-2 col-sm-2 col-md-2">
                        <div class="taskTs checkbox facheckbox facheckbox-circle facheckbox-success">
                            <input type="checkbox"
                                class="tsCheck <?php if($inUsrShift){echo 'checked';} ?>" 
                                id="hide<?php echo $tid;?>"
                                <?php if(!$userControls){echo 'disabled="disabled"';} ?>
                                <?php if($inUsrShift){echo 'checked="checked"';} ?> 
                                data-stime="<?php echo strtotime($task['Task']['start_time']); ?>" 
                                data-etime="<?php echo strtotime($task['Task']['end_time']); ?>" 
                                data-tid="<?php echo $task['Task']['id']?>" 
                            />
                            
                            <label class="taskTimeshift" for="hide<?php echo $tid;?>">
                                <?php
                                    if($view_type != 6){
                                        $t1 = date('Y-m-d H:i:s', strtotime($task['Task']['start_time']));
                                        $t2 = date('Y-m-d H:i:s', strtotime($task['Task']['end_time']));
                                        $d1 = date('Y-m-d', strtotime($task['Task']['start_time']));
                                        $d2 = date('Y-m-d', strtotime($task['Task']['end_time']));
                                        
                                        $diff = (strtotime($task['Task']['end_time']) - strtotime($task['Task']['start_time']));
                                        $dh = floor($diff / 3600);
                                        $dm = floor(($diff / 60) % 60);
                                        $ds = $diff % 60;
                                    
                                        if($diff == 0){
                                            echo date('g:i A', strtotime($t1));
                                        }
                                        // Important seconds
                                        elseif($diff < 60){
                                            echo $this->Time->format('g:i:s', $task['Task']['start_time']);
                                            echo ' - ';
                                            echo $this->Time->format('g:i:s A', $task['Task']['end_time']);
                                            echo '<br/>('.$diff.'s)';
                                        }
                                        // < hr
                                        elseif(($diff >= 60) && ($diff < 3600)){
                                            echo $this->Time->format('g:i', $task['Task']['start_time']);
                                            echo ' - ';
                                            echo $this->Time->format('g:i A', $task['Task']['end_time']);
                                            echo '<br/>('.$dm.' min)';
                                        }
                                        // > 1h < 24h
                                        elseif($diff >= 3600 && $diff < 86400){
                                            echo $this->Time->format('g:i A', $task['Task']['start_time']);
                                            echo ' - ';
                                            echo $this->Time->format('g:i A', $task['Task']['end_time']);
                                            echo '<br/>('.$dh.' hr, '.$dm.' min)'; 
                                        }
                                        // >1 day or spans days
                                        elseif ($diff >= 86400){
                                            echo date('g:i A', strtotime($t1));
                                        }
                                        
                                        if($d1 != $d2){
                                            echo '<br/>(Multi-day)';
                                        }
                                    }
                                    else {
                                        echo date('M j g:i A', strtotime($task['Task']['start_time']));
                                    }
                                ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-3 col-sm-3 col-md-2">
                        <?php
                            echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            echo $this->Ops->makeTeamsSig($task['Task']['TasksTeam'], $zoneTeamCodeList, $userControls);
                        ?> 
                    </div>
                    <div class="col-xs-5 col-sm-5 col-md-7">

                        <?php
                            echo $task['Task']['short_description'].'<br/>';

                            if ($show_details && !empty($task['Task']['details'])){
                                echo '<hr align="left" style="width: 100%; margin-bottom:3px; margin-top:3px; border-top: 1px solid #aaa;"/>';
                                echo nl2br($task['Task']['details']);
                            }
                        ?>
                    </div>
                    <div class="col-xs-2 col-sm-2 col-md-1">
                        <div class="pull-right task-buttons" style="text-align: right; margin-left: 8px;">
                            <?php 
                                if($hasDueDate){
                                    if($hasDueSoon){
                                        echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                    }
                                    else {
                                        echo '<button type="button" class="btn btn-warning btn-xs xxs-bot-marg">';
                                    }
                                    echo '<i class="fa fa-clock-o"></i>&nbsp;';
                                    echo $this->Time->format('M d', $task['Task']['due_date']);
                                    echo '</button><br/>';
                                }

                                if($hasActionable){
                                    echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-flag fa-lg"></i>&nbsp';
                                    echo $task['Task']['actionable_type'];
                                    echo '</button><br/>';
                                    
                                    /*echo('<div class="input-group-btn">
     <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
       Foo
       <span class="caret"></span>
     </button>
     <ul class="dropdown-menu pull-left">
       <li><a href="#" data-value="bar">Bar</a></li>
       <li><a href="#" data-value="baz">Baz</a></li>
       <li><a href="#" data-value="beh">Beh</a></li>
     </ul>
   </div>');
                    */?>
                    
    <div class="btn-group">
        <button type="button" class="btn btn-xs btn-danger dropdown-toggle xxs-bot-marg actTypeDD" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-flag"></i>&nbsp; None <span class="caret"></span>
        </button>
    
        <ul class="dropdown-menu atid_dropdown">
            <li><a href="#">&nbsp; None</a></li>
        <?php 
            $curAtId = (isset($task['Task']['actionable_type_id']))? $task['Task']['actionable_type_id']:null;
            foreach ($actionableTypes as $atid => $atlab){
                echo '<li value="';
                echo $atid.'" ';
                echo ($atid == $curAtId)?'class="active" selected':'';
                echo '><a href="#">&nbsp;'.$atlab.'</a></li>';
            }
        ?>
        </ul>
    </div>
                    
                    
                    <?php                
                                    
                                    
                                    
                                    
                                    
                                }
                                
                                if($hasComment){
                                    echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-comment-o"></i>&nbsp;';
                                    echo $commentCount.' New';
                                    echo '</button><br/>';    
                                }
                                                
                                if($hasChange && $hasNewChange){
                                    echo '<button type="button" class="btn btn-success btn-xs xxs-bot-marg">';
                                    echo '<i class="fa fa-exchange"></i>&nbsp;';
                                    echo $numChange.' New';
                                    echo '</button><br/>';    
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php 
                if (!empty($task['Parent']['id']) && ($single_task || in_array($view_type, array(0)))): ?>
                                
                <div class="row xs-bot-marg">
                    <div class="col-xs-2">
                        <div class="text-align-right">
                            <?php 
                            if($task['Parent']['id'] && ($task['Task']['time_control']==1)){
                                $off = ($task['Task']['time_offset'] <>0)? '('.$task['Task']['time_offset'].' min)': '';
                                $relType ='<i class="fa fa-clock-o"></i> <b>Time Synced To<br>'.$off.'</b>' ; 
                            }
                            else{
                                $relType ='<i class="fa fa-level-up"></i> <b>Linked To</b>';
                            }
                            ?>
                            <h5><?php echo $relType;?></h5>
                        </div>
                    </div>
                    <div class="col-xs-10">
                        <?php 
                            echo $this->Ops->subtaskRowSingle($task['Task']['Parent']);
                        ?>
                    </div>
                </div>
                <?php endif; 
                
                if (!empty($task['Task']['Assist']) && ($single_task || in_array($view_type, array(0)))):
                    $tcs = Hash::extract($task['Task']['Assist'],'{n}.time_control');

                    // At least one team is controlled
                    if(in_array(1, $tcs)): ?>
                        <div class="row xs-bot-marg">
                            <div class="col-xs-2">
                                <div class="text-align-right">
                                    <h5><i class="fa fa-clock-o"></i> <b>Time Controls</b></h5>
                                </div>
                            </div>
                            <div class="col-xs-10">
                            <?php 
                                foreach($task['Task']['Assist'] as $as){
                                    if(!$as['time_control']){
                                        continue;
                                    }
                                    echo $this->Ops->subtaskRowSingle($as);
                                }
                            ?>
                            </div>
                        </div>
                <?php 
                    endif;
                    
                    if(in_array(0, $tcs)): ?>
                        <div class="row xs-bot-marg">
                            <div class="col-xs-2">
                                <div class="text-align-right">
                                    <h5><i class="fa fa-group"></i> <b>Linked Teams</b></h5>
                                </div>
                            </div>
                            <div class="col-xs-10">
                            <?php 
                                foreach($task['Task']['Assist'] as $as){
                                if($as['time_control']==1){
                                        continue;
                                    }


                                    echo $this->Ops->subtaskRowSingle($as);
                                }
                            ?>
                            </div>
                        </div>
                <?php 
                    endif;
                    
                    
                    
                    
                    
                endif; 
                ?>
            </div>
  
            <div class="panel-body taskPanelBody" id="task_detail_<?php echo $task['Task']['id'];?>"" style="display:none;"></div>    
            </div>
        </div>
    </div>
    <?php
        $last_t_day = $curr_t_day;
        $last_t_hr = $curr_t_hr;
        
        $last_c_day = $curr_c_day;
        endforeach; 
    echo '<br/>';
    if($this->Paginator->param('current')):
    ?>

    <p><small>
        <?php
            echo $this->Paginator->counter(array(
                'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
            ));
        ?>
    </small></p>
    <ul class="pagination">
        <?php
            echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
            echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
            echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
            echo '<span class="csSpinner" style="display: none; margin-left: 5px; vertical-align: middle;">';
            echo $this->Html->image('ajax-loader_old.gif');
            echo '</span>'; 
        ?>
    </ul><!-- /.pagination -->
        <div id="pageNum" style="visibility:hidden;"><?php echo $this->Paginator->param('page');?></div>
    </div><!-- /.index -->

    
            
<?php
endif;
    }
    // No tasks
    else { 
        
        if($single_task == 1){?>
            <div class="alert alert-danger" role="alert">
                <div class="row">
                    <div class="col-md-8">
                <i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>Task Not Found! </b> The task you requested was not found. It may have been deleted. Please verify the task ID.        
                    </div>
                    <div class="col-md-4">
                <a href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'compile'))?>" class="btn btn-default ai_hidden pull-right">
                                        <i class="fa fa-gears"></i> Back to Compiled Tasks                                   
                                    </a>        
                    </div>
                </div>
                
                
            </div>

        
        <?php 
        }
        else{
            ?>
            <div class="alert alert-danger" role="alert">
                <i class="fa fa-lg fa-exclamation-circle"></i>&nbsp; <b>No Tasks Found! </b> No tasks matched your search parameters.  Please try refining your search terms.
            </div>
        <?php 
        } 
    }
    echo $this->Js->writeBuffer();
?>

<!-- Comment styling from http://bootsnipp.com/snippets/featured/user-comment-example -->
