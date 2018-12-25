<?php
    $this->layout="default";
    //echo $this->Html->css("signin-form", null, array("inline"=>false));
    $this->set('title_for_layout', 'Compiler Login'); 
    
?>

<div class="container">
    <div class="row lg-top">
        <div class="col-sm-8 col-sm-offset-2 col-md-8 col-md-offset-2 well">
            <h2 class="form-signin-heading text-center">Please Log In</h2>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <?php 
                         echo $this->Session->flash(); 
                         echo $this->Session->flash('auth');
                         echo '<br/>';
                     ?>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                

                    <div class="form-group">

                        <?php 
                    echo $this->Form->create('User', array('class'=>'form-signin', 'role' => 'form'));    
                    echo $this->Form->input('username', array(
                        'label'=>false,
                        'div'=>array(
                            'class'=>'input-group'), 
                        'after'=>'<span class="input-group-addon"><i class="fa fa-user"></i></span>', 
                        'placeholder'=>'Username', 
                        'class'=>'form-control input-lg'));
                        ?>
                    </div>
                </div>    
            </div>

            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">

                <div class="form-group">

                <?php 
                  echo $this->Form->input('password', array(
                    'label'=>false,
                    'placeholder'=>'Password', 
                    'div'=>array(
                        'class'=>'input-group'),
                    'after'=>'<span class="input-group-addon"><i class="fa fa-key"></i></span>',
                    'class'=>'form-control input-lg' ));
                ?>    
                </div>
                </div>    
            </div>
            <div class="row">
                <div class="col-sm-8 col-sm-offset-2">
                <?php 
                    echo $this->Form->button('Log In', array('class'=>'btn btn-yh btn-lg btn-block', 'type' => 'submit'));
                ?>    
                </div>    
            </div>
            <div class="row sm-bot-marg">
                <div class="col-sm-8 col-sm-offset-2">
                    <br/><br/>
                    <div class="pull-right">
                <?php 
                    echo $this->Html->link('Forgot Password', array(
                        'controller'=>'users',
                        'action'=>'forgotPassword',
                    ));
                ?>    
                </div>
                </div>    
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var sc_project=10673942; 
    var sc_invisible=1; 
    var sc_security="6d649148"; 
    var sc_first_party_cookie=0; 
    var scJsHost = (("https:" == document.location.protocol) ?
    "https://secure." : "http://www.");
    document.write("<sc"+"ript type='text/javascript' src='" +
    scJsHost+
    "statcounter.com/counter/counter.js'></"+"script>");
</script>

