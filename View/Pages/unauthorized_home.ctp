<?php

    $this->set('title_for_layout', 'Ops Committee Compiler'); 
    if (!$this->Session->read('Auth.User')):?>
    
<div class="jumbotron">
    <div class="container">
        <h1><?php echo Configure::read('EventShortName');?> Ops Committee Compiler</h1>
        <p>Welcome to the Dragonball Ops Committee Compiler. To continue, you'll need to log in. If you've forgotten your login or password, you can <?php echo $this->Html->link('reset your password.', array('controller'=>'users', 'action'=>'forgotPassword'))?></p>
        <p>Looking for the 2016 version of the Compiler? It can be found online at <a href="http://db2016.thebws.com">db2016.thebws.com</a></p>
        <p><a class="btn btn-yh btn-lg" href="<?php echo $this->Html->url(array('controller'=>'users', 'action'=>'login'))?>" role="button">Log In</a></p>
    </div>
</div>
<?php endif;?>         
<div class="container">
    <?php echo $this->element('/pages/faq');?>
</div>
