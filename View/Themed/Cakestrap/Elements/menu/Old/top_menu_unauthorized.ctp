<nav class="navbar navbar-inverse bg-yh" role="navigation">
    <div class="navbar-header">
		<?php echo $this->Html->Link(Configure::read('AppShortName').' Operations Comittee', '/', array('class' => 'navbar-brand')); ?>
	</div><!-- /.navbar-header -->

    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li><?php echo $this->Html->link(__('<i class="fa fa-info-circle"></i> Event Information'), array('controller'=>'pages', 'action'=>'display','info'), array('escape'=>false))?></li>
        </ul><!-- /.nav navbar-nav -->
        <ul class="nav navbar-nav navbar-right">
    		<li class="dropdown"><?php echo $this->Html->link('Log In', array('controller'=>'users', 'action'=>'login')); ?></li>
        </ul>
    </div>        
</nav><!-- /.navbar navbar-default -->