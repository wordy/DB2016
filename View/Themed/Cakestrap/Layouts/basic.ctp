<?php 
    $urid = $this->Session->read('Auth.User.user_role_id');
    echo $this->Html->docType('html5');
?>
<html lang="en">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title><?php echo Configure::read('EventShortName').' Compiler: '.$title_for_layout;?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <?php
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        //echo $this->Html->css('libs/font-awesome/css/font-awesome.min.css');
        //echo $this->Html->css('libs/bootstrap.min', array ('media'=>'all'));
        echo $this->Html->css('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
        echo $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
        echo $this->Html->css('libs/summernote');
		echo $this->Html->css('core');
        echo $this->fetch('css');
        //echo $this->Html->script('libs/bootstrap-datetimepicker_bp');
        //echo $this->Html->script('libs/daterangepicker');
        //echo $this->Html->script('libs/bootstrap-multiselect');
        //echo $this->Html->script('libs/jquery-1.10.2.min');
        //echo $this->Html->script('libs/bootstrap.min');
        echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js');
        echo $this->Html->script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js');
        echo $this->fetch('script');
	?>
    <noscript><div style="position: fixed; top: 0px; left: 0px; z-index: 3000; height: 100%; width: 100%; background-color: #FFFFFF"><div class="alert alert-danger"><h1>Sorry, the compiler requires Javascript</h1><span><br/><br/><br/><br/><strong>Notice: </strong> JavaScript is not enabled. The use of this site requires a browser that supports Javascript, which you currently don't have.  If you use a browser extension to block JavaScript, please add this website to the "white list". Otherwise, please try another device, or another browser.  <a href="http://enable-javascript.com/" class="alert-link">You can learn about enabling JavaScript in your browser here.</a></span><br/><br/><br/><br/><br/></div></div></noscript>
    </head>
    <body>
        <?php echo ($urid)? $this->element('menu/authorized_app_menu'): $this->element('menu/unauthorized_app_menu'); ?>
        <?php echo $this->fetch('content'); ?>
        <!-- /#content .container -->
        
    <?php
        //echo $this->Filepicker->scriptTag(); 
        echo $this->Html->script('libs/summernote.min');
        echo $this->Html->script('ops');
        echo $this->fetch('scriptBottom');
        echo $this->Js->writeBuffer();   
     ?>
    </body>
</html><!-- default layout-->