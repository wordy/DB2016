<?php
    $this->assign('page_title', 'Compile Plan ');
    
    echo $this->Html->css('bootstrap-wysihtml5', array('inline'=>false));
    echo $this->Html->script('/eip/inputs-ext/wysihtml5/bootstrap-wysihtml5-0.0.2/wysihtml5-0.3.0.min', array('inline'=>false));
    echo $this->Html->script('/eip/inputs-ext/wysihtml5/wysihtml5', array('inline'=>false));
    echo $this->Html->script('bootstrap3-wysihtml5', array('inline'=>false));

    if (AuthComponent::user('id')){
        $controlled_teams = AuthComponent::user('Teams');
        $user_role = AuthComponent::user('user_role_id');
    }
        
    //$show_ = $this->request->data('Task.show_threaded');
    $show_details = $this->request->data('Task.show_details');
    
    // JSON encoded for Xeditable
    $j_teams = array();
    foreach ($teams as $cid => $zone){
        foreach($zone as $tid=>$tcode){
            $j_teams[] = array('value'=>$tid,'text'=>$tcode);    
        }
    }
    $j_teams = json_encode($j_teams);
    
    $j_acttypes = array();
    foreach ($actionableTypes as $type_id => $str_type){
        $j_acttypes[] = array('value'=>$type_id,'text'=>$str_type);
    }
    $j_acttypes = json_encode($j_acttypes);
    
    $j_tasktypes = array();
    foreach ($taskTypes as $k => $category){
        foreach ($category as $type_id => $str_type){
           $j_tasktypes[] = array('value'=>$type_id,'text'=>$str_type);
        }    
    }
    $j_tasktypes = json_encode($j_tasktypes);
    
    
    

$this->Js->buffer("
    $('#chgtest').on('click', function(event){
        $.ajax( {
            url:'".$this->Html->url(array('controller'=>'changes', 'action'=>'pageChanges', 657))."',
            beforeSend:function () {
                //$('#ajax-menu-spinner').fadeIn();
                },
            success:function(data, textStatus) {
                $('#ajax_content').html(data);},
            complete:function (XMLHttpRequest, textStatus) {
                //$('#ajax-menu-spinner').fadeOut();
                alert('complete!');
                }, 
            type: 'post',
            dataType:'html',
          });
          return false;
      });

    //EIP
    $('a.eip_task_start_time').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipStartTime'))."',
        title: 'Task Start Time',
        format: 'yyyy-mm-dd hh:ii:ss',
        clear: false,
        showbuttons:false,    
        viewformat: 'M d hh:ii',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'A start time is required.';
            }
        },
        datetimepicker: {
            //weekStart: 1,
            //todayBtn: 'linked',
            todayHighlight: true,
            minuteStep: 1,
            pickerPosition: 'bottom-left',
            startView: 0,
       }
    });

    $('a.eip_task_end_time').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipEndTime'))."',
        title: 'Task End Time',
        format: 'yyyy-mm-dd hh:ii:ss',  
        clear: false,          
        showbuttons:false,
        viewformat: 'M d hh:ii',
        validate: function(value) {
            if($.trim(value) == '') {
                return 'A start time is required.';
            }
        },
        datetimepicker: {
            //weekStart: 1,
            //todayBtn: 'linked',
            todayHighlight: true,
            minuteStep: 1,
            pickerPosition: 'bottom-left',
            startView:0
       }
    });
    
    $('a.eip_task_public').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipPublic'))."',
        title: 'Task Visibility',
        type: 'select',
        showbuttons: false,
        source: [
          {value: 0, text: 'Private'},
          {value: 1, text: 'Public'},
        ],
        display: function(value, sourceData) {
            if(value==1){
                var outhtml = '<span><i class=\"fa fa-lg fa-eye\"></i> <small>Public</small></span>';
            }
            else{
                var outhtml = '<span><i class=\"fa fa-lg fa-eye-slash\" ></i> <small>Private</small></span>';
            }
                
            $(this).html(outhtml);
        },
    });
    
    $('a.eip_task_type').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipTaskType'))."',
        title: 'Task Type',
        type: 'select',
        showbuttons: false,
         source:".$j_tasktypes.",
    });
    
    $('a.eip_short_description').editable({
        url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'eipShortDescription'))."',
        title: 'Short Description (140 Characters)',
        type: 'text',
        showbuttons: true,
        validate: function(value) {
            if($.trim(value) == '') {
                return 'A short description is required.';
            }
            if(value.length> 140){
                return 'This field is limited to 140 characters.  You entered '+value.length+'.';
            }
        },
    });

    var offset = 420;
    var duration = 500;
    $(window).scroll(function() {
        if ($(this).scrollTop() > offset) {
            $('.back-to-top').fadeIn(duration);
        } else {
            $('.back-to-top').fadeOut(duration);
        }
    });
                
    $('.back-to-top').click(function(event) {
        event.preventDefault();
        $('html, body').animate({scrollTop: 0}, duration);
        return false;
        
    });
    
    $('#performAjaxLink').click(
            function()
            {                
                $.ajax({
                    type:'POST',
                    async: true,
                    cache: false,
                    url: '".$this->Html->url(array('controller'=>'tasks', 'action'=>'hello'))."',
                    success: function(response) {
                        //console.log(response);
                        //var team_code = response.Task.team_code;
                        var dataFromServer = JSON.parse(response);
                        console.log(dataFromServer);
                        $('#resultField').val(dataFromServer.Task.team_code);
                    },
                    //data: $('form').serialize()
                });
                return false;
            }
    );
    
    $('.info').on('click', function(){
        
    });
    
    
    
");


?>
    <style>
        .back-to-top {
            position: fixed;
            bottom: 2em;
            right: 0px;
            text-decoration: none !important;
            color: #000000;
            background-color: rgba(235, 235, 235, 0.80);
            font-size: 12px;
            padding: 1em;
            display: none;
        }

        .back-to-top:hover {
            text-decoration: none !important;
    
            background-color: rgba(135, 135, 135, 0.50);
        }   
    </style>

    <div class="row">
        <h2>
            <?php echo $this->fetch('page_title');
            //echo '<p>Total Keys: ' . $rc . '</p>';
            ?>
            <a href="#" class="back-to-top"><i class="fa fa-2x fa-arrow-circle-o-up"></i> <span class="h4">Top</span></a>
            <small><?php echo $this->fetch('page_title_sub');?></small>
        </h2>
    </div>
    
    <ul class="nav nav-pills" role="tablist">
  <li role="presentation" class="active"><a href="#home" role="tab" data-toggle="pill">Home</a></li>
  <li role="presentation"><a href="#profile" role="tab" data-toggle="pill">Profile</a></li>
  <li role="presentation"><a href="#messages" role="tab" data-toggle="pill">Messages</a></li>
  <li role="presentation"><a href="#settings" role="tab" data-toggle="pill">Settings</a></li>
</ul>

<!-- Tab panes -->
<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="home">home</div>
  <div role="tabpanel" class="tab-pane" id="profile">profile</div>
  <div role="tabpanel" class="tab-pane" id="messages">msg.</div>
  <div role="tabpanel" class="tab-pane" id="settings">set.</div>
</div>
    
    
    
    <div class="row">
        <div>
            <ul id="myTab" class="nav nav-tabs">
                <li class="active"><a href="#quick_add" data-toggle="tab">Add Task</a></li>
                <li><a href="#compile_options" data-toggle="tab">Compile Options</a></li>
            </ul>
            
            <div id="myTabContent" class="tab-content">
                <div class="tab-pane fade in col-md-12 vsm-top-marg" id="compile_options">
                    <?php echo $this->element('task/compile_options'); ?>
                </div>
                <div class="tab-pane fade in active col-md-12 vsm-top-marg" id="quick_add">
                    <?php echo $this->element('task/quick_add'); ?>
                </div>
            </div>
        </div>
    </div>
	
	
    
	<div id="page-content" class="row">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default task-panel tp-border  teamtid3">
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-2">
								<label class="task">
									<input type="checkbox" id="checkbox4"/>
										Nov 11 10:00 - 11:00<br>(2h 31m)
									</input>
								</label>
							</div>
							<div class="col-sm-10">
							<div class="pull-right">
									<button type="button" class="btn btn-danger btn-xs xs-bot-marg">
									<i class="fa fa-clock-o"></i> Nov 11 &nbsp;
								</button><br>
								<button type="button" id="chgtest" class="btn btn-success btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
									10 New
								</button>
								</div>
								<a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657">(<b>Meeting</b>) </a>
								<a href="#" class="eip_short_description editable editable-click" data-value="First Meeting" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
								<br/>
								<span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
								<br/>
								Details allaalall la ala lala la la lal allaalall la ala lala la la lalaal laalall la ala lala la la lalaallaalall la ala lala la la lala
								
								
						
							
							
							
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="panel panel-default med-top-marg">

  <div class="panel-body">
  	<div class="row">
			<div class="col-md-2">
				<div class="list-group">
				  <a href="#" class="list-group-item active">
					Task Menu
				  </a>
				  <a href="#" class="list-group-item">Edit</a>
				  <a href="#" class="list-group-item">Changes</a>
				</div>

			</div>


			<div class="col-md-10">
        <div class="row">
            <div class="col-md-9">
                <?php echo $this->Form->create('Task', array(
                    //'url' => array(
                      //  'controller' => 'tasks', 
                        //'action' => 'edit', $task['Task']['id']),
                    'action'=>'edit',
                    'type'=>'post', 
                    'id'=>'form-edit-task', 
                    'novalidate' => true,
                    'inputDefaults' => array(
                        'label' => false), 
                    'role' => 'form')); ?>
                <?php echo $this->Form->input('id', array('type'=>'hidden')); ?>
  

                    <div class="row">

                        <div class="col-md-4">
                            <div class="form-group">
                                <?php echo $this->Form->label('task_type_id', 'Task Type*'); ?>
                                <?php echo $this->Form->input('task_type_id', array('class' => 'form-control', 'id'=>'input-tasktype-select', 'options'=>$taskTypes)); ?>
                            </div><!-- .form-group -->
                        </div>

                        <div class="col-md-4">
                            <?php echo $this->Form->input('start_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'label'=>'Start Time*',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                'class'=>'form-control datetimepicker-stime',
                                'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')))); 
                            ?>
                        </div>

                        <div class="col-md-4">
                            <?php echo $this->Form->input('end_time', array(
                                'format' => array('label', 'between', 'before', 'input', 'after', 'error'),
                                'type'=>'text',
                                'label'=>'End Time*',
                                'between'=>'',
                                'before'=>'<div class="input-group">',
                                'placeholder'=>'Choose a date',
                                'div'=>array(
                                    'data-date-format' => 'Y-m-d H:i:s'),
                                'after'=>'<span class="input-group-addon"><i class="fa fa-calendar"></i></span></div>',
                                'class'=>'form-control datetimepicker-etime',
                                'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')))); ?>
                        </div>
      

      
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?php echo $this->Form->label('short_description', 'Short Description*');?>
                                <?php echo $this->Form->input('short_description', array(
                                    'error' => array('attributes' => array('wrap' => 'span', 'class' => 'help-inline text-danger bolder')),
                                    'class' => 'form-control')); ?>
                            </div><!-- .form-group -->
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                                <?php echo $this->Form->input('details', array(
                                    'label'=>'Details', 
                                    'id'=>'edit_input-details', 
                                    'class'=>'input-details form-control', 
                                    'type' => 'textarea')); ?>
                                <p class="help-block">(Optional) Use this to store extra details about this task</p>   
                        </div>
                    </div>
					
									

                    <div class="row">
				<div class="col-md-12">
                                    <?php 
                
            echo $this->Form->submit('  Save Changes  ', array('id'=>'form-submit-button', 'div'=>false, 'class' => 'btn btn-large btn-success'));
            echo '&nbsp;&nbsp;';
            echo $this->Html->link('Cancel', array('action'=>'compile'), array('class'=>'btn btn-large btn-danger'));
        
       echo $this->Form->end(); ?> 
                                </div>
                            </div>

            </div><!--col-md-9-->

            <div class="col-md-3">
                    <div class="panel panel-yh">
                    <div class="panel-heading"><b>Teams</b></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo $this->Form->label('team_id', 'Lead Team*');?>
                                    <?php echo $this->Form->input('team_id', array(
                                        'empty'=>1,
                                        'readonly'=>1, 
                                        'div'=>false, 
                                        'multiple'=>false, 
                                        'options'=>array(1,2,3,4), 
                                        'id'=>'edit_input-leadteam-select', 
                                        'class' => 'form-control',
                                        'error' => array('attributes' => array(
                                            'id'=>'lead-team-error-message',
                                            'wrap' => 'span', 
                                            'class' => 'help-inline text-danger bolder')
                                        ))); ?>
                                </div><!-- .form-group -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php echo $this->Form->label('AssistTeams', 'Request Response From');?>
                                    <?php echo $this->Form->input('AssistTeams', array(
                                        'empty'=>true, 
                                        'id'=>'e10', 
                                        'options'=>array(1,2,3), 
                                        'selected'=>array(1,2,3),
                                        'type'=>'select', 
                                        'placeholder'=>'Select Assisting Team(s)', 
                                        'multiple'=>true, 
                                        'class' => 'input-conteam-select')); ?>
                                </div><!-- .form-group -->
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('PushTeams', 'Notify (Nothing Needed)');?>
                                        <?php echo $this->Form->input('PushTeams', array(
                                            'empty'=>true, 
                                            'id'=>'e11', 
                                            'options'=>array(1,2,3),
                                            'selected'=>array(1,2,3), 
                                            'type'=>'select', 
                                            'placeholder'=>'Select Pushed Team(s)', 
                                            'multiple'=>true, 
                                            'class' => 'input-conteam-select')); ?>
                                    </div> <!--.form-group--> 
                                </div>
                            </div>
                    </div><!--panel body-->
                </div><!--panel-->
                
                <div class="panel panel-danger">
                    <div class="panel-heading"><b>Dates &amp; Statuses</b></div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="form-group">
                            <div class="col-md-12">
                                <?php echo $this->Form->label('due_date', 'Due Date'); ?>
                                <?php echo $this->Form->input('due_date', array(
                                    'empty'=>true,
                                    'type'=>'text',
                                    'placeholder'=>'Set due date',
                                    'div'=>array(
                                        'class'=>'input-group'),
                                    'after'=>'<span class="input-group-addon"><i class="fa fa-clock-o"></i></span>',
                                    'class'=>'form-control input-date-notime')); ?>
                            </div>
                            </div>
                        </div>
                        
                        <?php if($user_role >= 200): ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <?php echo $this->Form->label('actionable_type_id', 'Action Item Status');?>
                                        <?php echo $this->Form->input('actionable_type_id', array(
                                            //'id'=>'input-actionabletype-select',
                                            'empty'=>true,
                                            'value'=>1,
                                            'options'=>array(1,2,3), 
                                            'div'=>array(
                                                'class'=>'input-group'),
                                                //'label'=>'Actionable Type',        
                                            'after'=>'<span class="input-group-addon"><i class="fa fa-flag"></i></span>',
                                            'class' => 'form-control')); ?>
                                    </div><!-- .form-group -->
                                </div>
                            </div>                        
                        <?php endif; ?>
                </div><!--panel-->

                                
				</div><!--col-md-3-->
				

				
				
        </div><!--row-->
    </div><!--outer panel body-->

</div><!--outer panel-->
					
					
					
					
					
					
					
					
					
					
					
					
					
					
				  </div>
				</div>
			</div>
			
		</div>
</div>
</div>
			
	
	
	
	
	
	
	
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default task-panel teamtid2">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-11">
										<div class="row">
											<div class="col-md-2">
												<label class="task">
													<input type="checkbox" id="checkbox4"/>
														Nov 11<br/> 10:00 - 11:00 <br>(2h 31m)
													</input>
												</label>
											</div>
											
											<div class="col-md-10">
												<a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657">(<b>Meeting</b>) </a>
												<a href="#" class="eip_short_description editable editable-click" data-value="First Meeting lalal la alal al alala l ala" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
												<br/>
												<span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
												<br/>
												Details allaalall la ala lala la la lal allaalall la ala lala la la lalaal laalall la ala lala la la lalaallaalall la ala lala la la lala
											</div>
										</div>
									</div>

									<div class="col-md-1">
										<button type="button" class="btn btn-primary btn-xs xs-bot-marg">
											<i class="fa fa-clock-o"></i> Nov 11 &nbsp;
										</button>
										<button type="button" id="chgtest" class="btn btn-success btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
											10 New
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default task-panel">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-2">
											<label class="task">
												<input type="checkbox" id="checkbox4"/>
												Nov 11<br/> 10:00 - 11:00 <br>(2h 31m)
												</input>
											</label>
								</div>
								
								<div class="col-md-9">
									<a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657">(<b>Meeting</b>) </a>
									<a href="#" class="eip_short_description editable editable-click" data-value="First Meeting lalal la alal al alala l ala" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
									<br/>
									<span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
									<br/>
									Details allaalall la ala lala la la lal allaalall la ala lala la la lalaal laalall la ala lala la la lalaallaalall la ala lala la la lala
								</div>
								<div class="col-md-1"><button type="button" class="btn btn-primary btn-xs xs-bot-marg">
                                            <i class="fa fa-clock-o"></i> Nov 11 &nbsp;
                                        </button>
                                        <button type="button" id="chgtest" class="btn btn-success btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
                                            10 New
                                        </button></div>
							</div>
							<!--<div class="row">
								<div class="col-md-2">col-md2</div>
								<div class="col-md-9">
								
								</div>
								<div class="col-md-1">col-md2</div>
							</div>
							<div class="row">
								<div class="col-md-11">col-md10</div>
								<div class="col-md-1">col-md2</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="panel panel-default task-panel teamtid1">
						<div class="panel-body">
							<div class="row">
								<div class="col-md-1">
											<label class="task">
												<input type="checkbox" id="checkbox4"/>
												Nov 11<br/> 10:00 - 11:00<br>(2h 31m)
												</input>
											</label>
								</div>
								
								<div class="col-md-10">
									<a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657"><b>Meeting</b></a>
									<a href="#" class="eip_short_description editable editable-click" data-value="First Meeting lalal la alal al alala l ala" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
									<br/>
									<span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
									<br/>
									Details allaalall la ala lala la la lal allaalall la ala lala la la lalaal laalall la ala lala la la lalaallaalall la ala lala la la lala
								</div>
								<div class="col-md-1"><button type="button" class="btn btn-danger btn-xs xs-bot-marg">
                                            <i class="fa fa-clock-o"></i> Nov 11 &nbsp;
                                        </button>
                                        <button type="button" id="chgtest" class="btn btn-yh btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
                                            10 New
                                        </button></div>
							</div>
							<!--<div class="row">
								<div class="col-md-2">col-md2</div>
								<div class="col-md-9">
								
								</div>
								<div class="col-md-1">col-md2</div>
							</div>
							<div class="row">
								<div class="col-md-11">col-md10</div>
								<div class="col-md-1">col-md2</div>
							</div>-->
						</div>
					</div>
				</div>
			</div>
	
	
	
	
	<!--
		<div class="row">
			<div class="col-md-3">
				<div class="list-group">
				  <a href="#" class="list-group-item active">
					Task Menu
				  </a>
				  <a href="#" class="list-group-item">Edit</a>
				  <a href="#" class="list-group-item">Changes</a>
				</div>

			</div>


			<div class="col-md-9">
				<div class="panel panel-default">
				  <div class="panel-heading">Panel heading without title</div>
				  <div class="panel-body">
					Panel content
				  </div>
				</div>

				<div class="panel panel-default">
				  <div class="panel-heading">
					<h3 class="panel-title">Panel title</h3>
				  </div>
				  <div class="panel-body">
					Panel content
				  </div>
				</div>
			</div>
			
		</div>-->
	
	




		<div class="row">
	        <div class="col-md-12">
	            <a href="#" id="performAjaxLink">Do Ajax </a>
	            <form id="ajaxform">
                <?php echo $this->Form->input('your_field', array('id' => 'resultField')); ?>
                </form>
	        </div>
	    </div>
	    
	    <!--
	    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <span class="glyphicon glyphicon-list"></span>Sortable Lists
                    <div class="pull-right action-buttons">
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                <span class="glyphicon glyphicon-cog" style="margin-right: 0px;"></span>
                            </button>
                            <ul class="dropdown-menu slidedown">
                                <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-pencil"></span>Edit</a></li>
                                <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-trash"></span>Delete</a></li>
                                <li><a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-flag"></span>Flag</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                
                <div class="panel-body ttest">
                    <ul class="list-group">
                        <li class="list-group-item ttrow">
                            <div class="row">
                                <div class="col-md-1 highlight">
                                    <h4 class="great">Nov 25</h4>
                                        <div class="checkbox">
                                            <input type="checkbox" id="checkbox4" />
                                            <p>Nov 11 10:00-11:00</p>
                                        </div>
                                </div>
                                
                                <div class="col-md-2 highlight2">
                                    <a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657"><b>Meeting</b></a>
                                    <br/>
                                    <span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
                                </div>
                            
                                <div class="col-md-8 highlight">
                                    <div>
                                        <a href="#" class="eip_short_description editable editable-click" data-value="First Meeting lalal la alal al alala l ala" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
                                    </div>
                                    <div class="comment-text">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum mauris lorem, euismod nec enim quis, vulputate hendrerit neque. Integer euismod iaculis orci quis lobortis. Integer hendrerit, erat eget tempus tincidunt, ligula erat molestie orci, sed pharetra tellus sapien ut orci. Sed porttitor eget urna non cursus. Suspendisse purus quam, fringilla at facilisis eu, sollicitudin vitae tellus. Fusce imperdiet, nibh et porta volutpat, est dolor porta arcu, vel fringilla tortor tortor ac lacus. Vivamus nec arcu dictum, auctor tortor id, sodales enim. Etiam sodales leo id turpis rutrum auctor. Etiam quis lobortis orci, eu ultrices neque. Vestibulum pellentesque lectus id felis ultrices rhoncus. Phasellus vel justo libero. Vestibulum sagittis sem eu eleifend varius. Nullam sed magna auctor, interdum leo sit amet, egestas mi. Ut sit amet libero commodo, scelerisque felis nec, fringilla sapien. Praesent rutrum turpis lectus, vel convallis velit rhoncus vel. Sed commodo faucibus elit, in aliquam libero tempor nec.

<p>In hac habitasse platea dictumst.</p>
                                    </div>
                                </div>
                            
                                <div class="col-md-1 pullright highlight2">
                                        <button type="button" class="btn btn-danger btn-xs xs-bot-marg">
                                            <i class="fa fa-clock-o"></i> Nov 11 &nbsp;
                                        </button>
                                        <button type="button" id="chgtest" class="btn btn-yh btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
                                            10 New
                                        </button>

                                    
                                        <button type="button" class="btn btn-default btn-xs" data-toggle="collapse" data-target="#li-collapsed">
                                            Collapse
                                        </button>
                                        <div class="btn-group">
                                            <?php echo $this->Html->link(__('View'), array('controller'=>'tasks', 'action' => 'view', 1), array('class' => 'btn btn-default btn-xs')); ?>
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view', 1)); ?>
                                                    </li>
                                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', 1)); ?></li>
                                                    <li class="divider"></li>
                                                    <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', 1), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                                </ul>
                                        </div>
                                </div>
                            </div>
                        </li>
                        <div class="col-md-12" id="li-collapsed">
                            <div class="row" id="ajax_content">
                                <div class="col-md-12">
                                <div class="col-md-1">
                                    <div class="checkbox">
                                        <input type="checkbox" id="checkbox4" />
                                        <p>Nov 11 10:00-11:00</p>
                                    </div>
                                </div>
                                
                                <div class="col-md-2">
                                    <a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657"><b>Meeting</b></a>
                                    <br/>
                                    <span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
                                </div>
                            
                                <div class="col-md-7">
                                    <div>
                                        <a href="#" class="eip_short_description editable editable-click" data-value="First Meeting lalal la alal al alala l ala" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
                                    </div>
                                    <div class="comment-text">
                                        Should be collapsed
                                    </div>
                                </div>
                            
                                <div class="col-md-1 pullright">
                                        <button type="button" class="btn btn-danger btn-xs xs-bot-marg">
                                            <i class="fa fa-clock-o"></i> Nov 11
                                        </button>
                                        <button type="button" class="btn btn-yh btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
                                            10 New
                                        </button>

                                    

                                        <div class="btn-group">
                                            <?php echo $this->Html->link(__('View'), array('controller'=>'tasks', 'action' => 'view', 1), array('class' => 'btn btn-default btn-xs')); ?>
                                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                                    <span class="caret"></span>
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li>
                                                        <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view', 1)); ?>
                                                    </li>
                                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', 1)); ?></li>
                                                    <li class="divider"></li>
                                                    <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', 1), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                                </ul>
                                            </div>
                                        </div>
                                    </div></div>
                            </div>
                        
                        <li class="list-group-item ttrow">
                        
    
                            <div class="row">
                                <div class="col-md-10 col-md-offset-1">
                                    <div class="row">
                            <div class="col-md-1">
                                                            <h4 class="great">Nov 25</h4>
                                
                            <div class="checkbox">
                                <input type="checkbox" id="checkbox4" />
                                <p>Nov 11 10:00-11:00</p>
                            </div>
                            </div>
                            <div class="col-md-2">
                                <a href="#" class="eip_task_type editable editable-click" data-value="3" data-name="Task.task_type_id" data-type="select" data-pk="657"><b>Meeting</b></a>
                                <br/>
                            <span class="btn btn-leadt">CC</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-danger btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span><span class="btn btn-default btn-xxs">BSA</span>
                            </div>
                            
                            <div class="col-md-8">
                                <div>
                                    <a href="#" class="eip_short_description editable editable-click" data-value="First Meeting lalal la alal al alala l ala" data-name="Task.short_description" data-type="text" data-pk="657"><b>First Meeting</b></a>
                                                          
                                </div>
                                <div class="comment-text">
                                    
                                </div>
                            </div>
                            <div class="col-md-1 pullright">
                                <div class="mic-info">
                                    <button type="button" class="btn btn-danger btn-xs xs-bot-marg">
                                        <i class="fa fa-clock-o"></i> Nov 11 &nbsp;
                                    </button>

                                    <button type="button" class="btn btn-yh btn-xs xs-bot-marg"><i class="fa fa-exchange"></i>
                                        10 New
                                    </button>
  
  
                                    
                                    <div class="btn-group">
                                <?php echo $this->Html->link(__('View'), array('controller'=>'tasks', 'action' => 'view', 1), array('class' => 'btn btn-default btn-xs')); ?>
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view', 1)); ?>
                                    </li>
                                    
                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', 1)); ?></li>
                                     
                                    
                                        <li class="divider"></li>
                                        <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', 1), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                     
                                </ul>
                            </div>
                                    
                                </div>
                            </div>
                        </div></div></div>

                        </li>
                        <li class="list-group-item ttrow2">
                            <div class="checkbox">
                                <input type="checkbox" id="checkbox5" />
                                <label for="checkbox5">
                                    List group item heading 4
                                </label>
                            </div>
                           <div class="pull-right action-buttons">
                                <a href="http://www.jquery2dotnet.com"><span class="glyphicon glyphicon-pencil"></span></a>
                                <a href="http://www.jquery2dotnet.com" class="trash"><span class="glyphicon glyphicon-trash"></span></a>
                                <a href="http://www.jquery2dotnet.com" class="flag"><span class="glyphicon glyphicon-flag"></span></a>
                            </div>
                        </li>
                    </ul>
                </div>
                

                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>
                                Total Count <span class="label label-info">25</span></h6>
                        </div>
                        <div class="col-md-6">
                            <ul class="pagination pagination-sm pull-right">
                                <li class="disabled"><a href="javascript:void(0)">«</a></li>
                                <li class="active"><a href="javascript:void(0)">1 <span class="sr-only">(current)</span></a></li>
                                <li><a href="http://www.jquery2dotnet.com">2</a></li>
                                <li><a href="http://www.jquery2dotnet.com">3</a></li>
                                <li><a href="http://www.jquery2dotnet.com">4</a></li>
                                <li><a href="http://www.jquery2dotnet.com">5</a></li>
                                <li><a href="javascript:void(0)">»</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
	    
	    
	    
	    
	    
	    
	    
	    
	    
	    
    <?php 
        if (!empty($tasks)){ ?>
            <div class="tasks index">
            <div class="row">
                <div class="col-md-12">
                    <div class="pull-right">
                        <p><i class="fa fa-exchange"></i> Changes, <i class="fa fa-paperclip"></i> Attachments, <i class="fa fa-clock-o"></i> Due Date, <i class="fa fa-flag"></i> Action Item, <i class="fa fa-eye"></i> Visibility</p>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table id="tasks-index" class="table table-hover table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th width="1%"> </th>
                            <th width="7%"><?php echo __('Time'); ?></th>
                            <th width="21%"><?php echo __('Teams'); ?></th>
                            <th width="57%"><?php echo __('Description'); ?></th>
                            <th width="6%">Icons</th>
                            <th width="8%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $today = date('Y-m-d');
                        $today_str = strtotime($today);
                        $owa = strtotime($today.'-1 day');
                        
                        $owfn = strtotime($today.'+1 week');
                        
                        foreach ($tasks as $task):
                            $hasDueDate = false;
                            $hasDueSoon = false;
                            $hasActionable = false;
                            $hasAttachment = false;
                            $hasNewAttachment = false;
                            $hasChange = false;
                            $hasNewChange = false;
                            
                            if(!empty($task['Task']['due_date'])){
                                $dueString = strtotime($task['Task']['due_date']);
                                $hasDueDate = true;
                                
                                if(($dueString >= $today_str) && ($dueString < $owfn)){
                                    $hasDueSoon = true;
                                }
                            }
                            
                            if(!empty($task['Task']['actionable_type'])){
                                $hasActionable = true;
                            }
                            
                            if (!empty($task['Attachment'])){
                                $hasAttachment = true;
                                $numNewAttachment = 0;
                                $numAttachment = 0;
                                foreach ($task['Attachment'] as $att){
                                    $numAttachment++;
                                    if (strtotime($att['created']) > $owa){
                                        $hasNewAttachment = true;
                                        $numNewAttachment++;
                                    }
                                }  
                            }
                            
                            if (!empty($task['Change'])){
                                $hasChange = true;
                                $numChange = 0;
                                foreach ($task['Change'] as $chg){
                                    if (strtotime($chg['created'])  > $owa){
                                        $hasNewChange = true;
                                        //break;
                                        $numChange++;
                                    }
                                }  
                            }
                        
                        ?>

                        <tr <?php 
                            if($this->request->data('Task.color_act_pri')){
                                if($task['Task']['public']==0){
                                    echo 'class= "active"';
                                }
                                elseif($task['Task']['actionable_type_id']){
                                    echo 'class="danger"';
                                }
                                elseif(!empty($task['Task']['due_date'])) {
                                    $now = strtotime($today);
                                    $owfn = strtotime($today.'+1 week');
                                    $duedate = strtotime($task['Task']['due_date']);
                                    
                                    if (($duedate > $now) && ($duedate < $owfn)){
                                        echo 'class="warning"';    
                                    }
                                }
                            }
                        ?>>
                        <td 
                        <?php 
                                echo 'style="background:'.$task['Task']['task_color_code'].'"';
                            
                         ?>>
                        </td> 
                        <td>
                        <?php
                            if(in_array($task['Task']['team_id'], $controlled_teams)){
                                echo '<a href="#" class="eip_task_start_time" data-format = "yyyy-mm-dd hh:ii:ss" data-value ="'.date('Y-m-d H:i:s', strtotime($task['Task']['start_time'])). '" data-name="Task.start_time" data-type="datetime" data-pk="'.$task['Task']['id'].'">';
                                echo $this->Time->format('M d H:i', $task['Task']['start_time']);
                                echo '</a>-';
                                
                                echo '<a href="#" class="eip_task_end_time" data-format = "yyyy-mm-dd hh:ii:ss" data-value ="'.date('Y-m-d H:i:s', strtotime($task['Task']['end_time'])). '" data-name="Task.end_time" data-type="datetime" data-pk="'.$task['Task']['id'].'">';
                                //echo '<br/>';
                                echo $this->Time->format('H:i', $task['Task']['end_time']);
                                echo '</a>';
                                echo '&nbsp;&nbsp;';       
                            }
                            
                            else{
                                echo $this->Time->format('M d H:i', $task['Task']['start_time']);
                            }
                                
                            $time1 = strtotime($task['Task']['start_time']);
                            $time2 = strtotime($task['Task']['end_time']);
                            $diff = $time2 - $time1;

                            // NOTE: LIMITATION Hides for duration less than one min.  May impact PRO
                            // since their events last seconds [for everyone else though, it makes sense]
                            if((60 < $diff)  && ($diff <= 3599)){
                                echo '<br/>('.gmdate("i", $diff).' min)';
                            }
                            elseif($diff > 3599){
                                echo '<br/>('.gmdate('H', $diff).' hr, '.gmdate('i',$diff).' min)';  
                            }
                        ?>
                        </td>

                        <td>
                        <?php 
                            if(in_array($task['Task']['team_id'], $controlled_teams)){
                                echo '<a href="#" class="eip_task_type" data-value="'.$task['Task']['task_type_id'].'" data-name="Task.task_type_id" data-type="select" data-pk="'.$task['Task']['id'].'">';
                                echo '<b>'.$task['Task']['task_type'].'</b>';
                                echo '</a><br/>';    
                               
                            }
    
                            else{
                                echo '<b>'.$task['Task']['task_type'].'</b><br/>';
                            }                     

                            $tt = $task['TasksTeam'];
                            $tt_l = Hash::extract($tt, '{n}[task_role_id=1].team_code');
                            
                            
                            //$this->log($tt_l);
                            //$tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_code');
                            $tt_p = Hash::extract($tt, '{n}[task_role_id=2].team_id');
                            //$tt_r = Hash::extract($tt, '{n}[task_role_id=3].team_code');
                            $tt_r = Hash::extract($tt, '{n}[task_role_id=3].team_id');

                            $buttons = '';
                            
                            
                                foreach ($task['TasksTeam'] as $k => $tat) {
                                    if($tat['task_role_id'] == 1){
                                        $buttons.= '<span class="btn btn-leadt">'.$tat['team_code'].'</span>';
                                    }    
                                    
                                    elseif ($tat['task_role_id']==2) {
                                        $buttons.= '<span class="btn btn-default btn-xxs">'.$tat['team_code'].'</span>';
                                    }
    
                                    
                                    elseif ($tat['task_role_id']==3) {
                                        $buttons.= '<span class="btn btn-danger btn-xxs">'.$tat['team_code'].'</span>';
                                    }
                                }
                                                
                            
                            

                            
                            
                            
                            
                            
                            
                            
                            /*
                            if (!empty($tt_l)){
                                $buttons.= '<span class="btn btn-medgrey btn-xxs">'.$tt_l[0].'</span>';
                            }

                            foreach ($tt_n as $ttn){
                                $buttons.= '<span class="btn btn-success btn-xxs">'.$ttn['team_code'].'</span>';     
                            }

                            foreach ($tt_r as $ttr){
                                $buttons.= '<span class="btn btn-danger btn-xxs">'.$ttr['team_code'].'</span>';
                            }*/
                            //echo $buttons;
                            echo '<span class="btn btn-leadt">'.$tt_l[0].'</span>';
                            echo $this->Form->input('PushTeams', array(
                                        //'empty'=>true, 
                                        'id'=>'edit_input-conteam-select',
                                        
                                        'options'=>$teams, 
                                        'selected'=>$tt_p, 
                                        //'type'=>'select', 
                                        'placeholder'=>'Pushed To', 
                                        //'style'=> 'width:94%', 
                                        'multiple'=>true, 
                                        'class' => 'input-conteam-select'
                                        )
                                        ); 
                                        
                            echo $this->Form->input('ContributingTeams', array(
                                        //'empty'=>true, 
                                        'id'=>'edit_input-conteam-select',
                                        
                                        'options'=>$teams, 
                                        'selected'=>$tt_r, 
                                        //'type'=>'select', 
                                        'placeholder'=>'Request Help', 
                                        //'style'=> 'width:94%', 
                                        'multiple'=>true, 
                                        'class' => 'input-conteam-select'
                                        )
                                        ); 
                            
                            
                        ?>
                        </td>

                        <td>
                        <?php
                            if(in_array($task['Task']['team_id'], $controlled_teams)){
                                echo '<a href="#" class="eip_short_description" data-value="'.$task['Task']['short_description'].'" data-name="Task.short_description" data-type="text" data-pk="'.$task['Task']['id'].'">';
                                echo $task['Task']['short_description'];
                                echo '</a>';    
                            }
                            else{
                                echo $task['Task']['short_description'];
                            } 

                            if ($show_details && !empty($task['Task']['details'])){
                                echo '<hr style="margin-bottom:2px; margin-top:3px; border-top: 1px solid #444;"/>';
                                echo '<u>Details:</u><br/>'; 
                                echo nl2br($task['Task']['details']);
                            }
                                
                        ?>
                        </td>
                        <td>
                            <?php 
                                if($hasChange && $hasNewChange){
                                    echo '<b><i class="fa fa-exchange fa-lg highlight-new"></i></b>&nbsp;<small>'.$numChange.' New';
                                    echo '</small><br/>';                                    
                                }
                                if($hasAttachment && $hasNewAttachment){
                                    echo '<b><i class="fa fa-paperclip fa-lg highlight-new"></i></b>&nbsp;<small>'.$numNewAttachment.' New';
                                    echo '</small><br/>';                
                                }
                                elseif($hasAttachment && !$hasNewAttachment){
                                    
                                    echo '<b><i class="fa fa-paperclip fa-lg text-muted"></i></b>&nbsp;<small>'.$numAttachment.' File';
                                    echo '</small><br/>';
                                }
                                if($hasDueDate && $hasDueSoon){
                                    echo '<b><i class="fa fa-clock-o fa-lg highlight-duesoon"></i></b>&nbsp;<small>'.date('M d', strtotime($task['Task']['due_date'])).'</small><br/>';
                                }
                                if($hasActionable){
                                    echo '<b><i class="fa fa-flag fa-lg highlight-duesoon"></i></b>&nbsp;<small>'.$task['Task']['actionable_type'].'</small><br/>';
                                }

                            ?>
                        </td>
                        <td>
                                                        <div class="btn-group">
                                <?php echo $this->Html->link(__('View'), array('controller'=>'tasks', 'action' => 'view', $task['Task']['id']), array('class' => 'btn btn-default btn-xs')); ?>
                                <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown">
                                    <span class="caret"></span>
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <?php echo $this->Html->link('View', array('controller'=>'tasks', 'action'=>'view', $task['Task']['id'])); ?>
                                    </li>
                                    <?php if(in_array($task['Task']['team_id'], $controlled_teams)):?>
                                    <li><?php echo $this->Html->link(__('Edit'), array('controller'=>'tasks', 'action' => 'edit', $task['Task']['id'])); ?></li>
                                    <?php endif; 
                                    if ($user_role >= 200 || in_array($task['Task']['team_id'], $controlled_teams)  ):?>
                                        <li class="divider"></li>
                                        <li><?php echo $this->Form->postLink(__('Delete'), array('controller'=>'tasks', 'action' => 'delete', $task['Task']['id']), null, __('Are you sure you want to delete this task? This CANNOT BE UNDONE!')); ?></li>  
                                    <?php endif;?> 
                                </ul>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div><!-- /.table-responsive -->
        <p><small>
            <?php
                echo $this->Paginator->counter(array(
                    'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
                ));
            ?>
        </small></p>
        <ul class="pagination">
            <?php
                echo $this->Paginator->prev('< ' . __('Previous'), array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
                echo $this->Paginator->numbers(array('separator' => '', 'currentTag' => 'a', 'tag' => 'li', 'currentClass' => 'disabled'));
                echo $this->Paginator->next(__('Next') . ' >', array('tag' => 'li'), null, array('class' => 'disabled', 'tag' => 'li', 'disabledTag' => 'a'));
            ?>
        </ul><!-- /.pagination -->
    </div><!-- /.index -->
            
    <?php  }
        else {
            echo 'No tasks matched your search parameters.  Please try refining your search terms.';
        }
    ?>
</div>	    

<?php //echo $this->Js->writeBuffer(); ?>
