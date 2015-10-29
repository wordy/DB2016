<div class="row sm-bot-marg">
<nav class="navbar navbar-inverse" role="navigation">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button><!-- /.navbar-toggle -->
		<?php echo $this->Html->Link(Configure::read('AppShortName').': Team Lead', '/', array('class' => 'navbar-brand')); ?>
	</div><!-- /.navbar-header -->
	<div class="collapse navbar-collapse navbar-ex1-collapse">
		<ul class="nav navbar-nav">
            <li class="dropdown">
               <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-bookmark-o"></i> Tasks<b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this -> Html -> link(__('New Task'), array('controller' => 'tasks', 'action' => 'add'), array('class' => '')); ?></li>
                    <li><?php echo $this -> Html -> link(__('Compile Plan'), array('controller' => 'tasks', 'action' => 'compile'), array('class' => '')); ?></li>
                    <li><?php echo $this->Html->link(__('Action Items'), array('controller' => 'tasks', 'action' => 'actionable'), array('class' => '')); ?></li>

                    <?php 
                    if(AuthComponent::user('user_role_id')==10){
                        //Team Lead... only has 1 team
                        $uteams = AuthComponent::user('Teams');
                        $lead_for = $uteams[0];
                        echo '<li>';
                        echo $this->Html->link(__('Tasks Requiring Attention'), array('controller'=>'tasks', 'action'=>'getRecentTaskChangesByTeam',$lead_for ));
                        echo '</li>';
             }
            ?>
                    
                </ul>
            </li>

            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-paperclip"></i> Files <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    <li><?php echo $this -> Html -> link(__('View Files'), array('controller' => 'attachments', 'action' => 'index'), array('class' => '')); ?></li>
                    <li><?php echo $this -> Html -> link(__('Upload A File'), array('controller' => 'attachments', 'action' => 'uploadAttachment'), array('class' => '')); ?></li> 
                    <li><?php echo $this -> Html -> link(__('Attach Files to Tasks'), array('controller' => 'attachments', 'action' => 'attachToTask'), array('class' => '')); ?></li> 
                    
                    </ul>
            </li>
            <li><?php echo $this->Html->link(__('<i class="fa fa-info-circle"></i> Event Information'), array('controller'=>'pages', 'action'=>'display','info'), array('escape'=>false))?></li>
            
    <!--            
            <li><?php echo $this -> Html -> link(__('Files'), array('controller' => 'tasks', 'action' => 'aBuildPlan'), array('class' => '')); ?></li>
            <li><?php echo $this -> Html -> link(__('Build Action Plan'), array('controller' => 'tasks', 'action' => 'aBuildPlan'), array('class' => '')); ?></li>
-->
		</ul><!-- /.nav navbar-nav -->
	

		<ul class="nav navbar-nav pull-right">
		    <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php
           if (AuthComponent::user('id')){
                    echo AuthComponent::user('user_role'). ' - ';
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
</div>