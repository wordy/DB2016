<?php 
    $urid = $this->Session->read('Auth.User.user_role_id');
    echo $this->Html->docType('html5');
?>
<html lang="en">
    <head>
        
        <?php echo $this->Html->charset(); ?>
        <title><?php echo Configure::read('EventShortName').' Compiler: '.$title_for_layout;?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
    <?php
        echo $this->Html->meta('icon');
        echo $this->fetch('meta');
        //echo $this->Html->css('libs/font-awesome/css/font-awesome.min.css');
        //echo $this->Html->css('libs/bootstrap.min', array ('media'=>'all'));
        //echo $this->Html->css('https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css');
        echo $this->Html->css('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        
        echo $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css');
        echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css');
        
        //echo $this->Html->css('datetimepicker');
        //echo $this->Html->css('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.1/css/select2.min.css');
        //echo $this->Html->css('libs/select2.min'); 
        
        
        echo $this->Html->css('libs/bootstrap-multiselect');
        echo $this->Html->css('libs/daterangepicker');
		echo $this->Html->css('libs/select2-bootstrap.min');
        echo $this->Html->css('libs/summernote');
        echo $this->Html->css('libs/bootstrap-datetimepicker.min');
		echo $this->Html->css('core');
 
        //echo $this->Html->css('compressed/css.min');
 
        echo $this->fetch('css');
        //echo $this->Html->script('libs/bootstrap-datetimepicker_bp');
        //echo $this->Html->script('libs/daterangepicker');
        //echo $this->Html->script('libs/bootstrap-multiselect');
        //echo $this->Html->script('libs/jquery-1.10.2.min');
        //echo $this->Html->script('libs/bootstrap.min');
        //echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js');
        echo $this->Html->script('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js');
        echo $this->Html->script('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js');
        echo $this->fetch('script');
        echo $this->fetch('scriptTop');
	?>
    <noscript><div style="position: fixed; top: 0px; left: 0px; z-index: 3000; height: 100%; width: 100%; background-color: #FFFFFF"><div class="alert alert-danger"><h1>Sorry, the compiler requires Javascript</h1><span><br/><br/><br/><br/><strong>Notice: </strong> JavaScript is not enabled. The use of this site requires a browser that supports Javascript, which you currently don't have.  If you use a browser extension to block JavaScript, please add this website to the "white list". Otherwise, please try another device, or another browser.  <a href="http://enable-javascript.com/" class="alert-link">You can learn about enabling JavaScript in your browser here.</a></span><br/><br/><br/><br/><br/></div></div></noscript>
    </head>
    <body>
        
        <?php echo ($urid)? $this->element('menu/authorized_app_menu'): $this->element('menu/unauthorized_app_menu'); ?>

        <div id="topLevelFlash" class="container hidden-print">
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2" id="topFlashContent"><?php echo $this->Session->flash(); ?>
                    <?php //echo $this->Session->flash('auth'); ?>
                </div>
            </div>
        </div>

        <div class="container-fluid total-page-loc">
            <div id="ajax-content-load" class="container-fluid">
                <?php echo $this->fetch('content'); ?>
            </div><!-- /#content .container -->
        </div>
        
        <footer class="footer">
            <div class="container">
                <div class="row lg-top-marg">
                    <div class="col-xs-6"><p class="text-muted">&copy; 2013-2019 Yee Hong Foundation</p></div>
                    <div class="col-xs-6 text-align-right"><a href="<?php echo $this->Html->url(array('controller'=>'pages', 'action'=>'version'));?>">Version History</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="<?php echo $this->Html->url(array('controller'=>'pages', 'action'=>'legalnotes'));?>">Legal Notes</a></div>
                </div>
            </div>
        </footer>
        <a href="#" id="back-to-top" class="back-to-top"><i class="fa fa-2x fa-arrow-circle-o-up"></i> <span class="h4">Top</span></a>
    <?php
        //echo $this->Filepicker->scriptTag(); 
        
        echo $this->Html->script('libs/moment.min');
        echo $this->Html->script('libs/bootstrap-datetimepicker_bp.min');
        echo $this->Html->script('libs/daterangepicker.min');
        echo $this->Html->script('libs/bootstrap-multiselect.min');
        echo $this->Html->script('//cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js');
        echo $this->Html->script('libs/summernote.min');
        echo $this->Html->script('libs/bootbox.min');
        echo $this->Html->script('libs/jquery.hotkeys');
        
        echo $this->Html->script('ops');
        
        //echo $this->Html->script('compressed/compressed');
        
        //echo $this->Html->script('compile');
        
        echo $this->fetch('scriptBottom');
        echo $this->Js->writeBuffer();   
        
        
        
        

        
        
        
        
        
     ?>
    </body>
</html><!-- default layout-->