<?php
    //$this->assign('page_title', 'Compile Plan ');
    
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
    $zcount = count($teams);
    
    //debug($zcount);
    
    for ($i=0; $i<$zcount; $i++) {
        $ztlist[$i] = array_keys($teams['Zone '.$i]);
    }
    
    //debug($ztlist);
    
    
    $ztlist_c = array();
    foreach ($ztlist as $key=>$team_ids){
        $ztlist_c[$key] = count($team_ids);
    }
    
    //debug($ztlist_c);
    //debug($ztlist);
    
        
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

    $('.task-panel-heading').on('click', function(event){
        var tdheading_div = $(this);
        var tid = $(this).attr('data-tid');
        //alert(tid);
        //var ttask = $(this);
        var tdbody_id = 'div #task_detail_'+tid;
        
        var tdbody_div = $(tdbody_id);
                   
        $.ajax( {
            url: '/tasks/taskDetails/'+tid,
            beforeSend:function () {
                $('#ajaxProgress').fadeIn();
            },
            success:function(data, textStatus) {
                tdbody_div.html(data).collapse('show');
                //$('#ajax-content-load').html(data);
            },
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajaxProgress').fadeOut();
            }, 
            type: 'post',
            dataType:'html',
        });


        event.preventDefault();        
        return false;
    });
    
    
    
    
    
    
");


?>
    <style>
  
    </style>
    <a href="#" class="back-to-top"><i class="fa fa-2x fa-arrow-circle-o-up"></i> <span class="h4">Top</span></a>
    
    <div class="row">
        <h2>
            Compile Tasks
            <span id="ajaxProgress" style="display: none; margin-left: 10px; vertical-align: top;">
                <?php echo $this->Html->image('ajax-loader_old.gif'); ?>                    
            </span>
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
        <div class="row" id="tasksList">
            <div class="col-md-12">
                <div data-taskid="<?php echo ($task['Task']['id']); ?>" id="tid<?php echo ($task['Task']['id']); ?>" 
                    class="panel panel-default task-panel" 
                    style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
                 >
                    <div class="panel-heading task-panel-heading" data-tid="<?php echo ($task['Task']['id']); ?>">
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
                            <div class="col-sm-2">
                                <?php
                                    $tt = $task['TasksTeam'];
                                    
                                    
                                    //$tbz = Hash::extract($teams, '{s}.{n}');
                                    
                                    
                                    //$tbz = Hash::combine($teams, '{s}','{s}.{n}','{s}.{n}.zone');
                                    //debug($ztlist);
                                    
                                    $buttons13 = '';
                                    $buttons2 = '';
                            
                                    
                                    $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
                                    $buttons13.= '<span class="btn btn-leadt">'.$tt_l[0].'</span>';
                                    
                                    
                                    
                                    //$tt_p = Hash::extract($tt, '{n}[task_role_id=2]');
                                    //$tt_p_ids = Set::combine($tt_p, '{n}.team_id');
                                     
                                     $tt_p = Hash::combine($tt, '{n}.team_id', '{n}');
                                     
                                     //debug($tt_p);
                                     $tt_p_ids = array_keys($tt_p);
                                     
                                    //Hash::extract($tt, '{n}[task_role_id=2].team_id');
                                    
                                    
                                    //debug($tt_p_ids);
                                    foreach ($ztlist as $zone => $tlist){
                                        $zone_t_count = 0;
                                        foreach ($tlist as $k => $tid){
                                            if(in_array($tid, $tt_p_ids)){
                                                $zone_t_count++;
                                            }
                                        }
                                         
                                        if($zone_t_count == $ztlist_c[$zone] || $zone_t_count == $ztlist_c[$zone]-1){
                                            $buttons2.= '<span class="btn btn-default btn-xxs">Z'.$zone.'</span>';
                                            
                                            foreach ($tlist as $k => $tid){
                                                unset($tt_p[$tid]);
                                                
                                            }
                                        }
                                        
                                        
                                        //debug($tlist);
                                        /*
                                        if(in_array($tlist, $tt_p_ids)){
                                            
                                            //debug($tt_p);
                                            //debug($tlist);
                                          $buttons2.= '<span class="btn btn-default btn-xxs">Z'.$zone.'</span>';
                                          $tt_p = array_diff($tt_p, $tlist);
                                        }*/
                                    }
                                    
                                    foreach ($tt_p as $k => $team){
                                        $buttons2.= '<span class="btn btn-default btn-xxs">'.$team['team_code'].'</span>';
                                    }                                    
                                    
                                    $tt_r = Hash::extract($tt, '{n}[task_role_id=3]');
                                    foreach ($tt_r as $k => $team){
                                        $buttons13.= '<span class="btn btn-danger btn-xxs">'.$team['team_code'].'</span>';
                                    }

                                //This is a retarded way to show requests before pushes
                                $buttons = $buttons13.$buttons2;
                                
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                echo $buttons;
                                ?> 
                            </div>
                            <div class="col-sm-8">
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
      
                    <div class="panel-body collapse task-panel-body" id="task_detail_<?php echo $task['Task']['id'];?>">
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
