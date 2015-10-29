<?php echo $this->Html->docType('html5'); ?> 
<html lang="en">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo Configure::read('EventShortName').': '.$title_for_layout; ?>
        </title>
        <?php
            //echo $this->fetch('meta');
            //echo $this->Html->css('bootstrap.min', array('media'=>'print'));
            echo $this->Html->css('libs/bootstrap.min', array('media'=>'all'));
            //echo $this->Html->css('font-awesome/css/font-awesome.min.css');
			echo $this->Html->css('core');
            echo $this->fetch('css');
	?>

    </head>
    <body>
        <div class="container">
    		<div class="row">	
                <div id="ajax-content-load" class="container">
                    <?php echo $this->fetch('content'); ?>
                </div><!-- /#content .container -->
            </div>
        </div>
    </body>
</html>