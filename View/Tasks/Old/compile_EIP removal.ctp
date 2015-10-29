<?php
    $this->assign('page_title', 'Compile Plan ');
    
    echo $this->Html->css('bootstrap-wysihtml5', array('inline'=>false));
    echo $this->Html->script('/eip/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/wysihtml5-0.3.0.min', array('inline'=>false));
    echo $this->Html->script('/eip/inputs-ext/wysihtml5/wysihtml5', array('inline'=>false));
    echo $this->Html->script('bootstrap3-wysihtml5', array('inline'=>false));

    if (AuthComponent::user('id')){
        $controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
        
    //$show_ = $this->request->data('Task.show_threaded');
    $show_details = $this->request->data('Task.show_details');
    
    // JSON encoded for Xeditable
    $j_teams = array();
    foreach ($teams as $cid => $zone){
        foreach($zone as $tid=>$tcode){
            $j_teams[] = array('value'=>$tid,'text'=>$tcode);    
        }
    }
    $j_teams = json_encode($j_teams);
    
    $j_acttypes = array();
    foreach ($actionableTypes as $type_id => $str_type){
        $j_acttypes[] = array('value'=>$type_id,'text'=>$str_type);
    }
    $j_acttypes = json_encode($j_acttypes);
    
    $j_tasktypes = array();
    foreach ($taskTypes as $k => $category){
        foreach ($category as $type_id => $str_type){
           $j_tasktypes[] = array('value'=>$type_id,'text'=>$str_type);
        }    
    }
    $j_tasktypes = json_encode($j_tasktypes);
    
    
    

$this->Js->buffer("
    //EIP
    $('a.eip_task_start_time').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipStartTime'))."',
        title: 'Task Start Time',
        format: 'yyyy-mm-dd hh:ii:ss',
        container: 'body',
        clear: false,
        showbuttons:false,    
        viewformat: 'M d hh:ii',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'A start time is required.';
            }
        },
        datetimepicker: {
            //weekStart: 1,
            //todayBtn: 'linked',
            todayHighlight: true,
            minuteStep: 1,
            
            pickerPosition: 'top-left',
            startView: 0,
       }
    });

    $('a.eip_task_end_time').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipEndTime'))."',
        title: 'Task End Time',
        format: 'yyyy-mm-dd hh:ii:ss',  
        clear: false,          
        showbuttons:false,
        viewformat: 'M d hh:ii',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'A start time is required.';
            }
        },
        datetimepicker: {
            //weekStart: 1,
            //todayBtn: 'linked',
            todayHighlight: true,
            minuteStep: 1,
            pickerPosition: 'bottom-left',
            startView:0
       }
    });
    
    $('a.eip_task_public').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipPublic'))."',
        title: 'Task Visibility',
        type: 'select',
        showbuttons: false,
        source: [
          {value: 0, text: 'Private'},
          {value: 1, text: 'Public'},
        ],
        display: function(value, sourceData) {
            if(value==1){
                var outhtml = '<span><i class=\"fa fa-lg fa-eye\"></i> <small>Public</small></span>';
            }
            else{
                var outhtml = '<span><i class=\"fa fa-lg fa-eye-slash\" ></i> <small>Private</small></span>';
            }
                
            $(this).html(outhtml);
        },
    });
    
    $('a.eip_task_type').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipTaskType'))."',
        title: 'Task Type',
        type: 'select',
        showbuttons: false,
         source:".$j_tasktypes.",
    });
    
    $('a.eip_short_description').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipShortDescription'))."',
        title: 'Short Description (140 Characters)',
        type: 'text',
        showbuttons: true,
        validate: function(value) {
            if($.trim(value) == '') {
                return 'A short description is required.';
            }
            if(value.length> 140){
                return 'This field is limited to 140 characters.  You entered '+value.length+'.';
            }
        },
    });

    var offset = 420;
    var duration = 500;
    $(window).scroll(function() {
        if ($(this).scrollTop() > offset) {
            $('.back-to-top').fadeIn(duration);
        } else {
            $('.back-to-top').fadeOut(duration);
        }
    });
                
    $('.back-to-top').click(function(event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, duration);
        return false;
        
    });
    
    $('#performAjaxLink').click(
            function()
            {                
                $.ajax({
                    type:'POST',
                    async: true,
                    cache: false,
                    url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'hello'))."',
                    success: function(response) {
                        //console.log(response);
                        //var team_code = response.Task.team_code;
                        var dataFromServer = JSON.parse(response);
                        console.log(dataFromServer);
                        $('#resultField').val(dataFromServer.Task.team_code);
                    },
                    //data: $('form').serialize()
                });
                return false;
            }
    );
    
    $('.info').on('click', function(){
        
    });
    
    $('div.task-panel').on('click', function(e){
        
        console.log('click registered');
        
        var tid = $(this).attr('data-taskid');
        var ttask = $(this);
        var ttid = 'div #td_'+$(this).attr('data-taskid');
        
        var tddiv = $(ttid);
        
        //console.log($(this));
       
       $.ajax( {
            //@DBALL URL CHECK for LIVE SERVER
            url: '/cake/tasks/taskDetails/'+tid,
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                
                //var ttid = $(this).attr('id');
                
                //$(ttid)
                
                //alert(ttid);
                //var d = $('<div></div>'); 
                //$(this).append(d);
                //d.html(data)
                //.fadeIn();
                //d.html(data);
                tddiv.html(data).collapse('show');
                
                tddiv.on('shown.bs.collapse', function () {
                   //alert('shown');
                     ttask.on('click', function(){
                        //tddiv.collapse('hide');
                        tddiv.collapse('hide');
                    }); 
                    // do something…
                });
                
                //tddiv.slideDown('slow');
                //ttask.append(d);
                
                
                
                //$('#ajax-content-load').html(data);
            },
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
          });
          
          
          return false;
       
       
       
       
       
        
    });
    
    
");


?>
    <style>
        .back-to-top {
            position: fixed;
            bottom: 2em;
            right: 0px;
            text-decoration: none !important;
            color: #000000;
            background-color: rgba(235, 235, 235, 0.80);
            font-size: 12px;
            padding: 1em;
            display: none;
        }

        .back-to-top:hover {
            text-decoration: none !important;
    
            background-color: rgba(135, 135, 135, 0.50);
        }   
    </style>

    <div class="row">
        <h2>
            <?php echo $this->fetch('page_title');
            //echo '<p>Total Keys: ' . $rc . '</p>';
            ?>
            <a href="#" class="back-to-top"><i class="fa fa-2x fa-arrow-circle-o-up"></i> <span class="h4">Top</span></a>
            <small><?php echo $this->fetch('page_title_sub');?></small>
        </h2>
    </div>
   <div class="row">
        <div>
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#quick_add" data-toggle="tab">Add Task</a></li>
                <li><a href="#compile_options" data-toggle="tab">Compile Options</a></li>
            </ul>
            
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in col-md-12 vsm-top-marg" id="compile_options">
                    <?php echo $this->element('task/compile_options'); ?>
                </div>
                <div class="tab-pane fade in active col-md-12 vsm-top-marg" id="quick_add">
                    <?php echo $this->element('task/quick_add'); ?>
                </div>
            </div>
        </div>
    </div>

	<div id="page-content" class="row">
 
    <?php 
        if (!empty($tasks)){ ?>
            <div class="tasks index">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <p><i class="fa fa-clock-o"></i> Due Date, <i class="fa fa-exchange"></i> Changes, <i class="fa fa-flag"></i> Action Item</p>
                    </div>
                </div>
            </div>
            
            <?php
                
                $today = date('Y-m-d');
                $today_str = strtotime($today);
                $owa = strtotime($today.'-1 day');
                
                $owfn = strtotime($today.'+1 week');
                
                foreach ($tasks as $task):
                    //Hide/show elements based on permissions.
                    $userControls = false;
                    if(in_array($task['Task']['team_id'], $controlled_teams)){ $userControls = true; }
                    $hasDueDate = false; $hasDueSoon = false; $hasActionable = false; $hasChange = false; $hasNewChange = false;
                    
                    if(!empty($task['Task']['due_date'])){
                        $dueString = strtotime($task['Task']['due_date']);
                        $hasDueDate = true;
                        if(($dueString >= $today_str) && ($dueString < $owfn)){ $hasDueSoon = true; }
                    }
                    if(!empty($task['Task']['actionable_type'])){ $hasActionable = true; }
                    if (!empty($task['Change'])){
                        $hasChange = true;
                        $numChange = 0;
                        foreach ($task['Change'] as $chg){
                            if (strtotime($chg['created'])  > $owa){
                                $hasNewChange = true;
                                $numChange++;
                            }
                        }  
                    }
        ?>
            <div class="row">
            <div class="col-md-12">
                <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" 
                    class="panel panel-default task-panel" 
                    style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-sm-2">
                                <label class="task">
                                    <input type="checkbox" data-taskid="<?php echo $task['Task']['id']?>" id="checkbox4"/>
                                    <?php
                                        echo $this->Time->format('M j H:i', $task['Task']['start_time']);
                                        echo '-';
                                        echo $this->Time->format('H:i', $task['Task']['end_time']);

                                        $time1 = strtotime($task['Task']['start_time']);
                                        $time2 = strtotime($task['Task']['end_time']);
                                        $diff = $time2 - $time1;
            
                                        // NOTE: LIMITATION Hides for duration less than one min.  May impact PRO
                                        // since their events last seconds [for everyone else though, it makes sense]
                                        if((60 < $diff)  && ($diff <= 3599)){
                                            echo '<br/>('.gmdate("i", $diff).' min)';
                                        }
                                        elseif($diff > 3599){
                                            echo '<br/>('.gmdate('H', $diff).' hr, '.gmdate('i',$diff).' min)';  
                                        }
                                    ?>
                                    </input>
                                </label>     
                            </div>
                            <div class="col-sm-10">
                                <div class="pull-right">
                                    <?php 
                                        if($hasActionable){
                                            echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                            echo '<i class="fa fa-flag fa-lg"></i>&nbsp';
                                            echo $task['Task']['actionable_type'];
                                            echo '</button><br/>';
                                        }
                                        if($hasDueDate){
                                            if($hasDueSoon){
                                                echo '<button type="button" class="btn btn-danger btn-xs xxs-bot-marg">';
                                            }
                                            else {
                                                echo '<button type="button" class="btn btn-primary btn-xs xxs-bot-marg">';
                                            }
                                        echo '<i class="fa fa-clock-o"></i>&nbsp;';
                                        echo $this->Time->format('M d', $task['Task']['due_date']);
                                        echo '</button><br/>';
                                    }
                                        if($hasChange && $hasNewChange){
                                            echo '<button type="button" class="btn btn-success btn-xs xxs-bot-marg">';
                                            echo '<i class="fa fa-exchange"></i>&nbsp;';
                                            echo $numChange.' New';
                                            echo '</button><br/>';    
                                        }
                                    ?>
                                        <div class="btn-group">
                                            <?php echo $this->Html->link(__('View'), array('controller'=>'tasks', 'action' => 'view', $task['Task']['id']), array('class' => 'btn btn-default btn-xs')); ?>
                                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                <span class="caret"></span>
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <ul class="dropdown-menu" role="menu">
                                                <li>
                                                    <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view', $task['Task']['id'])); ?>
                                                </li>
                                                <?php if($userControls):?>
                                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', $task['Task']['id'])); ?></li>
                                                <?php endif; 
                                                if ($user_role >= 200 || $userControls):?>
                                                    <li class="divider"></li>
                                                    <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', $task['Task']['id']), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                                <?php endif;?> 
                                            </ul>
                                        </div>
                                </div><!--flags-->
                                <?php
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
                                
                                    echo '<b>('.$task['Task']['task_type'].')</b> &nbsp;';
                                    echo $task['Task']['short_description'].'<br/>';
                                    echo $buttons.'<br/>';
                                    
                                    if ($show_details && !empty($task['Task']['details'])){
                                        echo '<hr align="left" style="width: 80%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                        echo '<u>Details:</u><br/>'; 
                                        echo nl2br($task['Task']['details']);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!--task row-->
        
        <div class="row">
            <div class="col-md-12" id="td_<?php echo ($task['Task']['id']); ?>"></div>
            
        </div>
        <?php endforeach; ?>   

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
            ?>
        </ul><!-- /.pagination -->
    </div><!-- /.index -->
            
    <?php  }
        else {
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
    ?>
</div>	    

<?php echo $this->Js->writeBuffer(); ?>
