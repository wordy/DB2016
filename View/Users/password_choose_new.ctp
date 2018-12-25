<?php 
    echo $this->Html->script('libs/pwstrength-bootstrap-1.2.7.min');
    
    $uid = null;
    
    /*
    $this->Js->buffer("
    

    ");
    */
    echo $this->Form->create('User', array(
        'action'=>'chooseNewPass',
        //'url'=>'/users/chooseNewPass/'.$reset_token,
        'class'=>'formChooseNew',
        'type'=>'post',
        'id'=>'formChooseNewPassword',
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
    
             
    ?>
<script type="text/javascript">
    jQuery(document).ready(function () {
        
        $('#formChooseNewPassword').on('submit', function(e){
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
            container: "#pwd-container",
            verdicts: [
                "<i class='fa fa-exclamation-triangle'></i> Weak",
                "<i class='fa fa-exclamation-triangle'></i> Normal",
                "Medium",
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
        $("#pass2, #pass1").keyup(checkPasswordMatch);
    });
</script>
    
<div class="row">
    <div class="col-xs-12 col-md-12">
        <h1>Choose New Password</h1>
        <ul>
            <li>You will need to set at least a "Strong" password, though "Very Strong" is preferred!</li>
            <li>If you forget your password, you can reset it from the login page.</li>
        </ul>
        
        <div class="row">
            <div class="col-md-10 col-md-offset-1 col-xs-12">
        <div class="alert alert-info">
            <h4><i class="fa fa-thumbs-o-up"></i> Tips for Choosing a Strong Password</h4>
                <ul>
                    <li>7+ Characters, mix LEtTeR cAsEs, include numbers and/or special characters (%$^@*#!)</li>
                    <li>OR: try a passphrase - "I HATE remembering passwords!!!" is a secure password that's easy to remember.</li>
                </ul>
            
        </div>
                
            </div>
        </div>
            
        <div class="well">

            <div class="row" id="pwd-container">
                <div class="col-sm-6">
                    <h5><b>Choose New Password*</b></h5>
                    <div class="form-group">
                        <?php echo $this->Form->input('password', array(
                            'class' => 'form-control',
                            'type'=>'password',
                            'id'=>'pass1',
                            'label'=>false,
                           )); 
                        ?>
                    </div><!-- .form-group -->
                </div>
                <div class="col-sm-6" style="padding-top: 40px;">
                    <div class="pwstrength_viewport_progress"></div>        
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="help-block" id="strongMsg">Please choose a password that is at least "Strong"</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <h5><b>Re-enter Password to Confirm*</b></h5>
                        <?php echo $this->Form->input('password2', array(
                            'class' => 'form-control',
                            'label'=>false,
                            'id'=>'pass2',
                            'type'=>'password',
                           )); 
                        ?>
                    </div><!-- .form-group -->
                </div>
                <div class="col-sm-6" style="padding-top: 40px;">
                    <div id="passMatch"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3">
                    <button class="btn btn-default btn-yh" type="submit"><i class="fa fa-lock"></i> Set New Password</button>            
                </div>
                <div class="col-sm-9">
                    <span id="choosePassErrorMsg"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php    
        echo $this->Form->input('reset_token', array(
            'type'=>'hidden',
            'value'=> $reset_token,
       )); 


echo $this->Form->end(); 
    echo $this->Js->writeBuffer();
?> 
