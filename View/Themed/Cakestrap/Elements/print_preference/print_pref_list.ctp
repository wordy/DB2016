    <?php
        
    
    $today = date('Y-m-d');
    $today_str = strtotime($today);
    $owa = strtotime($today.'-1 week');
    $owfn = strtotime($today.'+8 days');
    
    $this->Paginator->options(array(
        'update' => '#printTaskListWrap',
        'evalScripts' => true,
        'before' => $this->Js->get('#pSpinner')->effect('fadeIn', array('buffer' => false)),
        'complete' => $this->Js->get('#pSpinner')->effect('fadeOut', array('buffer' => false)),
        'url' => array('controller' => 'tasks', 'action' => 'userPrint')
    ));
    
    
    if (!empty($tasks)){ ?>
    <div class="tasks index">

    <?php
         // Hold days of tasks
    $cur_t_day = '';
    $prev_t_day = '';
    $last_t_day = '';
    $last_t_hr = '';
    $curr_t_day = '';            
    $curr_t_hr = '';
    
    $eday_var = Configure::read('EventLongDate');
    $eday = date('Y-m-d',  strtotime($eday_var)); 
        foreach ($tasks as $task):
                    $daysAreSame = false;
        $onEday = false;
        $hoursAreSame = false;
        
        $curr_t_day = date('Y-m-d', strtotime($task['Task']['start_time']));
        
        
        
        $curr_t_hr = date('H', strtotime($task['Task']['start_time']));
        //print_r($user_controls);
        //echo $task[
        //'Task']['team_id'];
        //debug($last_t_day);
        //debug($curr_t_day);        
        if($last_t_day == $curr_t_day){
            $daysAreSame = true;
        }
        
        if($curr_t_day == $eday){
            $onEday = true;
        }
        
        if($curr_t_hr == $last_t_hr){
            $hoursAreSame = true;
        }
        
            
            
        $hide_task = false;
        $hide_det = false;
        $tid = $task['Task']['id'];

        if(in_array($tid, $PrintPrefs['hide_task'])){
            $hide_task = true;
        }
        
        if(in_array($tid, $PrintPrefs['hide_detail'])){
            $hide_det = true;
        }

    ?>
<?php 

            if(!$daysAreSame){
                echo '<h4 class="great">'.date('M j', strtotime($curr_t_day)).'</h4>';
            }
            
            elseif($onEday && !$hoursAreSame){
                echo '<h4 class="eday">'.date('g A', strtotime($task['Task']['start_time'])).'</h4>';
            }
            
            elseif($onEday && !$hoursAreSame && !$daysAreSame){
                echo '<h4 class="great">'.date('M j g A', strtotime($task['Task']['start_time'])).'</h4>';
            }
        
        ?>
    <div class="row">
        <div class="col-md-12">
            <div 
                data-taskid="<?php echo ($task['Task']['id']); ?>" 
                id="tid<?php echo ($task['Task']['id']); ?>" 
                class="panel task-panel" 
                style="border-left: 5px solid <?php echo ($task['Task']['task_color_code'])? $task['Task']['task_color_code'] : '#555'; ?>"
             >
                <div class="panel-heading task-panel-heading 
                <?php 
                    $vis = '';
                    if($hide_task){ $vis = 'task-panel-heading-muted';}
                    elseif(!$hide_task && $hide_det){ $vis = 'task-panel-heading-nodet';}
                    echo $vis;
                ?>" 
                data-tid="<?php echo ($task['Task']['id']); ?>">
                    <div class="row">
                        <div class="col-xs-2 col-sm-2 col-md-2 cpTimeTd">

                        
                                <?php

                                    if($daysAreSame){
                                        echo $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true, false);    
                                        }
                                    else{
                                        echo $this->Ops->durationFull($task['Task']['start_time'], $task['Task']['end_time'], true, false);
                                        
                                    }                                
                                ?>
                        </div>
                        <div class="col-xs-2 col-sm-2 col-md-2">
                            <?php
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                                echo $this->Ops->pdfSig2016($task['TasksTeam'], $zoneTeamCodeList).'</td>';
                            ?> 
                        </div>
                        <div class="col-xs-5 col-sm-5 col-md-6">
                            <?php
                                echo $task['Task']['short_description'].'<br/>';
                                
                                if (!empty($task['Task']['details'])){
                                    echo '<hr align="left" style="width: 100%; margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                    echo nl2br($task['Task']['details']);
                                }
                            ?>
                        </div>
                    <div class="col-xs-3 col-sm-3 col-md-2">
                            <div class="pull-right task-buttons" style="margin-right: 5px;">
                            <?php 
                                if (!empty($task['Task']['details'])): ?>
                                <span class="fa-stack fa-lg cpDet" data-action="details">
                                    <i class="fa fa-list-ul fa-stack-1x" 
                                    data-hide_detail = "<?php echo ($hide_det)? 1:0; ?>"
                                    data-tid="<?php echo $task['Task']['id'];?>"></i>
                                    <!--<i class="fa fa-ban fa-stack-2x text-danger"></i>-->
                                </span>
                                <?php endif;?>
                                <span class="fa-stack fa-lg cpPrint" data-action="print">
                                    <i class="fa fa-print fa-stack-1x"
                                     data-hide_task = "<?php echo ($hide_task)? 1:0; ?>"
                                     data-tid="<?php echo $task['Task']['id'];?>"></i>
                                </span>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
        $last_t_day = $curr_t_day;
    $last_t_hr = $curr_t_hr; 
    
    endforeach; ?>
        <p>
            <small>
            <?php
                echo $this->Paginator->counter(array(
                    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                ));
            ?>
            </small>
        </p>
        <ul class="pagination hidden-print">
            <?php
                echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
             echo '<span id="pSpinner" style="display: none; margin-left: 5px; vertical-align: middle; float: left;">';
                        echo $this->Html->image('ajax-loader_old.gif', array('id' => 'spinner_img', ));
                        echo '</span>';
            ?>
        </ul><!-- /.pagination -->
    <div id="pageNum" style="visibility:hidden"><?php echo $this->Paginator->param('page');?></div>
    </div><!-- /.index -->
    <?php  }
        else { //no $tasks
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
?>
</div>

<?php

    echo $this->Js->writeBuffer();
?>