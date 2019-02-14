<?php
    if (AuthComponent::user('id')) {
        $userRole = AuthComponent::user('user_role_id');
        $userTeams = AuthComponent::user('Teams');
        $userTeamList = AuthComponent::user('TeamsList');
        $userTeamByZone = AuthComponent::user('TeamsByZone');
    }
    
    $this->Js->buffer("
    
        //$('.alert-danger').delay(3000).fadeOut('fast');
        
        // Add Role Task
        $('body').on('submit','form.formAddRole', function(e){
            e.preventDefault();
            var thisform = $(this);
            var subBut = thisform.find('.roleSubmitButton');
            var valCont = $(this).find('.roleValidationContent');
            var spinner = $('#global-busy-indicator');
            
            $.ajax( {
                url: $(this).attr('action'),
                type: 'post',
                data: $(this).serialize(),
                dataType:'html',
                beforeSend:function () {
                    subBut.html('<i class=\"fa fa-cog fa-spin\"></i> Saving...').attr('disabled', true);
                    spinner.fadeIn('fast');
                    valCont.fadeOut('fast');              
                },
                success:function(data, textStatus) {
                    $('#topFlashContent').html('<div class=\"alert alert-success\"><i class=\"fa fa-check\"></i><b>OK</b> Role added successfully</div>').fadeIn('fast').delay(3000).fadeOut('fast');            
                    $('#ExistingRolesDiv').html(data).fadeIn('fast');
                    $('#formAddRole').trigger('reset');
                },
                error: function(xhr, statusText, err){
                    valCont.html(xhr.responseText).fadeIn('fast');
                },                
                complete:function (xhr, statusText) {
                    subBut.html('<i class=\"fa fa-plus\"></i> Add Role').attr('disabled', false);
                    spinner.fadeOut('fast');
                },
            });
            return false;
        });
    ");
    
?>
    
<div id="page-container" class="row">
	<div id="page-content" class="col-sm-12">
	    <div class="row">
            <div class="col-md-9 col-sm-8">
                <h1><i class="fa fa-id-badge"></i> Manage Team Roles</h1>
                <p class="lead">Roles are abstract <i>placeholders</i> for individual volunteers, groups, etc. Roles allow teams to plan their execution with placeholders and later fill those roles with actual bodies. </p>

                <div class="well">
                <?php echo $this -> Form -> create('Role', array('class' => 'formAddRole', 'id' => 'formAddRole', 'url' => array('action' => 'manage'), 'novalidate' => true, 'inputDefaults' => array('label' => false), 'role' => 'form'));?>
                        <div class="form-group">
                        <?php echo $this -> Form -> label('Role.team_id', 'Team*'); ?>
                        <?php echo $this -> Form -> input('Role.team_id', array('options' => $userTeamByZone, 'multiple' => false, 'id' => 'addRoleTeamSelect', 'div' => array('class' => 'input-group'), 'after' => '<span class="input-group-addon"><i class="fa fa-users"></i></span>', 'class' => 'form-control inputTeam')); ?>
                        <span class="help-block"> Note: You can only create roles for teams you control.</span>
    
                        </div>
                        <div class="form-group">
                            <?php echo $this -> Form -> label('Role.handle', 'Role Name (Handle)*'); ?>
                            <?php echo $this -> Form -> input('Role.handle', array(
                                'type' => 'text', 
                                'id' => 'rlHandle',
                                'placeholder' => 'Choose a handle', 
                                'div' => array(
                                    'class' => 'input-group', 
                                    'id' => 'inputRoleHandle', 
                                ), 
                                'after' => '<span class="input-group-addon"><i class="fa fa-id-badge"></i></span>', 
                                'class' => 'form-control'));
                            ?>
                        </div>
                        
                        <div class="row">
                            <div class="col-sm-12 roleValidationContent"></div>
                        </div>
    
                        <div class="row">
                            <div class="col-sm-12 col-md-4 col-md-push-8">
                                <button class="btn btn-success btn-lg btn-block roleSubmitButton submit">
                                    <i class="fa fa-plus"></i> Add New Role
                                </button>
                            </div>
                        </div>
                        <?php    echo $this -> Form -> end();?>
                </div><!--well-->
                <div class="alert alert-info"><i class="fa fa-info-circle"></i> <b>Note: </b>If a desired handle is already taken, teams can prefix with their team code, e.g. "FMM-R1".  This should be avoided when possible, however, to avoid confusion.</div>
                
                <h3>Examples</h3>
                <ul>
                    <li><b>All Teams:</b> Team leads and ATLs each have pre-configured roles based on their compiler logins (e.g. @brandon).  You can assign tasks to team TLs or ATLs</li>
                    <li><b>Production</b>: R1/R2, ME/FE, SL/SR, SLA/SRA (runners, escorts, stage left/right and assistants).</li>
                    <li><b>Security:</b> SEC1, SEC2, SEC3 could be pairs of volunteers. You can then plan rotations of those groups through different areas throughout the night, schedule breaks, etc.</li>
                    <li><b>Registration:</b> REG1, REG2, WC1, COU1, COU2 for registration, will call, and courtesy volunteers</li>
                    <li><b>Welcome:</b> GOP1, GOP2, GOP3 for the Gods of Prosperity. Use tasks to plot the deployment of the GOPs to strategic areas throughout the reception, etc.</li> 
                </ul>
            </div>
	        
	        <div class="col-md-3 col-sm-4">
                <div class="roles index" id="ExistingRolesDiv">
                    <?php
                        echo $this->element('role/ajax_manage_roles_table', array('roles'=>$roles,'userRole'=>$userRole))
                    ?>
            	</div><!-- /.index -->
           </div>
    	</div>
	</div><!-- /#page-content .col-sm-9 -->
</div><!-- /#page-container .row-fluid -->
