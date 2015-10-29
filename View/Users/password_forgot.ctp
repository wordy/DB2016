    <?php echo $this->Form->create('User', array(
        'action'=>'forgotPassword',
        'class'=>'formResetPW',
        'type'=>'post',
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
    ?>
<div class="container">
    <div class="row">
        <div class="col-xs-12 col-md-12">
            <h1>Password Reset</h1>

            <div class="well">
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <h5><b>Instructions</b></h5>
                <ol>
                    <li>Enter your email address &amp; click Reset Password</li>
                    <li>Check your email for a password reset URL</li>
                    <li>Follow the link in the email and choose your new password</li>
                </ol>
                    </div>
                    
                    <div class="col-xs-12 col-md-6">
                    <h5><b>Please enter your email address</b></h5>
                <div class="form-group">
                    <?php echo $this->Form->input('email', array(
                        'class' => 'form-control',
                        'label'=>false,
                        'placeholder'=>'Enter email address',
                        'div'=>array(
                            'class'=>'input-group'),
                        'after'=>'<span class="input-group-btn"><button class="btn btn-primary" type="submit"><i class="fa fa-refresh"></i> Reset Password</button></span>',
                       )); 
                    ?>
                </div><!-- .form-group -->
                    </div>
                    
                    <div class="col-xs-12 col-md-12">
                        <br>
                        <div class="alert alert-info">
                    <b>Notes</b>
                    <ul>
                        <li>The email may not arrive instantly. Please allow 15 mins before trying again.</li>
                        <li>If necessary, please add the address <b>DBOpsCompiler@gmail.com</b> to your "safe senders" list.</li>
                    </ul>
                </div>
                    </div>
                </div>
                
             
                
                
                
            </div>
        </div>
    </div>
</div>
<?php echo $this->Form->end(); ?> 
