
<nav class="navbar navbar-inverse" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button><!-- /.navbar-toggle -->
		<?php echo $this->Html->Link(Configure::read('AppShortName').': Admin', '/', array('class' => 'navbar-brand')); ?>
	</div><!-- /.navbar-header -->
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
    
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bookmark-o"></i> Tasks<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this->Html->link(__('New Task'), array('controller' => 'tasks', 'action' => 'add'), array('escape' => false, 'class' => '')); ?></li>
                    <li><?php echo $this -> Html -> link(__('Compile Plan'), array('controller' => 'tasks', 'action' => 'compile'), array('escape' => false, 'class' => '')); ?></li>
                    <li><?php echo $this->Html->link(__('Action Items'), array('controller' => 'tasks', 'action' => 'actionable'), array('escape' => false, 'class' => '')); ?></li>
                    <!--<li><?php echo $this -> Html -> link(__('Tasks Requiring Action'), array('controller' => 'tasks', 'action' => 'viewActionable'), array('class' => '')); ?></li>-->
                </ul>
            </li>
	       <?php /*
			<li class="dropdown">
    			<a href="#" class="dropdown-toggle" data-toggle="dropdown">Messages&nbsp;&nbsp;<span class="badge badge-success">4</span>&nbsp;<b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><?php echo $this -> Html -> link(__('Mailbox'), array('controller'=>'messages', 'action' => 'index'), array('class' => '')); ?></li>
					<li><?php echo $this -> Html -> link(__('Send New Message'), array('controller' => 'messages', 'action' => 'add'), array('class' => '')); ?></li> 
				</ul>
			</li>
	
			<li><a href="">Linked Task Changes&nbsp;&nbsp;<span class="badge badge-warning">54</span></a></li>
            */
            ?>
            
			
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-paperclip"></i> Files <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this -> Html -> link(__('View Files'), array('controller' => 'attachments', 'action' => 'index'), array('class' => '')); ?></li>
                    <li><?php echo $this -> Html -> link(__('Upload a File'), array('controller' => 'attachments', 'action' => 'uploadAttachment'), array('class' => '')); ?></li> 
                    <li><?php echo $this -> Html -> link(__('Attach Files to Tasks'), array('controller' => 'attachments', 'action' => 'attachToTask'), array('class' => '')); ?></li> 
                    
                    </ul>
            </li>
            
            
            
            
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-lock"></i> Admin <b class="caret"></b></a>

                <ul class="dropdown-menu">

                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Users</a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('New User'), array('controller' => 'users', 'action' => 'add'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Users'), array('controller' => 'users', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('Reset User\'s Password'), array('controller' => 'users', 'action' => 'resetPassword'), array('class' => '')); ?></li>
                        </ul>
                    </li>
                    
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
                        <a tabindex="-1" href="#">Tasks & Teams</a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('List Teams'), array('controller' => 'teams', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Task Types'), array('controller' => 'task_types', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Actionable Types'), array('controller' => 'actionable_types', 'action' => 'index'), array('class' => '')); ?></li> 
                            <li><?php echo $this->Html->link(__('List Task Colours'), array('controller' => 'task_colors', 'action' => 'index'), array('class' => '')); ?></li> 
                        </ul>
                    </li>

                    
                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Advanced</a>
                        <ul class="dropdown-menu">
                            <li><?php echo $this->Html->link(__('New Change'), array('controller' => 'changes', 'action' => 'add'), array('class' => '')); ?></li>
                            <!--<li><?php echo $this->Html->link(__('New Attachment'), array('controller' => 'attachments', 'action' => 'add'), array('class' => '')); ?></li>-->
                            <li><?php echo $this -> Html -> link(__('Assign Team to Task'), array('controller' => 'tasks_teams', 'action' => 'add'), array('class' => '')); ?></li>
                            <li><?php echo $this -> Html -> link(__('List Team Assignments'), array('controller' => 'tasks_teams', 'action' => 'index'), array('class' => '')); ?></li>
                            <li><?php echo $this->Html->link(__('List Changes'), array('controller' => 'changes', 'action' => 'index'), array('class' => '')); ?></li> 
                        </ul>
                    </li>
                </ul>
            </li>
            <li><?php echo $this->Html->link(__('<i class="fa fa-info-circle"></i> Event Information'), array('controller'=>'pages', 'action'=>'display','info'), array('escape'=>false))?></li>
        </ul><!-- /.nav navbar-nav -->

		<ul class="nav navbar-nav pull-right">
    	    <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
                if (AuthComponent::user('id')){
                    echo AuthComponent::user('handle');
                }
               ?> <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <!--<li><?php echo $this -> Html -> link(__('Change Preferences'), array('controller' => 'actionable_types', 'action' => 'add'), array('class' => '')); ?></li>--> 
                    <li><?php echo $this->Html->link(__('Change Password'), array('controller'=>'users', 'action'=>'changePassword'));?></li>
                    <li><?php echo $this->Html->link(__('Log Out'), array('controller'=>'users', 'action'=>'logout'));?></li>
                    <li class="divider"></li>

                    <li class="dropdown-submenu">
                        <a tabindex="-1" href="#">Teams You Control</a>
                        <ul class="dropdown-menu">
                            <?php 
                                foreach ($this->Session->read('Auth.User.TeamsList') as $team){
                                    echo '<li><a href="#">'.$team.'</a></li>';
                                }
                            ?>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
	</div><!-- /.navbar-collapse -->
</nav><!-- /.navbar navbar-default -->
