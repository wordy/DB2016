<?php
//$this->log($task);
    if (AuthComponent::user('id')) {
        $controlled_teams = AuthComponent::user('Teams');
        $controlled_tcodes = AuthComponent::user('TeamsList');
    }

    //Defaults
    $currTeamId = $task['Task']['team_id'];
    $tid = $task['Task']['id'];
    
    $ateams = (!empty($task['TasksTeam']))? Hash::extract($task['TasksTeam'], '{n}.team_id'): array();
    $userIsAssisting = (!empty($aInControl))? TRUE: FALSE;
    $userControls = (in_array($currTeamId, $controlled_teams)) ? TRUE : FALSE;
    
    $jqCIN = array();
    //$this->log($aInControl);
    if(isset($aInControl)){
        foreach($aInControl as $zone){
            foreach($zone as $k => $tcode){
                $jqCIN[$k] = $tcode;
            }
        }
    }
    
    
    $show_view = $show_links = $show_comments = $show_actions = $show_changes = TRUE;
    $show_edit = ($userControls)? TRUE: FALSE;
    
    $act_actions = $act_view = $act_links = $act_comments = $act_changes = $act_edit = '';
    
    if($userControls){
        $act_edit = 'active';
    }else{ $act_actions = 'active';}
    
        
    
    if(isset($task['Offset'])){
        $this->request->data('Offset.type', $task['Task']['time_offset_type']);
        $this->request->data('Offset.minutes', $task['Offset']['minutes']);
        //$this->request->data('Offset.seconds', $task['Offset']['seconds']);
    }
    
    // Checks if team color is light or not. Changes text accordingly    
    $htmlCode = $task['Task']['task_color_code'];
    $icl = $this->Ops->isColorLight($htmlCode);
    if($icl){
        $this->Js->buffer("
            $('#task_detail_".$tid."').find('.with-nav-tabs.panel-standard .nav-tabs > li > a').addClass('dark');
        ");    
    }
    
    $this->Js->buffer("
    
        //var jCIN = ".json_encode($jqCIN).";

   
    ");
?>

<style>
    .nav-tabs { border-bottom: 2px solid #DDD; }
    .nav-tabs > li.active > a, .nav-tabs > li.active > a:focus, .nav-tabs > li.active > a:hover { border-width: 0; }
    .nav-tabs > li > a { border: none; color: #666; }
        .nav-tabs > li.active > a, .nav-tabs > li > a:hover { border: none; color: #00816C !important; background: transparent; }
        .nav-tabs > li > a::after { content: ""; background: #00816C; height: 2px; position: absolute; width: 100%; left: 0px; bottom: -1px; transition: all 250ms ease 0s; transform: scale(0); }
    .nav-tabs > li.active > a::after, .nav-tabs > li:hover > a::after { transform: scale(1); }
    .tab-nav > li > a::after { background: #00816C none repeat scroll 0% 0%; color: #fff; }
    .tab-pane { padding: 15px 0; }
    .tab-content{padding:10px}
</style>

<div class="container33333333">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" <?php echo (!empty($act_actions))? 'class="'.$act_actions.'"':'';?>><a href="#actionsTab<?php echo $tid?>" aria-controls="actions<?php echo $tid?>" role="tab" data-toggle="tab"><i class="fa fa-flash"></i> Actions</a></li>
                    <?php if($userControls):?>
                        <li role="presentation" <?php echo (!empty($act_edit))? 'class="'.$act_edit.'"':'';?>><a href="#editTab<?php echo $tid?>" aria-controls="edit<?php echo $tid?>" role="tab" data-toggle="tab"><i class="fa fa-pencil"></i> Edit</a></li>
                    <?php endif;?>
                    <li role="presentation"><a href="#changesTab<?php echo $tid?>" aria-controls="changes<?php echo $tid?>" role="tab" data-toggle="tab"><i class="fa fa-exchange"></i> Changes</a></li>
                    <li role="presentation"><a href="#linksTab<?php echo $tid?>" aria-controls="links<?php echo $tid?>" role="tab" data-toggle="tab"><i class="fa fa-link"></i> Linked Tasks</a></li>
                    <li role="presentation"><a href="#commentsTab<?php echo $tid?>" aria-controls="comments<?php echo $tid?>" role="tab" data-toggle="tab"><i class="fa fa-comment"></i> Comments</a></li>
                </ul>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane highlight2 <?php echo $act_actions?>" id="actionsTab<?php echo $tid?>">
                        <div class="col-xs-12">
                        <?php echo $this->element('task/tab_actions', array('task' => $task)); ?>
                        </div>
                        
                    </div>
                    
                    <?php if($userControls):?>
                        <div role="tabpanel" class="tab-pane <?php echo $act_edit?>" id="editTab<?php echo $tid?>">
                            <div class="col-xs-12">
                            <?php echo $this->element('task/tab_edit2', array('task' => $task, 'linkable'=>$linkable)); ?>
                            </div>
                        </div>
                    <?php endif;?>
                    
                    <div role="tabpanel" class="tab-pane <?php echo $act_changes?>" id="changesTab<?php echo $tid?>">
                        <div class="col-xs-12">
                            <div class="row" id="loaded_chg_<?php echo $tid ?>">
                                <?php echo $this->element('change/changes_by_task', array('task'=>$tid, 'userControls'=> $userControls)); ?>
                            </div>
                        </div>                    
                    </div>
                    
                    <div role="tabpanel" class="tab-pane <?php echo $act_links?>" id="linksTab<?php echo $tid?>">
                        <?php echo $this->element('task/tab_links');?>
                    </div>
                    
                    <div role="tabpanel" class="tab-pane <?php echo $act_comments?>" id="commentsTab<?php echo $tid?>">
                        <div id="commentsByTask<?php echo $tid;?>" class="highlight">
                            <?php echo $this->element('comment/by_task', array('tid'=>$tid, 'team'=>$task['Task']['team_id'])); ?> 
                        </div>  
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
