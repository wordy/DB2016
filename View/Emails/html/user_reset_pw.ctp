<p>Hello <?php echo $user['User']['handle']?>,</p>

You recently requested that your password for the compiler be reset.

<p>If you would like to continue, please follow this link:</p>
<?php 

/*
echo $this->Html->link('db2015/users/chooseNewPass/'.$pw_reset_token,
        'http://db2015/users/chooseNewPass/'.$pw_reset_token
    
    );

*/
    echo $this->Html->link('http://ops.yhdragonball.com/users/chooseNewPass/'.$pw_reset_token, 
        array(
            'controller'=>'users', 
            'action'=>'chooseNewPass', $pw_reset_token,
            'full_base'=>true
        ));
?>
<br>
<p>If you're having difficulty clicking on the link above, you can also paste the URL directly into your browser.</p>

<p>In case you've forgotten, your user name is <b><?php echo $user['User']['username']?></b>. You'll need that once you reset your password.</p>


<p>If you didn't make this request, you can safely ignore this message.  Your password has not been changed.</p>

Thanks,<br>
DBOps Compiler<br><br>

<small>This is an automated message, please do not reply.</small>





