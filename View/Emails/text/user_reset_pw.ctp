Hello <?php echo $user['User']['handle']?>,

Someone (hopefully you) recently requested that your password for the compiler be reset.

If you would like to continue, please follow this link:

<?php 
    echo 'http://ops.yhdragonball.com/users/chooseNewPass/'.$user['User']['pass_reset_token'];
?>

If you're having difficulty clicking on the link above, you can also paste the URL directly into your browser.

In case you've forgotten, your user name is <?php echo $user['User']['username']?>. You'll need that once you reset your password.

If you didn't make this request, you can safely ignore this message.  Your password has not been changed.

Thanks,
DBOps Compiler


This is an automated message, please do not reply.





