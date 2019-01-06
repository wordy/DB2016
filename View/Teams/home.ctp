<?php 

    $this->set('title_for_layout', 'Team Dashboard');
    
    $this->Js->buffer("
        $('#dashChangeTeam').on('change', function(e){
            var val = $(this).find('option:selected').text();
            window.location = '".$this->Html->url(array('controller'=>'teams', 'action'=>'home'))."/'+val;    
        });
    
        $('.helpTTs').popover({
            container: 'body',
            html:true,
        });

        $('#duesoon_task_link').on('click', function(){
            $('html, body').animate({
                scrollTop: $('#due_soon').offset().top-30}, 800);
                return false;
        });
        
                
        $('#open_task_link').on('click', function(){
            $('html, body').animate({
                scrollTop: $('#open_req').offset().top-50}, 800);
                return false;
        });

        $('#waiting_task_link').on('click', function(){
            $('html, body').animate({
                scrollTop: $('#waiting_req').offset().top-70}, 
                800
            );
            return false;
        });
                
        $('#dashChangeTeam').select2({
            minimumResultsForSearch: Infinity,
            theme: 'bootstrap',
            placeholder: 'Select a team',
            
            
        });
        
    ");
    
    $iOpenReq = $iWaitingReq = 0;
    if(!empty($open_tasks)){
        $iOpenReq = count($open_tasks);
    }

    if(!empty($waiting_tasks)){
        $iWaitingReq = count($waiting_tasks);
    }

    
?>


<h1 id="pageTitle"><i class="fa fa-home"></i> <?php echo isset($team_code) ? $team_code. ' ':''; ?>Team Home</h1>
    <p>This is a summary of important tasks for your team. Hint: Use <button class="btn btn-xs btn-default"><i class="fa fa-eye"></i> View</button> buttons to work with tasks in separate tabs.</p>
         <?php 
            if(empty($team_id)){
                echo '<div class="alert alert-info">';
                echo $this->Form->label('team_id', 'Change Team');
                ?> 
                <a class="helpTTs" tabindex="0" role="button" data-toggle="popover" data-trigger="focus" title="" data-content="<p>By default, you automatically view your own team's dashboard. Choose another team here to view <b>their</b> dashboard.</p><p>You can use this to verify that other teams are seeing your requests, for example.</p>" data-original-title="Choose Team Dashboard">
                    <i class="fa fa-question-circle text-info"></i>                    
                </a>
                <?php echo $this->Form->input('team_id', array(
                    'options'=>$zoneNameTeamCodeList,
                    'selected'=>(isset($team_id))? $team_id : false,
                    'multiple'=>false,
                    'empty'=>true,
                    'label'=>false, 
                    'id'=>'dashChangeTeam', 
                    'div'=>array(
                        'class'=>'input-group'),
                    'after'=>'<span class="input-group-addon"><i class="fa fa-users"></i></span>',
                    'class' => 'form-control inputLeadTeam')); 
                
                echo '</div>';
            }
            else{
    

        ?>
         
                
<div class="row">
    <div class="col-md-8 col-sm-12">
        <ul class="nav nav-pills nav-stacked">
            <li><a id="duesoon_task_link" href="#"><h4><i class="fa fa-bell-o fa-fw"></i>&nbsp; <?php echo ($team_code)?:'';?> Upcoming</span></h4>
                    Upcoming tasks, or those ending or due within the next 2 weeks.
                </a>
            </li>

            <li><a id="open_task_link" href="#"><h4><i class="fa fa-life-saver fa-fw"></i>&nbsp; Requests to <?php echo ($team_code)?:'';?> (Owing) <span class="badge <?php echo ($iOpenReq == 0)? 'badge-yh ': 'badge-danger ';?> pull-right">&nbsp;&nbsp;<?php echo $iOpenReq;?>&nbsp;&nbsp;</span></h4>
                    Tasks where other teams asked for your help and you haven't responded.
                </a>
            </li>
            <li><a id="waiting_task_link" href="#"><h4><i class="fa fa-hourglass-half fa-fw"></i>&nbsp; Requests from <?php echo ($team_code)?:'';?> (Waiting) <span class="badge <?php echo ($iWaitingReq == 0)? 'badge-yh': 'badge-danger';?> pull-right">&nbsp;&nbsp;<?php echo $iWaitingReq;?>&nbsp;&nbsp;</span></h4>
                    Tasks where you asked for help from other teams and you're waiting on a response.
                </a>
            </li>
            <li><a id="team_digest" href="<?php echo $this->Html->url(array('controller'=>'tasks', 'action'=>'digest', $team_id))?>">
                    <h4><i class="fa fa-newspaper-o fa-fw"></i>&nbsp;&nbsp; Preview <?php echo ($team_code)?:'';?> Digest</h4>                    View the contents of the weekly <?php echo ($team_code)?:'';?> email update.
                </a>
            </li>
        </ul>
    </div>

    <div class="col-md-4 col-sm-12">
       <div class="alert alert-info xs-bot-marg">
            <i class="fa fa-hand-o-right"></i> <b>Remember to Close Your Requests </b>
            <br>Requests remain open until the <u>requesting team closes them</u>.  When teams respond to your requests, don't forget to Close them to mark them as complete.
        </div> 
    </div>
</div>

<div class="row">
    <div class="col-md-12" id="due_soon">
        <?php echo $this->element('task/urgent_by_team', $urgentByTeam); ?>
    </div>
</div>



<div class="row">
    <div class='col-md-12'>
        <h2 id="open_req">Open Requests to <?php echo ($team_code)?:'';?> (Owing)</h2>
        <div  class="panel panel-bdanger">
            <div class="panel-heading">Open Requests <b>From Other Teams</b> to <b><?php echo ($team_code)?:'';?></b></div>
            <?php 
                if(empty($open_tasks)){ ?>
                <br>
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">
                        <div class="alert alert-success">
                            <i class="fa fa-thumbs-up"></i> <b>No Open Requests:</b> Nice, you currently don't owe anything to other teams.
                        </div>
                    </div>
                </div>
            <?php     
                }
                
                else{
            ?>
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th width="10%">Date</th>
                            <th width="25%">Teams</th>
                            <th width="65%">Description</th>
                        </tr>
                    </thead>
                    <tbody>    
                        <?php
                            foreach($open_tasks as $open_task){
                                echo '<tr>';
                                echo '<td>'.$this->Ops->durationFriendlyDaysOnly($open_task['Task']['start_time'], $open_task['Task']['end_time'], true).'</td>';
                                echo '<td><b>'.$open_task['Task']['task_type'].'</b><br>';
                                echo $this->Ops->ttSigLeadOpen($open_task['TasksTeam'], $zoneCodeTeamCodeList);
                                echo '</td>';
                                echo '<td>'.$open_task['Task']['short_description'].'<br><div class="pull-right">';
                                if($open_task['Task']['due_date']){
                                    echo '<button type="button" class="btn btn-danger btn-xs" style="margin-right:5px;">';
                                    echo '<i class="fa fa-bell-o"></i>&nbsp;';
                                    echo $this->Time->format('M d', $open_task['Task']['due_date']);
                                    echo '</button>';
                                }
                                echo $this->Html->link('<i class="fa fa-eye"></i> View', array(
                                    'controller'=>'tasks',
                                    'action'=>'compile',
                                    '?'=>array('task'=>$open_task['Task']['id'])
                                    ), 
                                    array(
                                        'escape'=>false,
                                        'class'=>'btn btn-default btn-xs task_view_button')
                                    );
                                
                                echo '</div></td>';
                            }    
                        ?>
                </tbody>
            </table>
            <?php } ?>
        </div>
    </div>
</div>
            
<div class="row">
    <div class='col-md-12'>
        <h2 id="waiting_req">Open Requests from <?php echo ($team_code)?:'';?> (Waiting)</h2>

        <div class="panel panel-dark">
            <div class="panel-heading">Open Requests <b>From <?php echo ($team_code)?:'';?></b> <b>to Other Teams</b></div>
            <?php 
                if(empty($waiting_tasks)){ ?>
                <br>
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1">
                        <div class="alert alert-success">
                            <i class="fa fa-thumbs-up"></i> <b>No Open Requests:</b> Nice, other teams currently don't owe you anything.
                        </div>
                    </div>
                </div>
            <?php     
                }
                
                else{
            ?>
                <table class="table table-striped table-condensed">
                    <thead>
                        <tr>
                            <th width="10%">Date</th>
                            <th width="15%">Teams</th>
                            <th width="75%">Description</th>
                        </tr>
                    </thead>
                    <tbody>    
                        <?php
                            foreach($waiting_tasks as $waiting_task){
                                echo '<tr><td>'.$this->Ops->durationFriendlyDaysOnly($waiting_task['Task']['start_time'], $waiting_task['Task']['end_time'], true).'</td>';
                                echo '<td><b>'.$waiting_task['Task']['task_type'].'</b><br>';
                                echo $this->Ops->ttSigLeadOpen($waiting_task['TasksTeam'], $zoneCodeTeamCodeList);
                                echo '</td>';
                                echo '<td><p>'.$waiting_task['Task']['short_description'].'</p>';

                                if(!empty($waiting_task['Assist'])){
                                    echo '<b><i class="fa fa-sitemap"></i> Incoming Links</b>';
                                    foreach($waiting_task['Assist'] as $k => $wt){
                                        echo  $this->Ops->subtaskRowSingle($wt, array('two_line_date'=>true, 'date_format'=>'M j'));    
                                    }    
                                }                                    
                                
                                echo '<div class="pull-right">';
                                
                                if(!empty($waiting_task['Change'])){
                                    $numChange = count($waiting_task['Change']);
                                    echo '<button type="button" class="btn btn-success btn-xs" style="margin-right:5px;">';
                                    echo '<i class="fa fa-exchange"></i>&nbsp;';
                                    echo $numChange.' New';
                                    echo '</button>';    
                                }
                                if($waiting_task['Task']['due_date']){
                                    echo '<button type="button" class="btn btn-danger btn-xs" style="margin-right:5px;">';
                                    echo '<i class="fa fa-bell-o"></i>&nbsp;';
                                    echo $this->Time->format('M d', $waiting_task['Task']['due_date']);
                                    echo '</button>';
                                }
                                echo $this->Html->link('<i class="fa fa-eye"></i> View', array(
                                    'controller'=>'tasks',
                                    'action'=>'compile',
                                    '?'=>array('task'=>$waiting_task['Task']['id'])
                                    ), 
                                    array(
                                        'escape'=>false,
                                        'class'=>'btn btn-default btn-xs task_view_button')
                                    );
                                
                                echo '</div>';
                                echo '</td></tr>';
                            }  // endforeach waiting task  
                        ?>
                </tbody>
            </table>
            
            <?php 
            } // end notempy $waiting_tasks
        ?>    
        </div>
    </div>
</div>
<?php
}   // end not empty $team_id 
?>


<?php 
    echo $this->Js->writeBuffer();
?>
