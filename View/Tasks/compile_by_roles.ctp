<?php 
    $start = isset($start_date)?:null;
    $end = isset($end_date)?:null;
    $rolesList = ($rolesList)?:array();
    $rolesByTeam = ($rolesByTeam)?:array();
    $userRoles = isset($userRoles)?$userRoles:array();

    $this->Js->buffer("
        $('#pdfCBRbutton').prop('disabled', true);
        
        $('#tbrTeams').multiselect({
            //includeSelectAllOption: true,
            enableClickableOptGroups: true,
            //enableFiltering: true,
            preventInputChangeEvent: true,
            buttonClass: 'btn btn-primary', 
            buttonWidth: '100%',
            numberDisplayed: 3,
            maxHeight: 200,
        });
        
        $('body').on('change', $('#tbrTeams'), function(e){
            var selected = [];       
            
            $(this).find('option:selected').each(function(){
                selected.push($(this).val());                
            });
                
            if(selected.length>0){
                $.ajax( {
                    url: '/tasks/byRole',
                    type: 'post',
                    data: {SelectedRoles: selected},
                    dataType:'html',
                    beforeSend:function(){
                    },
                    success:function(data, textStatus) {
                        $('#ajaxTasksByRole').html(data).fadeIn('fast');
                        $('#pdfCBRbutton').prop('disabled', false);
                    },
                    error: function(xhr, statusText, err){
                    },                
                    complete:function (XMLHttpRequest, textStatus) {
                    },
                });
            }
            else{
                $('#pdfCBRbutton').prop('disabled', true);
                $('#ajaxTasksByRole').html('').fadeOut('fast');
            }
       }); 
    ");
          //$this->start('scriptTop');
            //echo "<script> $('body').on('hidden.bs.modal', '.modal', function () {\$(this).removeData('bs.modal');});</script>";
        //$this->end();  

        /*<div class="alert alert-danger hidden-print"><span class="lead"><b>**DEMO MODE**</b> Currently showing DB2018's tasks. This will change to DB2019 shortly.</span></div>*/ 
        
            
?>
            
<div class="row main-container">
    <div class="col-sm-12">
        <h1 class="hidden-print"><i class="fa fa-vcard-o"></i> Create Customized Plan By Role</h1>
        <p class="lead hidden-print">Choose one or more roles to generate a customized plan with their assigned tasks.</p>
        <?php if(empty($userRoles)):?>
            <div class="row hidden-print">
                <div class="col-sm-5 col-md-3">
                    <div class="form-group">
                    <?php
                        //echo $this->Form->label('SelectedRoles', 'Select Roles');
                        echo $this -> Form -> input('SelectedRoles', array(
                            'type' => 'select', 
                            'class' => 'cbr-select-teams', 
                            'multiple' => true,
                            'label'=>false, 
                            'div' => false, 
                            'options' => $rolesByTeam, 
                            'id' => 'tbrTeams'
                        ));
                    ?>
                    </div>
                </div>
                <div class="col-sm-4 col-md-2">
                    <?php
                        echo $this->Html->link(
                            '<button type="button" class="btn btn-block btn-success" id="pdfCBRbutton"><i class="fa fa-file-pdf-o"></i> Download PDF</button>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'byRole',
                                '?'=>array(
                                    'view'=>'pdf'
                                ),
                            ),
                            array(
                                'escape'=>false,
                                'class'=>'printButton',
                            )
                        );
                    ?>
                </div>
            </div>
        <?php endif;?>
<hr class="hidden-print">
        <div class="row">
            <div class="col-xs-12">
                <div id="ajaxTasksByRole">
                    <?php //echo $this->element('/task/tasks_table_by_role', array('tasks', $tasks));?>
                </div>
            </div>
        </div>
    </div>
</div><!--end container-->



<?php echo $this->Js->writeBuffer();?>


