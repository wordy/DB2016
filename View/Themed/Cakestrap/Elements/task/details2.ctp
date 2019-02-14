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
    /*
        $('.helpTTs').popover({
            container: 'body',
            html:true,
        });*/
   
    ");
?>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-resetMarg with-nav-tabs panel-standard">
			<div class="panel-heading" style="background-color: <?php echo $task['Task']['task_color_code'];?>; border-top-right-radius: 0px;border-top-left-radius: 0px">
				<ul class="nav nav-tabs">
				<?php
				    // What's shown at first?
				    $f_actions = $f_edit = $f_link = $s_edit = $s_link = false;
                    $s_comments = $s_changes = true;
				    $s_actions = true;                         // Always allow view
				    
				    if(!empty($task['Task']['parent_id']) || !empty($task['Assist'])){ $s_link = true; }
                    if($userControls){ $s_edit = true;}
				    
				    if(isset($view_first)){
				        if($view_first == 'links'){ $f_link = true; }
                        elseif($view_first == 'edit'){  $f_edit = true; }
                        elseif($view_first == 'comments' || $view_first == 'changes' || $view_first == 'actions'){  $f_actions = true; }
                        else { $f_actions = true; }
				    }
                    else{
                        if($userControls){  $f_edit = true; }
                        else{ $f_actions = true; }
                    }
				
                    $cl_actions = $cl_edit = $cl_links = $cl_chg = $cl_com = '';
                    if($f_actions){ $cl_actions = 'active';}
                    elseif($f_edit){ $cl_edit = 'active';}
                    elseif($f_link){$cl_links = 'active';}
				
                    if($s_actions){ echo '<li class="'.$cl_actions.'"'.'><a href="#actionsTab'.$tid.'" data-toggle="tab"><i class="fa fa-flash"></i> Actions</a></li>'; }
                    if($s_edit){ echo '<li class="'.$cl_edit.'"'.'><a href="#editTab'.$tid.'" data-toggle="tab"><i class="fa fa-pencil"></i> Edit</a></li>';}
                    if($s_changes){ echo '<li class="'.$cl_chg.'"'.'><a href="#changesTab'.$tid.'" data-toggle="tab"><i class="fa fa-exchange"></i> Changes</a></li>';}                   
                    if($s_link){ echo '<li class="'.$cl_links.'"'.'><a href="#linksTab'.$tid.'" data-toggle="tab"><i class="fa fa-link"></i> Linked Tasks</a></li>';}
                    if($s_comments){ echo '<li class="'.$cl_com.'"'.'><a href="#commentsTab'.$tid.'" data-toggle="tab"><i class="fa fa-comment"></i> Comments</a></li>';}
                
                    
                
                ?>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
			    <?php if($s_actions): ?>
					<div class="tab-pane fade in <?php echo $cl_actions;?>" id="actionsTab<?php echo $tid ?>">
						<?php echo $this->element('task/tab_actions', array('task' => $task)); ?>
					</div><!-- /tab_view-->
				<?php endif;?>
                <?php if($s_edit): ?>
                    <div class="tab-pane fade in <?php echo $cl_edit;?>" id="editTab<?php echo $tid ?>">
                        <?php echo $this->element('task/tab_edit2', array('task' => $task, 'linkable'=>$linkable)); ?>
                    </div><!-- /tab_edit-->
                <?php endif;?>
                <?php if($s_link): ?>
                    <div class="tab-pane fade in <?php echo $cl_links;?>" id="linksTab<?php echo $tid ?>">
                        <?php echo $this->element('task/tab_links');?>
                    </div><!-- /tab_links-->
                <?php endif;?>
                <?php if($s_changes): ?>
                    <div class="tab-pane fade in <?php echo $cl_chg;?>" id="changesTab<?php echo $tid ?>"> 
                        <h3><i class="fa fa-exchange"></i> Recent Changes</h3>
                        <div class="row" id="loaded_chg_<?php echo $tid ?>">
                            <?php echo $this->element('change/changes_by_task', array('task'=>$tid, 'userControls'=> $userControls)); ?>
                        </div>                        
                    </div><!-- /tab_links-->
                <?php endif;?>
                <?php if($s_comments): ?>
                    <div class="tab-pane fade in <?php echo $cl_com;?>" id="commentsTab<?php echo $tid ?>">
                        <div class="row">
                            <div class="col-xs-12">
                                <div id="commentsByTask<?php echo $tid;?>">
                                    <?php echo $this->element('comment/by_task', array('tid'=>$tid, 'team'=>$task['Task']['team_id'])); ?> 
                                </div>                    
                            </div>
                        </div>
                    </div><!-- /tab_links-->
                <?php endif;?>                



				</div>
			</div>
		</div>
	</div>
</div>

<div class="container">
    <div class="row">
                                        <div class="col-md-6">
                                    <!-- Nav tabs --><div class="card">
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
                                        <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
                                        <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
                                        <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content">
                                        <div role="tabpanel" class="tab-pane active" id="home">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</div>
                                        <div role="tabpanel" class="tab-pane" id="profile">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</div>
                                        <div role="tabpanel" class="tab-pane" id="messages">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</div>
                                        <div role="tabpanel" class="tab-pane" id="settings">Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passage..</div>
                                    </div>
</div>
                                </div>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
