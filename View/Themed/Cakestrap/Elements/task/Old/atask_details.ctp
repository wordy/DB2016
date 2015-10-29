    <?php
        if (AuthComponent::user('id')){
            $controlled_teams = AuthComponent::user('Teams');
            $controlled_tcodes = AuthComponent::user('TeamsList');
        }
        

$this->Js->buffer("

    $('#viewTaskNav').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array(
                'controller'=>'tasks', 
                'action'=>'view', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#taskDetCont').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
      });
      return false;
    });
    
    /*
    $('#view_nav_attachments').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'attachments', 'action'=>'pageAttachments', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#taskDetCont').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                 $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
        });
      return false;
    });
    */

    $('#view_nav_changes').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'changes', 'action'=>'pageChanges', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#taskDetCont').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'post',
            dataType:'html',
          });
          return false;
      });
/*
    $('#view_nav_edit').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'tasks', 'action'=>'edit', $task['Task']['id']))."',
            beforeSend:function () {
                $('#ajax-menu-spinner').fadeIn();},
            success:function(data, textStatus) {
                $('#taskDetCont').html(data);
            },
            complete:function (XMLHttpRequest, textStatus) {
                $('#ajax-menu-spinner').fadeOut();}, 
            type: 'get',
            dataType:'html',
          });
          return false;
      });
*/
");


 
        
        
        
        
        
        
        
        //Defaults
        $userControls = false;
        $userIsAssisting = false;
        
        $currTeamId = $task_det['Task']['team_id'];
        
        // Teams that are listed as assisting
        // TODO: NON-HARD CODED ROLES
        $ateams = Hash::extract($task_det['TasksTeam'], '{n}[task_role_id = 3].team_id');
        $ateams_codes = Hash::extract($task_det['TasksTeam'], '{n}[task_role_id = 3].team_code');
        
        // Team codes & IDs, uses to populate lead team select and eval which tabs to show user
        $aInControlled = array_intersect($controlled_tcodes, $ateams_codes);
        $aInCurrUser = array_intersect($ateams, $controlled_teams);

        if(!empty($aInCurrUser)){
            $userIsAssisting = true;
        }
        
        if(in_array($currTeamId, $controlled_teams)){
            $userControls = true; 
        }
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel with-nav-tabs panel-yh">
                <div class="panel-heading">
                    <ul class="nav nav-tabs">
                    <?php
                        // Default view to edit if you control it. 
                        // Changes is default for ones you don't control
                        // Or Respond is default if it's available given your teams 
                        if ($userControls){
                            echo '<li class="active" id="viewTaskNav"><a href="#add_to'.$tid.'" data-toggle="tab"><i class="fa fa-edit"></i> Edit Task</a></li>';
                        }
                        if ($userIsAssisting){
                            echo '<li';
                            echo (!$userControls)? ' class="active"':"";
                            echo '><a href="#add_to'.$tid.'" data-toggle="tab"><i class="fa fa-reply"></i> Respond To Request</a></li>';
                        }

                        
                    ?>
                        <li <?php echo (!$userControls && !$userIsAssisting)? 'class="active"':''?>><a href="#changes_<?php echo $tid ?>" data-toggle="tab"><i class="fa fa-exchange"></i> Changes</a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">
                        <div id="taskDetCont" class="tab-pane fade <?php echo ($userControls)? 'in active':''?>" id="edit_<?php echo $tid ?>">
                            <?php echo $this->element('task/edit_ajax', array('task'=>$task_det)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php echo $this->Js->writeBuffer(); ?>
