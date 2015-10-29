<?php
$uStatuses = array(
    100 => 'New User',
    200 => 'Active',
    401 => 'Password Reset',
);

    if (AuthComponent::user('id')){
        $userRole = AuthComponent::user('user_role_id');
        //$userTeamList = AuthComponent::user('TeamsList');
    }
    
    $disabledIn = ($userRole < 200)? 'disabled':'';
    
    
    
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
    <h1><?php echo __('Edit User'); ?></h1>
    <p>Use this to edit Ops Team user accounts.</p>
    <div id="page-content" class="col-md-12 well">


    	<div class="users form">
    		<?php echo $this->Form->create('User', array(
    			 'inputDefaults' => array(
    			     'label' => false), 
    		     'role' => 'form',
                 'novalidate' => true,
                 )); 
             ?>
             <?php echo $this->Form->input('id', array('type'=>'hidden')); ?>
    		<fieldset>
    		    <div class="row">
    		        <div class="col-xs-12">
                        <div class="form-group">
                            <?php echo $this->Form->label('Teams', 'Teams User Controls');?>
                            <?php echo $this->Form->input('ControlledTeams', array(
                              'id'=>'newUserTeams',
                              'class'=>'input-conteam-select',
                              'selected'=>$userTeamCodes, 
                              'options'=>$teams,
                              'disabled'=>$disabledIn, 
                              'multiple'=>true)); 
                          ?>
                            <p class="help-block">CAUTION: Users will have full control over any tasks owned by these teams (including deleting!). Add carefully.</p>
                        </div><!-- .form-group -->
    		        </div>
    		    </div>
                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('username', 'Username/Login*');?>
                            <?php echo $this->Form->input('username', array(
                            'class' => 'form-control',
                            'disabled'=>$disabledIn,
                            )); ?>
                            <p class="help-block">Suggested: First initial + last name (i.e. John Doe --> JDoe). Should be unique.</p>
                        </div><!-- .form-group -->                        
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('handle', 'Handle/Display Name*');?>
                            <?php echo $this->Form->input('handle', array('class' => 'form-control',
                            'disabled'=>$disabledIn)); ?>
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
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('user_role_id', 'User Role*');?>
                            <?php echo $this->Form->input('user_role_id', array('disabled'=>$disabledIn, 'class' => 'form-control')); ?>
                        </div><!-- .form-group -->
                    </div>
                </div>








                <div class="row">
                    <div class="col-xs-12 col-md-6">
                        <div class="form-group">
                            <?php echo $this->Form->label('status', 'User Status');?>
                            <?php echo $this->Form->input('status', array(
                                'class' => 'form-control',
                                'options'=>$uStatuses,
                                'disabled'=>$disabledIn,
                                )); 
                            ?>
                            <p class="help-block">Flags users meeting conditions (i.e. password reset requested)</p>
                        </div><!-- .form-group -->
                    </div>
                    <div class="col-xs-12 col-md-6">
                        <b>Subscribe to Digest</b>
                        <div class="checkbox facheckbox facheckbox-circle facheckbox-success">
                            
                            
                            <?php 
                            
                                //$pref_dig = ($this->request->data['User']['pref_digest'] == true)? 'checked': false;
                            echo $this->Form->input('pref_digest', array(
                                'class' => 'input-control',
                                'id'=>'UserPrefDigest',
                                'div'=>false,
                                
                                //'checked'=>$pref_dig,
                                //'value'=>($pref_dig)? 1:0,
                                
                                
                                )); 
                            ?>
                            <?php echo $this->Form->label('pref_digest', 'Digest Subscribed');?>
                            <p class="help-block">Subscribe to the weekly email update</p>
                        </div><!-- .form-group -->
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
			<?php //echo $this->Form->submit('Save User', array('after'=>'<i class="fa fa-save"></i> ', 'class' => 'btn btn-large btn-yh', 'escape'=>false)); ?>
            <?php 

            echo $this->Form->button('<i class="fa fa-lg fa-save"></i>&nbsp; Save User', array(
                'type' => 'submit',
                'class' => 'btn btn-yh',
                'escape' => false
            ));
                        
            
            echo $this->Form->end(); ?>
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
