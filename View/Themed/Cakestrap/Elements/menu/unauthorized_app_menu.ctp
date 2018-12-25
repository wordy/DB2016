<nav class="navbar navbar-inverse navbar-fixed-top bg-yh" role="navigation" style="padding-right:10px">
    <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
		<?php echo $this->Html->Link(Configure::read('AppShortName').' Operations Comittee', '/', array('class' => 'navbar-brand')); ?>
	</div><!-- /.navbar-header -->
        <?php
        /*
        <ul class="nav navbar-nav">
            <li><?php echo $this->Html->link(__('<i class="fa fa-info-circle"></i> Event Information'), array('controller'=>'event_infos', 'action'=>'info'), array('escape'=>false));?></li>
       </ul>
         */
         ?>
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <?php /*
        <ul class="nav navbar-nav">
            <li><?php echo $this->Html->link(__('<i class="fa fa-info-circle"></i> Event Information'), array('controller'=>'pages', 'action'=>'display','info'), array('escape'=>false))?></li>
        </ul><!-- /.nav navbar-nav -->
         */
         ?>
        <ul class="nav navbar-nav pull-right">
    		<li>
		      <?php 
                echo $this->Html->link('<i class="fa fa-user"></i>&nbsp;Log In', 
                    array(
                        'controller'=>'users', 
                        'action'=>'login'
                    ),
                    array(
                        'escape'=>false
                    )
                ); 
            ?>
            </li>
        </ul>
    </div>        
</nav><!-- /.navbar navbar-default -->