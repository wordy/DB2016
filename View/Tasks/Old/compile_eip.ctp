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
        
    $show_threaded = $this->request->data('Task.show_threaded');
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
                <li class="active"><a href="#compile_options" data-toggle="tab">Compile Options</a></li>
                <li><a href="#quick_add" data-toggle="tab">Add Task</a></li>
            </ul>
            
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in active col-md-12 vsm-top-marg" id="compile_options">
                    <?php echo $this->element('task/compile_options'); ?>
                </div>
                <div class="tab-pane fade in col-md-12 vsm-top-marg" id="quick_add">
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
                        <p><i class="fa fa-exchange"></i> Changes, <i class="fa fa-paperclip"></i> Attachments, <i class="fa fa-clock-o"></i> Due Date, <i class="fa fa-flag"></i> Action Item, <i class="fa fa-eye"></i> Visibility</p>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="tasks-index" class="table table-hover table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th width="1%"> </th>
                            <th width="10%"><?php echo __('Time'); ?></th>
                            <th width="16%"><?php echo __('Teams'); ?></th>
                            <th width="55%"><?php echo __('Description'); ?></th>
                            <th width="9%">Icons</th>
                            <th width="9%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $today = date('Y-m-d');
                        $today_str = strtotime($today);
                        $owa = strtotime($today.'-1 day');
                        
                        $owfn = strtotime($today.'+1 week');
                        
                        foreach ($tasks as $task):
                            $hasDueDate = false;
                            $hasDueSoon = false;
                            $hasActionable = false;
                            $hasAttachment = false;
                            $hasNewAttachment = false;
                            $hasChange = false;
                            $hasNewChange = false;
                            
                            if(!empty($task['Task']['due_date'])){
                                $dueString = strtotime($task['Task']['due_date']);
                                $hasDueDate = true;
                                
                                if(($dueString >= $today_str) && ($dueString < $owfn)){
                                    $hasDueSoon = true;
                                }
                            }
                            
                            if(!empty($task['Task']['actionable_type'])){
                                $hasActionable = true;
                            }
                            
                            if (!empty($task['Attachment'])){
                                $hasAttachment = true;
                                $numNewAttachment = 0;
                                $numAttachment = 0;
                                foreach ($task['Attachment'] as $att){
                                    $numAttachment++;
                                    if (strtotime($att['created']) > $owa){
                                        $hasNewAttachment = true;
                                        $numNewAttachment++;
                                    }
                                }  
                            }
                            
                            if (!empty($task['Change'])){
                                $hasChange = true;
                                $numChange = 0;
                                foreach ($task['Change'] as $chg){
                                    if (strtotime($chg['created'])  > $owa){
                                        $hasNewChange = true;
                                        //break;
                                        $numChange++;
                                    }
                                }  
                            }
                        
                        ?>

                        <tr <?php 
                            if($this->request->data('Task.color_act_pri')){
                                if($task['Task']['public']==0){
                                    echo 'class= "active"';
                                }
                                elseif($task['Task']['actionable_type_id']){
                                    echo 'class="danger"';
                                }
                                elseif(!empty($task['Task']['due_date'])) {
                                    $now = strtotime($today);
                                    $owfn = strtotime($today.'+1 week');
                                    $duedate = strtotime($task['Task']['due_date']);
                                    
                                    if (($duedate > $now) && ($duedate < $owfn)){
                                        echo 'class="warning"';    
                                    }
                                }
                            }
                        ?>>
                        <td 
                        <?php 
                            if ($this->request->data('Task.color_team')){
                                echo 'style="background:'.$task['Task']['task_color_code'].'"';
                            }
                         ?>>
                        </td> 
                        <td>
                        <?php
                            if(in_array($task['Task']['team_id'], $controlled_teams)){
                                echo '<a href="#" class="eip_task_start_time" data-format = "yyyy-mm-dd hh:ii:ss" data-value ="'.date('Y-m-d H:i:s', strtotime($task['Task']['start_time'])). '" data-name="Task.start_time" data-type="datetime" data-pk="'.$task['Task']['id'].'">';
                                echo $this->Time->format('M d H:i', $task['Task']['start_time']);
                                echo '</a> - ';
                                
                                echo '<a href="#" class="eip_task_end_time" data-format = "yyyy-mm-dd hh:ii:ss" data-value ="'.date('Y-m-d H:i:s', strtotime($task['Task']['end_time'])). '" data-name="Task.end_time" data-type="datetime" data-pk="'.$task['Task']['id'].'">';
                                echo '<br/>';
                                echo $this->Time->format('M d H:i', $task['Task']['end_time']);
                                echo '</a>';
                                echo '&nbsp;&nbsp;';       
                            }
                            
                            else{
                                echo $this->Time->format('M d H:i', $task['Task']['start_time']);
                            }
                                
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
                        </td>

                        <td>
                        <?php 
                            if(in_array($task['Task']['team_id'], $controlled_teams)){
                                echo '<a href="#" class="eip_task_type" data-value="'.$task['Task']['task_type_id'].'" data-name="Task.task_type_id" data-type="select" data-pk="'.$task['Task']['id'].'">';
                                echo '<b>'.$task['Task']['task_type'].'</b>';
                                echo '</a><br/>';    
                               
                            }
    
                            else{
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            }                     

                            $tt = $task['TasksTeam'];
                            $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
                            $tt_c = Hash::extract($tt, '{n}[task_role_id=2]');
                                    
                            $ttc_active = array();
                            $ttc_inactive = array();
                            
                            if(!empty($tt_c)){
                                foreach ($tt_c as $cteam){
                                    if(($cteam['link_count']) >= 1){
                                        $ttc_active[] = $cteam['team_code'];
                                    }
                                    else{
                                        $ttc_inactive[] = $cteam['team_code'];
                                    }        
                                }
                            }

                            $tt_lp = Hash::extract($tt, '{n}[task_role_id=3].team_code');
                                    
                            $buttons = '';
                            if (!empty($tt_l)){
                                $buttons.= '<span class="btn btn-medgrey btn-xxs">'.$tt_l[0].'</span>';
                            }

                            if (!empty($tt_lp)){
                                $buttons.= '&nbsp;<i class="fa fa-long-arrow-right"></i><span class="btn btn-medgrey btn-xxs">'.$tt_lp[0].'</span>';
                            }

                            foreach ($ttc_active as $cta){
                                $buttons.= '<span class="btn btn-success btn-xxs">'.$cta.'</span>';     
                            }

                            foreach ($ttc_inactive as $cti){
                                $buttons.= '<span class="btn btn-default btn-xxs">'.$cti.'</span>';
                            }
                            echo $buttons;
                        ?>
                        </td>

                        <td>
                        <?php
                            if(in_array($task['Task']['team_id'], $controlled_teams)){
                                echo '<a href="#" class="eip_short_description" data-value="'.$task['Task']['short_description'].'" data-name="Task.short_description" data-type="text" data-pk="'.$task['Task']['id'].'">';
                                echo $task['Task']['short_description'];
                                echo '</a>';    
                            }
                            else{
                                echo $task['Task']['short_description'];
                            } 

                            if ($show_details && !empty($task['Task']['details'])){
                                echo '<hr style="margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                echo '<u>Details:</u><br/>'; 
                                echo nl2br($task['Task']['details']);
                            }
                                
                            if(!empty($task['Subtask']) && $show_threaded){
                                echo '<hr style="margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                echo '<u>Subtasks:</u><br/>'; 
                        
                                foreach ($task['Subtask'] as $st){
                                    $tSig = $this->Ops->makeInlineTaskTeamSignature($st['TasksTeam']);
                                    echo date('M d H:i', strtotime($st['start_time'])) .' '; 
                                    echo implode(null, $tSig).'  ';
                                    echo $this->Html->link($st['short_description'], array(
                                        'controller'=>'tasks', 
                                        'action'=>'view', $st['id']));
                                    echo '<br/>';
                                }
                            }
                        ?>
                        </td>
                        <td>
                            <?php 
                                if($hasChange && $hasNewChange){
                                    echo '<b><i class="fa fa-exchange fa-lg highlight-new"></i></b>&nbsp;<small>'.$numChange.' New';
                                    echo '</small><br/>';                                    
                                }
                                if($hasAttachment && $hasNewAttachment){
                                    echo '<b><i class="fa fa-paperclip fa-lg highlight-new"></i></b>&nbsp;<small>'.$numNewAttachment.' New';
                                    echo '</small><br/>';                
                                }
                                elseif($hasAttachment && !$hasNewAttachment){
                                    
                                    echo '<b><i class="fa fa-paperclip fa-lg text-muted"></i></b>&nbsp;<small>'.$numAttachment.' File';
                                    echo '</small><br/>';
                                }
                                if($hasDueDate && $hasDueSoon){
                                    echo '<b><i class="fa fa-clock-o fa-lg highlight-duesoon"></i></b>&nbsp;<small>'.date('M d', strtotime($task['Task']['due_date'])).'</small><br/>';
                                }
                                if($hasActionable){
                                    echo '<b><i class="fa fa-flag fa-lg highlight-duesoon"></i></b>&nbsp;<small>'.$task['Task']['actionable_type'].'</small><br/>';
                                }

                                if(in_array($task['Task']['team_id'], $controlled_teams)){
                                    echo '<a href="#" class="eip_task_public" data-value="';
                                    if($task['Task']['public']){ echo 1;} else{echo 0;}
                                    echo '" data-name="Task.public" data-type="select" data-pk="'.$task['Task']['id'].'">';
                                    echo '<i class="fa fa-lg fa-eye"></i>';
                                    echo '</a>';
                                }
                                else{
                                    echo ($task['Task']['public'] ? '<i class="fa fa-lg fa-eye"></i> <small>Public</small>' : '<i class="fa fa-eye text-muted"></i> <small>Private</small>');
                                    //echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                }    


                                
                            ?>
                        </td>
                        <td>
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
                                    <?php if(in_array($task['Task']['team_id'], $controlled_teams)):?>
                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', $task['Task']['id'])); ?></li>
                                    <?php endif; 
                                    if ($user_role >= 200 || in_array($task['Task']['team_id'], $controlled_teams)  ):?>
                                        <li class="divider"></li>
                                        <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', $task['Task']['id']), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                    <?php endif;?> 
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div><!-- /.table-responsive -->
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

<?php //echo $this->Js->writeBuffer(); ?>
