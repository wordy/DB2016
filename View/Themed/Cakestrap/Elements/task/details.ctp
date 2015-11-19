<?php
    if (AuthComponent::user('id')) {
        $controlled_teams = AuthComponent::user('Teams');
        $controlled_tcodes = AuthComponent::user('TeamsList');
    }

    //Defaults
    $userControls = false;
    $userIsAssisting = false;
    
    $currTeamId = $task['Task']['team_id'];
    $tid = $task['Task']['id'];
    
    $ateams = array();
    //$ateams_codes = array();
    
    if (!empty($task['TasksTeam'])) {
        $ateams = Hash::extract($task['TasksTeam'], '{n}.team_id');
    //    $ateams_codes = Hash::extract($task['TasksTeam'], '{n}[task_role_id != 1].team_code');
    }
    
    if (!empty($aInControl)) {
        $userIsAssisting = true;
    }
    
    if (in_array($currTeamId, $controlled_teams)) {
        $userControls = true;
    }
    
    if(isset($task['Offset'])){
        $this->request->data('Offset.sign', $task['Offset']['sign']);
        $this->request->data('Offset.minutes', $task['Offset']['minutes']);
        $this->request->data('Offset.seconds', $task['Offset']['seconds']);
        
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
    
        $('.helpTTs').popover({
            container: 'body',
            html:true,
        });
   
    ");
    
?>

<div class="row">
	<div class="col-xs-12">
		<div class="panel panel-resetMarg with-nav-tabs panel-standard">
			<div class="panel-heading" style="background-color: <?php echo $task['Task']['task_color_code'];?>; border-top-right-radius: 0px;border-top-left-radius: 0px">
				<ul class="nav nav-tabs">
				<?php
				    // What's shown at first?
				    $f_view = $f_edit = $f_link = $s_edit = $s_link= false;    
				    $s_view = true;                         // Always allow view
				    
				    if(!empty($task['Task']['parent_id']) || !empty($task['Assist'])){ $s_link = true; }
                    if($userControls){ $s_edit = true;}
				    
				    if(isset($view_first)){
				        if($view_first == 'links'){ $f_link = true; }
                        elseif($view_first == 'edit'){  $f_edit = true; }
                        elseif($view_first == 'comments' || $view_first == 'changes' || $view_first == 'actions'){  $f_view = true; }
                        else { $f_view = true; }
				    }
                    else{
                        if($userControls){  $f_edit = true; }
                        else{ $f_view = true; }
                    }
				
                    $cl_v = $cl_e = $cl_l = '';
                    if($f_view){ $cl_v = 'active';}
                    elseif($f_edit){ $cl_e = 'active';}
                    elseif($f_link){$cl_l = 'active';}
				
                    if($s_view){ echo '<li class="'.$cl_v.'"'.'><a href="#view_'.$tid.'" data-toggle="tab"><i class="fa fa-bookmark-o"></i> View</a></li>'; }
                    if($s_edit){ echo '<li class="'.$cl_e.'"'.'><a href="#edit_'.$tid.'" data-toggle="tab"><i class="fa fa-pencil"></i> Edit</a></li>';}
                    if($s_link){ echo '<li class="'.$cl_l.'"'.'><a href="#links'.$tid.'" data-toggle="tab"><i class="fa fa-link"></i> Linked Tasks</a></li>';}
                ?>
				</ul>
			</div>
			<div class="panel-body">
				<div class="tab-content">
			    <?php if($s_view): ?>
					<div class="tab-pane fade in <?php echo $cl_v;?>" id="view_<?php echo $tid ?>">
						<?php echo $this->element('task/tab_view', array('task' => $task)); ?>
					</div><!-- /tab_view-->
				<?php endif;?>
                <?php if($s_edit): ?>
                    <div class="tab-pane fade in <?php echo $cl_e;?>" id="edit_<?php echo $tid ?>">
                        <?php echo $this->element('task/tab_edit', array('task' => $task, 'linkable'=>$linkable)); ?>
                    </div><!-- /tab_edit-->
                <?php endif;?>
                <?php if($s_link): ?>
                    <div class="tab-pane fade in <?php echo $cl_l;?>" id="links<?php echo $tid ?>">
                        <?php echo $this->element('task/tab_links');?>
                    </div><!-- /tab_links-->
                <?php endif;?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $this->Js->writeBuffer(); ?>
