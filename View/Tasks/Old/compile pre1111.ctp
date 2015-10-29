<?php
    $this->assign('page_title', 'Compile Plan ');
    
    echo $this->Html->css('bootstrap-wysihtml5', array('inline'=>false));
    //echo $this->Html->script('/eip/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/wysihtml5-0.3.0.min', array('inline'=>false));
    //echo $this->Html->script('/eip/inputs-ext/wysihtml5/wysihtml5', array('inline'=>false));
    echo $this->Html->script('bootstrap3-wysihtml5', array('inline'=>false));
    //echo $this->Html->script('task_details', array('inline'=>false));

    if (AuthComponent::user('id')){
        $controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
    
    // Figures out teams in each zone, to combine buttons
    $ztlist = array();
    $zcount = count($teams)-1;
    for ($i=0; $i<$zcount; $i++) {
        $ztlist[$i] = array_keys($teams['Zone '.$i]);
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
    
    
    ");    
    

$this->Js->buffer("
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
    
    $('div .task-buttons').click(function(event){
        event.stopPropagation();
    });
    
    $('.tl_2').on('click', function(event){
           alert('click');
        
            
        var tid = $(this).attr('data-tid');
        
        
        
        var ttask = $(this);
        var ttid = 'div #task_detail_'+$(this).attr('data-tid');
        
        var tddiv = $(ttid);
               
       $.ajax( {
            //@DBALL URL CHECK for LIVE SERVER
            url: '/cake/tasks/taskDetails/'+tid,
            beforeSend:function () {
                //$('#ajax-menu-spinner').fadeIn();
            },
            success:function(data, textStatus) {
                tddiv.append(data).collapse('show');
                //$('#ajax-content-load').html(data);
            },
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
          });
          
          
          return false;
    
        
    
        
    });
    
    $('div.task-panel').on('click', function(e){
        
        //console.log('click registered');
        
        var tid = $(this).attr('data-taskid');
        var ttask = $(this);
        var ttid = 'div #td_'+$(this).attr('data-taskid');
        
        var tddiv = $(ttid);
               
       $.ajax( {
            //@DBALL URL CHECK for LIVE SERVER
            url: '/cake/tasks/taskDetails/'+tid,
            beforeSend:function () {
                //$('#ajax-menu-spinner').fadeIn();
            },
            success:function(data, textStatus) {
                
                //var ttid = $(this).attr('id');
                
                //$(ttid)
                
                //alert(ttid);
                //var d = $('<div></div>'); 
                //$(this).append(d);
                //d.html(data)
                //.fadeIn();
                //d.html(data);
                //tddiv.html(data).collapse('show');
                tddiv.append(data).collapse('show');
                
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
            <a href="#" class="test_link"><?php echo $this->fetch('page_title'); ?></a>
            <a href="#" class="back-to-top"><i class="fa fa-2x fa-arrow-circle-o-up"></i> <span class="h4">Top</span></a>
            <small><?php echo $this->fetch('page_title_sub');?></small>
        </h2>
    </div>
   <div class="row">
        <div>
            <ul id="myTab" class="nav nav-tabs">
                <li><a href="#quick_add" data-toggle="tab">Add Task</a></li>
                <li class="active"><a href="#compile_options" data-toggle="tab">Compile Options</a></li>
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
                        <p><i class="fa fa-clock-o"></i> Due Date, <i class="fa fa-exchange"></i> Changes, <i class="fa fa-flag"></i> Action Item</p>
                    </div>
                </div>
            </div>
            
            
            <?php //echo $this->element('task/task_list2015', array('task'=>$task, 'show_details'=>$show_details)); ?>

            
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
                    $z0c = $z1c = $z2c = $z3c = $z4c = false;
                    
        ?>

        
        <div class="row">
            <div class="col-md-12" id="td_<?php echo ($task['Task']['id']); ?>"></div>
            
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" 
                    class="panel panel-default task-panel" 
                    style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
                 >
                    <div class="panel-heading">
                        <div class="row">
                            <div class="col-sm-2">
                                <label class="task">
                                    <input type="checkbox" data-taskid="<?php echo $task['Task']['id']?>" id="hide_<?php echo $tid;?>">
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
                            <div class="col-sm-3">
                                <?php
                                    $tt = $task['TasksTeam'];
                                    
                                    $ztlist = array();
                                    $zcount = count($teams)-1;
                                    for ($i=0; $i<$zcount; $i++) {
                                        $ztlist[$i] = array_keys($teams['Zone '.$i]);
                                    }
                                    
                                    //$tbz = Hash::extract($teams, '{s}.{n}');
                                    
                                    
                                    //$tbz = Hash::combine($teams, '{s}','{s}.{n}','{s}.{n}.zone');
                                    //debug($ztlist);
                                    
                                    
                                    
                                    $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
                                    $tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_id');
                                    
                                    /*
                                    for($v = 0; $v < $zcount; $v++){
                                        if(in_array($ztlist[$v], $tt_p)){
                                            $z
                                        }
                                    }
                                     
                                     
                                    
                                    foreach ($ztlist as $z=>$team_ids){
                                        
                                    }
                                    */
                                    
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
                                
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                echo $buttons;
                                ?> 
                            </div>
                            <div class="col-sm-7">
                                <div class="pull-right task-buttons">
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
                                            
                                            <?php echo $this->Html->link(__('Details'), array(
                                                'controller'=>'tasks', 
                                                'action' => 'taskDetails', $task['Task']['id']), 
                                                array(
                                                    'data-tid'=>$task['Task']['id'], 
                                                    'class' => 'btn btn-default btn-xs tl_2')); 
                                            ?>
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
                                    echo $task['Task']['short_description'].'<br/>';
                                    
                                    if ($show_details && !empty($task['Task']['details'])){
                                        echo '<hr align="left" style="width: 80%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                        echo '<u>Details:</u><br/>'; 
                                        echo nl2br($task['Task']['details']);
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
      
                    <div class="panel-body collapse" id="task_detail_<?php echo $task['Task']['id'];?>">
                        Panel content
                    </div>    
                </div>
            
              </div>
        
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
