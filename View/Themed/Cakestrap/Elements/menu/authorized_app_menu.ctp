<?php
    //  $urid: TL=10; GM=100; Chair=200; Admin=500
    $userTeamsList = AuthComponent::user('TeamsList');
    $urid = $this->Session->read('Auth.User.user_role_id');
    $uid = $this->Session->read('Auth.User.id');
    $main_team_code = $this->Session->read('Auth.User.Settings.main_team_code')?:'';
    $user_ts_count = count($this->Session->read('Auth.User.Timeshift'));
    $timeshift_mode = $this->Session->read('Auth.User.Timeshift.Mode');
    $actionName = $this->request->params['action'];
    
    $this->Js->buffer("
        $('.searchSubmit').on('click', function(){
            inVal = $(this).closest('form').find('.searchTermEntry').val();
            inValLen = inVal.length;
            
            if(inValLen < 3){
                var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Minimum Search Length: </b>The minimum search length is <b>3</b> characters. Please choose a different search term.</div>';
                $('#topFlashContent').stop().html(msg).fadeIn('fast').delay(5000).fadeOut('fast');
                return false;
            }
        });
    
        $('#userTimeshiftPref').on('click', function(){
            prefbut = $(this);
            prefbut_span = prefbut.find('span');
            if(prefbut.hasClass('tsOn')){
                tsid = 0;
                prefbut.removeClass('tsOn').addClass('tsOff');
                prefbut_span.text('OFF').removeClass('label-success').addClass('label-danger');
            }
            else{
                tsid = 1;
                prefbut.removeClass('tsOff').addClass('tsOn');
                prefbut_span.text('ON').removeClass('label-danger').addClass('label-success');
            }
            
            if(tsid == 1){
                $('.task-buttons').each(function(){
                    if($(this).hasClass('canCollapse')){
                        $(this).hide();    
                    }
                });
                $('.task-timeShift').removeClass('hide').fadeIn('fast');    
            }
            else{
                $('.task-buttons').removeClass('hide').fadeIn('fast');
                $('.task-timeShift').hide();
            }
        
            $.ajax({
                url: '/users/setTimeshiftPref/'+tsid,
                type: 'post',
            });
            return false;
        });
    ");
    
    
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
		
        <a class="navbar-brand" href="/">
            <span id="global-busy-indicator" style="display:none;"><i class="fa fa-cog fa-spin fa-lg"></i></span>
            <span class="hidden-xs">OPS</span>
        </a>    
	</div><!-- /.navbar-header -->
	
    <div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
		    <li>
		        <?php 
                    $main_link = ($main_team_code)? $main_team_code : ' Home';
    		        echo $this->Html->link(__('<i class="fa fa-home fa-lg"></i> <span class="hidden-sm">'.$main_link."</span>"), array('controller'=>'teams', 'action'=>'home', $main_team_code),array('escape'=>false));
                ?>
            </li>

            <?php if($urid>=100):?>
                <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-retweet fa-lg"></i><span class="hidden-sm"> Team </span><b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'changeTeam', 0))?>"><b>Default</b></a></li>
                        <li class="divider"></li>
                    <?php foreach($userTeamsList as $tid => $tcode):?>
                        <li><a href="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'changeTeam', $tid))?>"><?php echo $tcode?></a></li>
                    <?php endforeach; ?>
                    </ul>
                </li>
            <?php endif; ?>
    
            <li><?php echo $this->Html->link(__('<i class="fa fa-gears fa-lg"></i> <span class="hidden-sm">Compile</span>'), array('controller'=>'tasks', 'action'=>'compile'), array('escape'=>false));?></li>

            <?php if($actionName == 'compile'): ?>
                <li id="userTimeshiftPref" class="<?php echo ($timeshift_mode)?'tsOn':'tsOff';?>"><a href="#"><i class="fa fa-arrows-v fa-lg"></i> Shift <span id="userTimeshiftMode" class="label <?php echo ($timeshift_mode)?'label-success':'label-danger';?> label-as-badge"><?php echo ($timeshift_mode)? 'ON' : 'OFF';?></span></a></li>-->
            <?php endif; ?>

            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-magic fa-lg"></i><span class="hidden-sm"> Export</span><b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this->Html->link(__('<i class="fa fa-print fa-lg"></i> Customized Plan'), array('controller'=>'tasks', 'action'=>'userPrint'), array('escape'=>false));?></li>
                    <li><?php echo $this->Html->link(__('<i class="fa fa-vcard-o fa-lg"></i> Assigned Tasks By Role'), array('controller'=>'tasks', 'action'=>'byRole'), array('escape'=>false));?></li>
                </ul>
            </li>

            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-info-circle fa-lg"></i><span class="hidden-sm"> Info </span><b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this->Html->link(__('<i class="fa fa-diamond"></i> Event Info'), array('controller' => 'event_infos', 'action' => 'info'), array('escape'=>false,));?></li>
                    <li><?php echo $this->Html->link(__('<i class="fa fa-sitemap"></i> Organizational Chart'), array('controller' => 'users', 'action' => 'orgChart'), array('escape'=>false));?></li>
                    <li><?php echo $this->Html->link(__('<i class="fa fa-files-o"></i> Files (Google Drive)'), 'https://drive.google.com/drive/folders/0B2G1CiYORu1UeDhGcktEbjA3ODA?ths=true', array('target'=>'_blank','escape'=>false));?></li>
                </ul>
            </li>

            <?php if($urid>=10): ?>
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lock fa-lg"></i><span class="hidden-sm"> Admin</span> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this->Html->link(__('<i class="fa fa-id-badge"></i> Manage Team Roles'), array('controller' => 'roles', 'action' => 'manage'), array('class' => '','escape'=>false,));?></li>
                    
                    <?php if($urid >=200):?>
                        <li class="divider"></li>
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
                                <li><?php echo $this->Html->link(__('New Role'), array('controller' => 'roles', 'action' => 'add'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('New Assignment'), array('controller' => 'assignments', 'action' => 'add'), array('class' => '')); ?></li>
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="#">Tasks &amp; Teams</a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__('List Teams'), array('controller' => 'teams', 'action' => 'index'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('List Roles'), array('controller' => 'roles', 'action' => 'index'), array('class' => '')); ?></li> 
                                <li><?php echo $this->Html->link(__('List Zones'), array('controller' => 'zones', 'action' => 'index'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('List Comments'), array('controller' => 'comments', 'action' => 'index'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('List Changes'), array('controller' => 'changes', 'action' => 'index'), array('class' => '')); ?></li> 

                                <li class="divider"></li>
                                <li><?php echo $this->Html->link(__('List Task Types'), array('controller' => 'task_types', 'action' => 'index'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('List Task Colours'), array('controller' => 'task_colors', 'action' => 'index'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('List Actionable Types'), array('controller' => 'actionable_types', 'action' => 'index'), array('class' => '')); ?></li> 
                            </ul>
                        </li>
                        <li class="dropdown-submenu">
                            <a tabindex="-1" href="#">Advanced</a>
                            <ul class="dropdown-menu">
                                <li><?php echo $this->Html->link(__('New Task Type'), array('controller' => 'task_types', 'action' => 'add'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('New Task Colour'), array('controller' => 'task_colors', 'action' => 'add'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('New Actionable Type'), array('controller' => 'actionable_types', 'action' => 'add'), array('class' => '')); ?></li> 
                                <li><?php echo $this->Html->link(__('New Change'), array('controller' => 'changes', 'action' => 'add'), array('class' => '')); ?></li>
                                <li><?php echo $this->Html->link(__('New TasksTeam'), array('controller' => 'tasks_teams', 'action' => 'add'), array('class' => '')); ?></li>
                                
                                <li class="divider"></li>
                                <li><?php echo $this->Html->link(__('List TasksTeam'), array('controller' => 'tasks_teams', 'action' => 'index'), array('class' => '')); ?></li> 
                                <li><?php echo $this->Html->link(__('List TeamsUsers'), array('controller' => 'teams_users', 'action' => 'index'), array('class' => '')); ?></li> 
                                <li><?php echo $this->Html->link(__('List Assignments'), array('controller' => 'assignments', 'action' => 'index'), array('class' => '')); ?></li> 

                                <li class="divider"></li>
                                <li><?php echo $this->Html->link(__('<i class="fa fa-newspaper-o"></i> Manage Digest'), array('controller' => 'tasks', 'action' => 'manageDigest'), array('escape'=>false));?></li>
                            </ul>
                        </li>

                    <?php endif; ?>
                </ul>
            </li><!-- End Admin -->
            <?php endif; ?>
            
            <li class="dropdown hidden-lg">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-search fa-lg"></i><span class="hidden-sm"> Search</span></a> 
                <ul class="dropdown-menu" role="menu">
                    <li><?php 
                        echo $this->Form->create('Search', array('url'=>array('controller'=>'tasks', 'action'=>'search'), 'inputDefaults' => array('label' => false), 'role' => 'search', 'class'=> 'navbar-form navbar-left')); 
                        echo $this->Form->input('term', array(
                            'class' => 'form-control searchTermEntry',
                            'type'=>'text',
                            'id'=>'searchAM',
                            'size'=>45,
                            'div'=>array(
                                'class'=>'input-group'),
                            'after'=>'<span class="input-group-btn">
                                <button class="btn btn-yh searchSubmit" type="submit"><i class="fa fa-search"></i></button></span>',
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
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user fa-lg hidden-sm"></i><span class="hidden-xs hidden-sm hidden-md"> <?php echo AuthComponent::user('handle');?></span> <b class="caret"></b></a>
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
                    'class' => 'form-control searchTermEntry',
                    'id'=>'SearchTermFull',
                    'type'=>'text',
                    'size'=>15,
                    'div'=>array(
                        'class'=>'input-group'),
                    'after'=>'<span class="input-group-btn">
                        <button class="btn btn-default searchSubmit" type="submit"><i class="fa fa-search"></i></button></span>',
                    'placeholder'=>'Search', 
                    'escape'=>false)); 
                echo $this->Form->end(); 
            ?>
        </div>
	</div>
</nav><!-- /navbar -->

    
