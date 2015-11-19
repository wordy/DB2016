<?php
    //  $urid: TL=10; GM=100; Chair=200; Admin=500
    //  Admins get all options (including scary ones)
     
    $userTeamsList = AuthComponent::user('TeamsList');
    $urid = $this->Session->read('Auth.User.user_role_id');
    $uid = $this->Session->read('Auth.User.id');
    $main_team_code = $this->Session->read('Auth.User.Settings.main_team_code')?:'';
?>
<!--navbar-fixed-top-->
<nav class="navbar navbar-fixed-top navbar-inverse " style="padding-right:10px" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand hidden-sm" href="/"><?php echo Configure::read('EventShortName')?> Ops</a>
	</div><!-- /.navbar-header -->
	
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
		    <li><?php 
                    $main_link = ($main_team_code)? $main_team_code : ' Home';
    		        echo $this->Html->link(__('<i class="fa fa-home"></i> '.$main_link), array('controller'=>'teams', 'action'=>'home', $main_team_code),array('escape'=>false));
                ?>
            </li>
            <?php if($urid>=100):?>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user-secret"></i> Team <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><a href="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'changeTeam', 0))?>"><b>Default</b></a></li>
                <?php foreach($userTeamsList as $tid => $tcode):?>
                    <li><a href="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'changeTeam', $tid))?>"><?php echo $tcode?></a></li>
                <?php endforeach; ?>
                </ul>
            </li>
            <?php endif; ?>
            <li><?php echo $this->Html->link(__('<i class="fa fa-gears"></i> Tasks'), array('controller'=>'tasks', 'action'=>'compile'), array('escape'=>false));?></li>
            <li><a href="/tasks/timeShift/"><i class="fa fa-clock-o"></i> Shift <span id="userTimeshiftCount" class="label label-success label-as-badge"><?php echo count($this->Session->read('Auth.User.Timeshift')); ?></span></a></li>
            <li><?php echo $this->Html->link(__('<i class="fa fa-print"></i> Print'), array('controller'=>'tasks', 'action'=>'userPrint'), array('escape'=>false));?></li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info-circle"></i> Info <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this->Html->link(__('<i class="fa fa-files-o"></i> Files (Google Drive)'), 'https://drive.google.com', array('target'=>'_blank','escape'=>false));?></li>
                    <li><?php echo $this->Html->link(__('<i class="fa fa-diamond"></i> Event Info'), array('controller' => 'event_infos', 'action' => 'info'), array('class' => '','escape'=>false,));?></li>
                    <li><?php echo $this->Html->link(__('<i class="fa fa-sitemap"></i> Organizational Chart'), array('controller' => 'users', 'action' => 'orgChart'), array('escape'=>false));?></li>
                </ul>
            </li>

            <?php if($urid>=200): ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lock"></i> Admin <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <?php if($urid >=200):?>
                    <li class="dropdown-submenu"><a tabindex="-1" href="#">Users</a>
                        <ul class="dropdown-menu">
                            <?php if($urid >=200):?>
                                <li><?php echo $this->Html->link(__('<i class="fa fa-user-plus"></i> New User'), array('controller' => 'users', 'action' => 'add'), array('escape'=>false));?></li>
                            <?php endif;?>
                            <?php if($urid >=500):?>
                            <li><?php echo $this->Html->link(__('<i class="fa fa-list-ul"></i> List Users'), array('controller' => 'users', 'action' => 'index'), array('escape'=>false));?></li>
                            <?php endif;?>
                        </ul>

                    </li>
                <?php endif; ?>
                <?php if($urid >=500): ?>
                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Add New</a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('New Team'), array('controller' => 'teams', 'action' => 'add'), array('class' => '')); ?></li> 
                            <li><?php echo $this->Html->link(__('New Task Type'), array('controller' => 'task_types', 'action' => 'add'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('New Actionable Type'), array('controller' => 'actionable_types', 'action' => 'add'), array('class' => '')); ?></li> 
                            <li><?php echo $this->Html->link(__('New Task Colour'), array('controller' => 'task_colors', 'action' => 'add'), array('class' => '')); ?></li> 
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Tasks &amp; Teams</a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('List Teams'), array('controller' => 'teams', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Zones'), array('controller' => 'zones', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Task Types'), array('controller' => 'task_types', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Actionable Types'), array('controller' => 'actionable_types', 'action' => 'index'), array('class' => '')); ?></li> 
                            <li><?php echo $this->Html->link(__('List Task Colours'), array('controller' => 'task_colors', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Comments'), array('controller' => 'comments', 'action' => 'index'), array('class' => '')); ?></li>
                        </ul>
                    </li>
                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Advanced</a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('<i class="fa fa-newspaper-o"></i> Manage Digest'), array('controller' => 'tasks', 'action' => 'manageDigest'), array('escape'=>false));?></li>
                            <li><?php echo $this->Html->link(__('New Change'), array('controller' => 'changes', 'action' => 'add'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('Assign Team to Task'), array('controller' => 'tasks_teams', 'action' => 'add'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Team Assignments'), array('controller' => 'tasks_teams', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Changes'), array('controller' => 'changes', 'action' => 'index'), array('class' => '')); ?></li> 
                        </ul>
                    </li>
                <?php endif; ?>
                </ul>
            </li><!-- End Admin -->
            <?php endif; ?>
            
            <li class="dropdown hidden-lg">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search"></i> Search</a> 
                <ul class="dropdown-menu" style="min-width: 350px" role="menu">
                    <li><?php 
                        echo $this->Form->create('Search', array('url'=>array('controller'=>'tasks', 'action'=>'search'), 'inputDefaults' => array('label' => false), 'role' => 'search', 'class'=> 'navbar-form navbar-left')); 
                        echo $this->Form->input('term', array(
                            'class' => 'form-control',
                            'type'=>'text',
                            'size'=>45,
                            'div'=>array(
                                'class'=>'input-group'),
                            'after'=>'<span class="input-group-btn">
                                <button class="btn btn-yh" type="submit"><i class="fa fa-search"></i></button></span>',
                            'placeholder'=>'Search', 
                            'escape'=>false));
                        ?>
                        <p class="help-block">Searches within task <b>descriptions</b> and <b>details</b>. Also supports searching by tags (e.g. #GOP)</p>
                        <?php echo $this->Form->end(); ?>
                    </li>
                </ul>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-lg"></i>&nbsp; <?php echo AuthComponent::user('handle');?>  <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this->Html->link(__('<i class="fa fa-user"></i> Profile'), array('controller'=>'users', 'action'=>'profile', AuthComponent::user('id')), array('escape'=>false));?></li>
                    <li><?php echo $this->Html->link(__('<i class="fa fa-gear"></i> Preferences'), array('controller'=>'users', 'action'=>'prefs', AuthComponent::user('id')), array('escape'=>false));?></li>
                    <li class="divider"></li>                       
                    <li><?php echo $this->Html->link(__('<i class="fa fa-power-off"></i> Log Out'), array('controller'=>'users', 'action'=>'logout'), array('escape'=>false));?></li>
                </ul>
            </li>
        </ul>
        <div class="visible-lg-block">
            <?php 
                echo $this->Form->create('Search', array(
                    'url'=>array(
                        'controller'=>'tasks',
                        'action'=>'search'),
                    'inputDefaults' => array(
                        'label' => false), 
                    'role' => 'search',
                    'class'=> 'navbar-form navbar-right')); 
                    
                echo $this->Form->input('term', array(
                    'class' => 'form-control',
                    'type'=>'text',
                    'size'=>15,
                    'div'=>array(
                        'class'=>'input-group'),
                    'after'=>'<span class="input-group-btn">
                        <button class="btn btn-default" type="submit"><i class="fa fa-search"></i></button></span>',
                    'placeholder'=>'Search', 
                    'escape'=>false)); 
            ?>
            <?php echo $this->Form->end(); ?>
        </div>
	</div>
</nav><!-- /navbar -->

    
