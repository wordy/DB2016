<?php
    echo $this->Html->script('libs/pwstrength-bootstrap-1.2.7.min');


$uStatuses = array(
    100 => 'New User',
    200 => 'Active',
    401 => 'Password Reset',
);

    if (AuthComponent::user('id')){
        $user_id = AuthComponent::user('id');
        $userRole = AuthComponent::user('user_role_id');
        //$userTeamList = AuthComponent::user('TeamsList');
    }
    
    $user = $this->request->data;
    
    
    $readOnlyIn = ($userRole < 200 || $user_id != $user['User']['id'])? 'readonly':false;
    
    
    
    $this->Js->buffer("

            //var tSel = $('#newUserTeams');
        //bindToSelect2(tSel);
        /*
         $('#newUserTeams').select2({
           'width': '100%',
           'theme':'bootstrap',
           'placeholder': 'Select teams user will control',
       });
        */
        
    ");
    
    
    
    
    
?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        
        $('#chgPassForm').on('submit', function(e){
            e.preventDefault();
            //$('#choosePassErrorMsg').html('');
            var txt = $('.password-verdict').text();
            var spos = txt.indexOf("Strong");
            var form = this;
            var match_txt = $('.matchMsg').text();
            $(form).find('.chooseError').remove();

            if(match_txt != 'Passwords match.'){
                $html = '<div class="alert alert-danger chooseError"><b>Password Mismatch: </b>Please re-enter password and confirmation.</div>';
                $('#choosePassErrorMsg').html($html);
            }
            else if (txt && spos != -1 && match_txt == 'Passwords match.'){
                form.submit();    
            }
            else{
                $html = '<div class="alert alert-danger chooseError"><b>Weak Password: </b>Please choose a stronger password. </div>';
                $('#choosePassErrorMsg').html($html);
            }
        });  
        
        
        "use strict";
        var options = {};
        options.ui = {
            scores: [17, 26, 35, 50],
            container: "#pwd-container",
            verdicts: [
                "<i class='fa fa-exclamation-triangle'></i> Weak",
                "<i class='fa fa-exclamation-triangle'></i> Normal",
                "<i class='fa fa-exclamation-triangle'></i> Medium",
                "<i class='fa fa-thumbs-up'></i> Strong",
                "<i class='fa fa-thumbs-up'></i> Very Strong"],
            showVerdictsInsideProgressBar: true,
            viewports: {
                progress: ".pwstrength_viewport_progress"
            }
        };
            
        $('#pass1').pwstrength(options);
            
        function checkPasswordMatch() {
            var password = $("#pass1").val();
            var confirmPassword = $("#pass2").val();
        
            if (password != confirmPassword)
                $("#passMatch").html("<span class='text-danger'><i class='fa fa-exclamation-triangle'></i><span class='matchMsg'> Passwords do not match!</span></span>");
            else if(password == '' || confirmPassword == '')
                $("#passMatch").html("<span class='text-default'><span class='matchMsg'>Enter password.</span></span>");
            else
                $("#passMatch").html("<span class='text-success'><i class='fa fa-thumbs-up'></i> <span class='matchMsg'>Passwords match.</span></span>");
        }
        $("#pass1, #pass2").keyup(checkPasswordMatch);
    });
</script>
<div id="page-container" class="container">
    <h1><?php echo 'Preferences for '.$user['User']['handle']; ?></h1>
    <p>Set preferences related to your compiler account.</p>
    

                <h2>User Settings</h2>

    	<div class="users form well">
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
                    <div class="col-xs-12 col-md-6">

                        <div class="form-group">
                            <?php echo $this->Form->label('email', 'Email Address*');?>
                            <?php echo $this->Form->input('email', array('type'=>'text', 'class' => 'form-control')); ?>
                            <p class="help-block">Email address where you'd like to be reached. For simplicity, please use the same address you use for the Ops Team.</p>
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
                          <?php 

            echo $this->Form->button('<i class="fa fa-lg fa-save"></i>&nbsp; Save Preferences', array(
                'type' => 'submit',
                'class' => 'btn btn-yh',
                'escape' => false
            ));
                        
            
            echo $this->Form->end(); ?>
            </div>
            
            <?php echo $this->Form->create('User', array(
                'action'=>'changePassword',
            
                 'inputDefaults' => array(
                     'label' => false), 
                 'role' => 'form',
                 'novalidate' => true,
                 'id'=>'chgPassForm'
                 )); 
             ?>
             <h2>Change Password</h2>
            <div class="well">
                <div class="row">
                    <div class="col-xs-6">
                       <div class="form-group">
                    <?php echo $this->Form->label('current_pass', 'Current Password*');?>
                    <?php echo $this->Form->input('current_pass', array('type'=>'password', 'class' => 'form-control')); ?>
                    <p class="help-block">Enter your current password.</p>
                </div><!-- .form-group -->
                    </div>
                </div>

                <div class="row"  id="pwd-container">
                    <div class="col-sm-4">
                       <div class="form-group">
                    <?php echo $this->Form->label('password1', 'New Password*');?>
                    <?php echo $this->Form->input('password1', array(
                    'type'=>'password', 'id'=>'pass1', 'class' => 'form-control')); ?>
                    <p class="help-block">Choose a new password that is as least "Strong."</p>
                </div><!-- .form-group -->
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <?php echo $this->Form->label('password2', 'New Password (Re-Enter)*');?>
                            <?php echo $this->Form->input('password2', array('type'=>'password',  'id'=>'pass2', 'class' => 'form-control')); ?>
                            <p class="help-block">Re-enter your new password to confirm.</p>
                        </div><!-- .form-group -->
                    </div>
                    
                    <div class="col-sm-4">
                        <b>Password Strength</b><br>
                        <div class="pwstrength_viewport_progress"></div>
                        <div id="passMatch"></div>
                    </div>

                </div>
			</fieldset>
            <div class="row">
                <div class="col-sm-3">
                                <?php 

            echo $this->Form->button('<i class="fa fa-lg fa-lock"></i>&nbsp; Change Password', array(
                'type' => 'submit',
                'class' => 'btn btn-danger',
                'escape' => false
            ));
                        
            
            echo $this->Form->end(); ?>

                </div>
                <div class="col-sm-9">
                    <span id="choosePassErrorMsg"></span>
                </div>
            </div>
            

			<?php //echo $this->Form->submit('Save User', array('after'=>'<i class="fa fa-save"></i> ', 'class' => 'btn btn-large btn-yh', 'escape'=>false)); ?>
  
		</div><!-- /.form -->
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->


