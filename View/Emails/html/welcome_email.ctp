<p>Hello <?php echo $user['User']['handle']?>,</p>

<p>An account has been created for you in the <?php echo Configure::read('AppShortName');?> Ops Compiler. You can access the compiler at <a href="http://db.thebws.com">http://db.thebws.com</a></p>

<p>Your username is: <b><?php echo $user['User']['username']?></b></p>
    
<p>You'll need this to log into the compiler after you set up your password.</p>
    
<p>Please follow the link below to choose your password:</p>
<?php
/*
    echo $this->Html->link('db2015/users/chooseNewPass/'.$token,
        'db2015/users/chooseNewPass/'.$token
    
    );
 */
    echo $this->Html->link(
        'http://db.thebws.com/users/chooseNewPass/'.$token, 
        array(
            'controller'=>'users', 
            'action'=>'chooseNewPass', $token,
            'full_base'=>true
        )
    );
?>
<br>
<p>If you're having difficulty clicking on the link above, you can also paste the URL directly into your browser.</p>

Thanks,<br>
DBOps Compiler<br><br>

<small>This is an automated message. Please do not reply.</small>





