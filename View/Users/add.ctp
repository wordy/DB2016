<?php
    $this->Js->buffer("
        //var tSel = $('#newUserTeams');
        //bindToSelect2(tSel);
         $('#newUserTeams').select2({
           'width': '100%',
           'theme':'bootstrap',
           'placeholder': 'Select teams user will control',
       });
        
        
    ");
?>

<div id="page-container" class="row">
    <h1><?php echo __('Add User'); ?></h1>
    <p>Use this to create new Ops Team user accounts.</p>
    <div id="page-content" class="col-md-12 well">


    	<div class="users form">
    		<?php echo $this->Form->create('User', array(
    			 'inputDefaults' => array(
    			     'label' => false), 
    		     'role' => 'form',
                 'novalidate' => true,
                 )); 
             ?>
    		<fieldset>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('username', 'Username/Login*');?>
                            <?php echo $this->Form->input('username', array('class' => 'form-control')); ?>
                            <p class="help-block">Suggested: First initial + last name (i.e. John Doe --> JDoe). Should be unique.</p>
                        </div><!-- .form-group -->                        
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('handle', 'Handle/Display Name*');?>
                            <?php echo $this->Form->input('handle', array('class' => 'form-control')); ?>
                            <p class="help-block">Friendly user identifier. Include team or user role in brackets. E.g. John (FMM) or Mary (GM Z1)</p>
                        </div><!-- .form-group -->
                    </div>
                </div>
                <div class="row">
                  <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('email', 'Email Address*');?>
                            <?php echo $this->Form->input('email', array('type'=>'text', 'class' => 'form-control')); ?>
                            <p class="help-block">Must be valid. Welcome email is sent to this address.</p>
                        </div><!-- .form-group -->
                                                <div class="form-group">
                            <?php echo $this->Form->input('send_welcome', array(
                                'type'=>'checkbox',
                                'div'=>'checkbox',
                                'before'=>'<label>',
                                'after'=>'</label>',
                                'checked'=>true,
                                'label'=>"Send Welcome Email*",
                                )); ?>
                            <p class="help-block">Sends the new user an email allowing them to set up their password. Uncheck to skip this step.</p>
                        </div><!-- .form-group -->    
                    </div>

                    <div class="col-xs-12 col-md-6">
                                                <div class="form-group">
                            <?php echo $this->Form->label('user_role_id', 'User Role*');?>
                            <?php echo $this->Form->input('user_role_id', array('class' => 'form-control')); ?>
                        </div><!-- .form-group -->
                        <div class="form-group">
                            <?php echo $this->Form->label('Teams', 'Teams User Controls');?>
                            <?php echo $this->Form->input('ControlledTeams', array(
                              'id'=>'newUserTeams',
                              'class'=>'input-conteam-select', 
                              'options'=>$teams, 
                              'multiple'=>true)); 
                          ?>
                            <p class="help-block">CAUTION: Users will have full control over any tasks owned by these teams (including deleting!). Add carefully.</p>
                        </div><!-- .form-group -->
                    </div>

                </div>
                
                <div class="row">


                    

                  <div class="col-xs-12 col-md-5">

                    </div>
</div>




<?php /*
                <div class="form-group">
                	<?php echo $this->Form->label('password1', 'Password*');?>
                	<?php echo $this->Form->input('password1', array('type'=>'password', 'class' => 'form-control')); ?>
                    <p class="help-block">Set an initial password.  User can change it themselves once they log in. Remind them!</p>
                </div><!-- .form-group -->

                <div class="form-group">
                    <?php echo $this->Form->label('password2', 'Password (Re-Enter)*');?>
                    <?php echo $this->Form->input('password2', array('type'=>'password', 'class' => 'form-control')); ?>
                </div><!-- .form-group -->
*/?>


			</fieldset>
			<?php //echo $this->Form->submit('Save New User', array('class' => 'btn btn-large btn-yh')); 
                echo $this->Form->button('<i class="fa fa-lg fa-save"></i>&nbsp; Save New User', array(
                    'type' => 'submit',
                    'class' => 'btn btn-yh',
                    'escape' => false
                ));
			?>
            <?php echo $this->Form->end(); ?>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
