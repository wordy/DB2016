<?php 
    //$this->extend('/Common/Task/view_task');
    $this->assign('page_title', 'Task ');
    $this->assign('page_title_sub', 'Details'); 
    
    $ztlist= array();
    foreach ($teams as $zone => $tids){
        $ztlist[$zone] = array_keys($tids);
    }
  
    if(!empty($task['Task']['task_color_code'])){
        $tcol = $task['Task']['task_color_code'];
        $this->Js->buffer("
            $('.panel-taskcolored').css('border-color','#ccc');
            $('.panel-taskcolored > .panel-heading').css({'color':'".$tcol."', 'background-color':'".$tcol."','border-color':'#ccc'});
            $('.panel-taskcolored > .panel-heading').css({'color':'#000', 'background-color':'".$tcol."','border-color':'#ccc'});
            $('.panel-taskcolored > .panel-heading + .panel-collapse .panel-body').css('border-top-color','#ccc');
            $('.panel-taskcolored > .panel-footer + .panel-collapse .panel-body').css('border-bottom-color','#ccc');
        ");
    }
    
    $tt_l_but = '';
    $tt_p_but = '';
    $tt_a_but = '';

    if(!empty($task['TasksTeam'])){
        //$tt = $task['TasksTeam'];
        
        /*
        $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
        $tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_code');
        $tt_a = Hash::extract($tt, '{n}[task_role_id=3].team_code');
        $tt_p_not_a = array_diff($tt_p, $tt_a);
    
        foreach ($tt_l as $k => $ttlcode) {
            $tt_l_but.= '<span class="btn btn-leadt">'.$ttlcode.'</span>';
        }    
            
        foreach ($tt_p_not_a as $k=>$ttpcode) {
            $tt_p_but.= '<span class="btn btn-default btn-xxs">'.$ttpcode.'</span>';
        }

        foreach ($tt_a as $k=>$ttacode) {
            $tt_a_but.= '<span class="btn btn-danger btn-xxs">'.$ttacode.'</span>';
        }*/
        
        
    }
    
    //Check if dates are on the same day (used to format task time range)
    $ends_same_day = false;
    $sday = date('Y-m-d', strtotime($task['Task']['start_time']));
    $eday = date('Y-m-d', strtotime($task['Task']['end_time']));
    
    if(empty($task['Task']['end_time'])){ $ends_same_day = true; }
    
    if($sday == $eday){ $ends_same_day = true; }
   
  
?>

    <div class="panel panel-success panel-taskcolored">
        <div class="panel-heading"><b>Task Details</b></div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-3">
                            <h5><b>Time</b></h5>
                            <?php        
                                //echo '<h5><b>Start</b></h5>';
                                if(!empty($task['Task']['start_time'])){
                                    echo date('M j h:i \- ', strtotime($task['Task']['start_time']));    
                                }
                                if($ends_same_day){
                                    echo date('h:i', strtotime($task['Task']['end_time']));
                                }
                                else{
                                    echo date('M j h:i', strtotime($task['Task']['end_time']));
                                }
                            ?>
                        </div><!--col-->
                
                        <div class="col-md-9">
                            <?php
                                    
                                echo '<h5><b>Short Description</b></h5>';
                                
                                if(!empty($task['Task']['task_type'])){
                                    echo '<b>('.strtoupper($task['Task']['task_type']) . ')</b> - ';}
                                echo $task['Task']['short_description']; ?>    
                        </div><!--col-->
                    </div><!--row-->

                    <div class="row">
                        <div class="col-md-12">
                            
                        </div>
                    </div>
                            
                    <div class="row">
                        <div class="col-md-12">
                            
                            <?php     
                                   
                                echo '<h5><b>Details</b></h5>';
                                if(!empty($task['Task']['details'])){
                                    echo nl2br($task['Task']['details']);    
                                }
                                else { echo '-';} 
                                
                                 ?>
                        </div>
                    </div>
                </div><!--col-->

                <div class="col-md-4">
                    <div class="row"><!-- Right Sidebar-->
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading"><b>Teams</b></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            
                                            if(!empty($task['TasksTeam'])){
                                                    
                                                echo $this->Ops->makeTeamsSig2015($task['TasksTeam'], $ztlist);
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div><!--team panel body-->
                            </div><!--team panel-->
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-danger">
                                <div class="panel-heading"><b>Dates &amp; Statuses</b></div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?php
                                                echo '<h5><b>Due Date</b></h5>';
                                                
                                                if (!empty($task['Task']['due_date'])){
                                                    echo $task['Task']['due_date'];
                                                }
                                                else {echo 'No due date set.';}
                                            ?> 
                                        </div>
                                        <div class="col-md-6">
                                            <?php
                                                echo '<h5><b>Action Item Status</b></h5>';
                                                if (!empty($task['Task']['actionable_type'])){
                                                echo $task['Task']['actionable_type'];    
                                            }
                                                else {echo 'Not actionable';}
                                            ?> 
                                        </div>
                                    </div>

                                </div><!--status body-->
                            </div><!--status panel-->
                        </div>
                    </div><!--row-->
                </div>
            </div><!--row-->
        </div>
    </div>


<?php //echo $this->Js->writeBuffer(); ?>