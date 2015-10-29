<?php
    if (AuthComponent::user('id')) {
        $controlled_teams = AuthComponent::user('Teams');
        $controlled_tcodes = AuthComponent::user('TeamsList');
    }

    //Defaults
    $userControls = false;
    $userIsAssisting = false;
    
    $currTeamId = $task['Task']['team_id'];
    
    $ateams = array();
    //$ateams_codes = array();
    
    if (!empty($task['TasksTeam'])) {
        $ateams = Hash::extract($task['TasksTeam'], '{n}[task_role_id != 1].team_id');
    //    $ateams_codes = Hash::extract($task['TasksTeam'], '{n}[task_role_id != 1].team_code');
    }
    
    
    // Team codes & IDs, uses to populate lead team select and eval which tabs to show user
    //$aInControlled = array_intersect($controlled_tcodes, $ateams_codes);
    //$aInCurrUser = array_intersect($ateams, $controlled_teams);
    //$aInCurrUser = $aInControl;
    
    if (!empty($aInControl)) {
        $userIsAssisting = true;
    }
    
    if (in_array($currTeamId, $controlled_teams)) {
        $userControls = true;
    }
?>
<style>
    /* Tab Navigation */

/* Tab Content */
.tab-pane {
    //background: #eee;
    //border-left: 1px solid #00816C;
    //border-radius: 0;
    padding-left: 10px;
}


.tabs-left, .tabs-right {
  border-bottom: none;
  padding-top: 2px;
  //background-color: #217FFF !important;
}
.tabs-left {
  border-right-color: transparent;
}
.tabs-left>li, .tabs-right>li {
  float: none;
  margin-bottom: 2px;
}
.tabs-left>li {
  margin-right: -1px;
}
.tabs-left>li.active>a,
.tabs-left>li.active>a:hover,
.tabs-left>li.active>a:focus {
  border-bottom-color: #ddd;
  border-right-color: transparent;
}

.tabs-left>li>a {
  border-radius: 4px 0 0 4px;
  margin-right: 0;
  display:block;
  background-color: #00816C;
  color: #fff;
}
</style>
<div class="row">

<div class="col-sm-2 col-md-1" style="margin-right:0px;padding-right:0px;"> <!-- required for floating -->
    <div class="row">
        <div class="col-xs-12">
  <!-- Nav tabs -->
  <ul class="nav nav-tabs tabs-left"><!-- 'tabs-right' for right tabs -->
    <li class="text-center <?php echo (!$userControls)? 'active':''?>"><a href="#tab_view<?php echo $tid;?>" data-toggle="tab"><i class="fa fa-bookmark-o fa-lg"></i><br>Info</a></li>
    <li class="text-center"><a href="#tab_links<?php echo $tid;?>" data-toggle="tab"><i class="fa fa-chain fa-lg"></i> <br>Links</a></li>
    <li class="text-center <?php echo ($userControls)? 'active':''?>"><a href="#tab_edit<?php echo $tid;?>" data-toggle="tab"><i class="fa fa-edit fa-lg"></i> <br>Edit</a></li>
    <!--<li><a href="#settings" data-toggle="tab">Settings</a></li>-->
  </ul>            
        </div>
    </div>
</div>
<div class="col-sm-10 col-md-11" style="margin-left:0px;padding-left:0px;">
    <!-- Tab panes -->
    <div class="tab-content">
      <div class="tab-pane <?php echo (!$userControls)? 'active':''?>" id="tab_view<?php echo $tid;?>">                <?php echo $this -> element('task/tab_view', array('task' => $task)); ?>        
</div>
      <div class="tab-pane" id="tab_links<?php echo $tid;?>">                <?php echo $this->element('task/tab_links');?>
</div>
      <div class="tab-pane <?php echo ($userControls)? 'active':''?>" id="tab_edit<?php echo $tid;?>""><?php echo $this -> element('task/tab_edit', array('task' => $task)); ?></div>
      <!--<div class="tab-pane" id="settings">Settings Tab.</div>-->
    </div>
</div>
</div>
<?php
/*

<!-- tabs left -->
<div class="row">
    <div class="col-xs-12">
      <div class="tabbable tabs-left">
        <ul class="nav nav-tabs">
          <li class="<?php echo (!$userControls)? 'active':''?>"><a href="#tab_view<?php echo $tid;?>" aria-controls="view" role="tab" data-toggle="tab"><i class="fa fa-bookmark-o"></i> View</a></li>
          <li><a href="#tab_links<?php echo $tid;?>" aria-controls="links" role="tab" data-toggle="tab"><i class="fa fa-chain"></i> Links</a></li>
          <li class="<?php echo ($userControls)? 'active':''?>"><a href="#tab_edit<?php echo $tid;?>" aria-controls="edit" role="tab" data-toggle="tab"><i class="fa fa-edit"></i> Edit</a></li>
        </ul>
        <div class="tab-content">
            <div class="row">
                <div class='col-xs-12'>
            <div class="tab-pane <?php echo (!$userControls)? 'active':''?>" id="tab_view<?php echo $tid;?>">
                <?php echo $this -> element('task/tab_view', array('task' => $task)); ?>        
            </div>
            <div class="tab-pane" id="tab_links<?php echo $tid;?>">
                <?php echo $this->element('task/tab_links');?>
            </div>
            <div class="tab-pane <?php echo ($userControls)? 'active':''?>" id="tab_edit<?php echo $tid;?>">
                <?php echo $this -> element('task/tab_edit', array('task' => $task)); ?>
            </div>
                    
                </div>
            </div>
        </div>
    </div>
      <!-- /tabs -->
        
    </div>
</div>

*/ ?>

<?php echo $this->Js->writeBuffer(); ?>
