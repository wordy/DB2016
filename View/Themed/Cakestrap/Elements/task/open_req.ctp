<?php 
    $this->Js->buffer("
        $('#dashChangeTeam').on('change', function(e){
            var val = $(this).find('option:selected').text();
            window.location = '/teams/home/'+val;    
        });
    
        $('.helpTTs').popover({
            container: 'body',
            html:true,
        });
        
        $('#open_task_link').on('click', function(){
            $('html, body').animate({
                scrollTop: $('#open_req').offset().top-60}, 800);
        });

        $('#waiting_task_link').on('click', function(){
            $('html, body').animate({
                scrollTop: $('#waiting_req').offset().top-60}, 800);
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


<h1 id="pageTitle"><?php echo isset($team_code) ? $team_code. ' ':''; ?>Team Dashboard</h1>
<br>
                

<div class="row">
    <div class="col-md-4">
        <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-info slim-alert">
                <?php echo $this->Form->label('team_id', 'Select Team');?> 
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
                ?>
            </div>
        </div>
    </div>
        <?php 
            if(!empty($team_id)):
        ?>
        <ul class="nav nav-pills nav-stacked">
            <li>
                <a id="due_soon" href="#"><h4><i class="fa fa-bell-o fa-fw"></i> <?php echo ($team_code)?:'';?> Due Soon</span></h4>
                    Tasks ending or due within the next 2 weeks.
                </a>
            </li>

            <li>
                <a id="open_task_link" href="#"><h4><i class="fa fa-life-saver fa-fw"></i> <?php echo ($team_code)?:'';?> Responses Required <span class="badge <?php echo ($iOpenReq == 0)? 'badge-yh ': 'badge-danger ';?> pull-right"><?php echo $iOpenReq;?></span></h4>
                    Tasks where other teams asked for your help and you haven't responded.
                </a>
            </li>
            <li>
                <a id="waiting_task_link" href="#"><h4><i class="fa fa-hourglass-o fa-fw"></i> Outstanding Requests <?php echo ($team_code)?'from '.$team_code:'';?><span class="badge <?php echo ($iWaitingReq == 0)? 'badge-yh': 'badge-danger';?> pull-right"><?php echo $iWaitingReq;?></span></h4>
                    Tasks where you asked for help from other teams and you're waiting on a response.
                </a>
            </li>
            <!--
            <li>
                <a href="http://www.jquery2dotnet.com"><i class="fa fa-bar-chart-o fa-fw"></i> Charts</a>
            </li>
            <li class="active"><a href="#"><i class="fa fa-home fa-fw"></i>Home</a></li>-->
        </ul>
        <?php 
            endif;
        ?>
        <br><br><br>

    </div>
    <div class="col-md-8">
            <?php
            if(empty($team_id)): ?>
    
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-info">
                <i class="fa fa-group"></i> <b>Choose Team </b> Select a team first to view their dashboard.
            </div>
        </div>
    </div>
<?php 
    endif;
    ?>
        

    
    <?php
    
    if (!empty($team_id)):?>
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->element('task/urgent_by_team', $urgentByTeam); ?>
            </div>
        </div>
        
        <div class="row lg-top-marg">
            <div class="col-xs-12">

            <div class="alert alert-info xs-bot-marg">
                <b>Note About Requests: </b> Requests remain open until the <b>requesting team closes them</b>. <br>If you respond to a request, it will show up here until the requesting team has reviewed your response and closed the request.
            </div><br>
    </div>
</div>
    
        <div class="row sm">
            <div class='col-md-12'>
                <div id="open_req" class="panel panel-bdanger">
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
                                        echo $this->Ops->ttSigLeadOpen($open_task['TasksTeam'], $zoneTeamCodeList);
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
                <div id="waiting_req" class="panel panel-dark">
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
                                    <th width="25%">Teams</th>
                                    <th width="65%">Description</th>
                                </tr>
                            </thead>
                            <tbody>    
                                <?php
                                    foreach($waiting_tasks as $waiting_task){
                                        echo '<tr>';
                                        echo '<td>'.$this->Ops->durationFriendlyDaysOnly($waiting_task['Task']['start_time'], $waiting_task['Task']['end_time'], true).'</td>';
                                        echo '<td><b>'.$waiting_task['Task']['task_type'].'</b><br>';
                                        echo $this->Ops->ttSigLeadOpen($waiting_task['TasksTeam'], $zoneTeamCodeList);
                                        echo '</td>';
                                        echo '<td><p>'.$waiting_task['Task']['short_description'].'</p><div class="pull-right">';
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
                                        
                                        echo '</div></td>';
                                    }    
                                ?>
                        </tbody>
                    </table>
                    <?php }?>    
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
    

<?php 
    echo $this->Js->writeBuffer();
?>
