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
    /***
Bootstrap Line Tabs by @keenthemes
A component of Metronic Theme - #1 Selling Bootstrap 3 Admin Theme in Themeforest: http://j.mp/metronictheme
Licensed under MIT
***/

/* Tabs panel */
.tabbable-panel {
  border:1px solid #eee;
  padding: 10px;
}

/* Default mode */
.tabbable-line > .nav-tabs {
  border: none;
  margin: 0px;
}
.tabbable-line > .nav-tabs > li {
  margin-right: 2px;
}
.tabbable-line > .nav-tabs > li > a {
  border: 0;
  margin-right: 0;
  color: #737373;
}
.tabbable-line > .nav-tabs > li > a > i {
  color: #a6a6a6;
}
.tabbable-line > .nav-tabs > li.open, .tabbable-line > .nav-tabs > li:hover {
  border-bottom: 4px solid #fbcdcf;
}
.tabbable-line > .nav-tabs > li.open > a, .tabbable-line > .nav-tabs > li:hover > a {
  border: 0;
  background: none !important;
  color: #333333;
}
.tabbable-line > .nav-tabs > li.open > a > i, .tabbable-line > .nav-tabs > li:hover > a > i {
  color: #a6a6a6;
}
.tabbable-line > .nav-tabs > li.open .dropdown-menu, .tabbable-line > .nav-tabs > li:hover .dropdown-menu {
  margin-top: 0px;
}
.tabbable-line > .nav-tabs > li.active {
  border-bottom: 4px solid #f3565d;
  position: relative;
}
.tabbable-line > .nav-tabs > li.active > a {
  border: 0;
  color: #333333;
}
.tabbable-line > .nav-tabs > li.active > a > i {
  color: #404040;
}
.tabbable-line > .tab-content {
  margin-top: -3px;
  background-color: #fff;
  border: 0;
  border-top: 1px solid #eee;
  padding: 15px 0;
}
.portlet .tabbable-line > .tab-content {
  padding-bottom: 0;
}
</style>

        <div class="row">
<div class="tabbable-panel">
                <div class="tabbable-line">
                    <ul class="nav nav-tabs ">
                        <li class="active">
                            <a href="#tab_default_1" data-toggle="tab">
                            Tab 1 </a>
                        </li>
                        <li>
                            <a href="#tab_default_2" data-toggle="tab">
                            Tab 2 </a>
                        </li>
                        <li>
                            <a href="#tab_default_3" data-toggle="tab">
                            Tab 3 </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_default_1">
                            <p>
                                I'm in Tab 1.
                            </p>
                            <p>
                                Duis autem eum iriure dolor in hendrerit in vulputate velit esse molestie consequat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat.
                            </p>
                            <p>
                                <a class="btn btn-success" href="http://j.mp/metronictheme" target="_blank">
                                    Learn more...
                                </a>
                            </p>
                        </div>
                        <div class="tab-pane" id="tab_default_2">
                            <p>
                                Howdy, I'm in Tab 2.
                            </p>
                            <p>
                                Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat. Ut wisi enim ad minim veniam, quis nostrud exerci tation.
                            </p>
                            <p>
                                <a class="btn btn-warning" href="http://j.mp/metronictheme" target="_blank">
                                    Click for more features...
                                </a>
                            </p>
                        </div>
                        <div class="tab-pane" id="tab_default_3">
                            <p>
                                Howdy, I'm in Tab 3.
                            </p>
                            <p>
                                Duis autem vel eum iriure dolor in hendrerit in vulputate. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat
                            </p>
                            <p>
                                <a class="btn btn-info" href="http://j.mp/metronictheme" target="_blank">
                                    Learn more...
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
</div>




<?php echo $this->Js->writeBuffer(); ?>
