<?php
    echo $this->Html->script('compile');
    $this->set('title_for_layout', 'Tasks');

    $show_details = $this->request->data('Task.show_details');
    $single_task = (isset($single_task))? $single_task : 0; 
     
    // Teams currently being shown via compile settings 
    $cs_teams = array();
    if(!empty($cSettings['Teams'])){
        foreach ($cSettings['Teams'] as $tid){
            $cs_teams[] = $teamIdCodeList[$tid];
        }
    }
    // Current URL
    $cURL = $this->params->here;
    $query = array();
    if($single_task){
        $this->Js->buffer("var single_task_id = '".$single_task."';"); 
        
        $query['task'] = $this->request->query('task');
    }
    else{
        $this->Js->buffer(" var single_task_id = null;"); 
    }
    
    // If results are from a user search, highlight the term
    if(isset($search_term)){
        $this->Js->buffer("$('body').highlight('".$search_term."');");
    }
    
    if(!empty($query)){
        $cURL .= '?'.http_build_query($query);    
    }
        
    $this->Js->buffer("
    
    
    
    var cURL = '".$cURL."';
    //console.log(cURL);
    
    // Back To Top
    var offset = 420;
    var duration = 700;
    $(window).on('scroll', function() {
        if($(this).scrollTop() > offset){ $('#back-to-top').fadeIn(duration);}
        else{ $('#back-to-top').fadeOut(duration);}
    });
                
    $('#back-to-top').on('click', function(){
        $('html, body').animate({scrollTop : 0}, duration);
        return false;
    });
    
    //$('.helpTTs').popover({container: 'body', html:true,});
    
    // Accordion toggle for menu   
    $('#compActMenu').on('hidden.bs.collapse', toggleCaChevron);
    $('#compActMenu').on('shown.bs.collapse', toggleCaChevron);     
    ");
    

    
    
    
    
    

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="cErrorStatus">
            <?php echo $this->Session->flash('compile');?>
        </div>
    </div> 

    <!-- Accordion Credit: http://jsfiddle.net/d2p17qj7/ -->    
    <div class="row hidden-print">
        <div class="panel-group" id="compActMenu">
            <div class="panel panel-bsuccess">
                <div id="colAddHeading" class="panel-heading panel-ctab accordion-toggle" data-toggle="collapse" data-parent="#compActMenu" href="#colAdd">
                    <h4 class="panel-title">          
                        <i class="fa fa-plus"></i>  &nbsp;&nbsp;Add Task 
                        <i class="cAindicator fa fa-chevron-down fa-fw pull-right"></i>
                    </h4>
                </div>
                <div id="colAdd" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php echo $this->element('task/quick_add'); ?>
                    </div>
                </div>
            </div>
            <div class="panel panel-bsteel">
                <div class="panel-heading panel-ctab accordion-toggle" data-toggle="collapse" data-parent="#compActMenu" href="#colCompOpts">
                    <h4 class="panel-title">
                        <i class="fa fa-sliders"></i>  &nbsp;&nbsp;Compile &amp View Options
                        <i class="cAindicator fa fa-chevron-down fa-fw pull-right"></i>
                    </h4>
                </div>
                <div id="colCompOpts" class="panel-collapse collapse">
                    <?php echo $this->element('task/compile_options'); ?>
                </div>
            </div>
        </div>
    </div>

    <div id="page-content" class="row">
        <div id="taskListWrap">
            <?php 
                echo $this->element('task/compile_screen');
            ?>
        </div>
    </div>
</div>
<!-- Modals -->
<div class="modal fade" id="deleteTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="deleteTaskModalLabel">Task Deletion Warning</h4>
      </div>
      <div class="modal-body">
        <p>Are you sure you wish to delete this task:</p>
        <p class="deleteTaskDesc"></p>
        <p>If you choose to continue:</p>
        <ul>
            <li>All teams will be removed</li>
            <li>Any linked tasks will be unlinked (but not deleted)</li>
            <li>Any requests (open or closed) to other teams will be removed</li>
        </ul>
        <p>Deleting tasks is <b>permanent</b> and <u>cannot be undone!</u></p>
        <span style="visibility: hidden;" id="deleteTaskId"></span>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
        <button type="button" class="btn btn-danger btn-doDelete"><i class="fa fa-trash-o"></i> Delete Task</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="dueDateTaskModal" tabindex="-1" role="dialog" aria-labelledby="deleteTaskModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="dueDateTaskModalLabel">Set Due Date As End Time?</h4>
            </div>
            <div class="modal-body">
                <p>You're setting a due date for this task. Would you like to set the due date as the task's end date also?</p>
                <p>By default, task's run from when they're created, until their due date. Associated teams will also be notified when due dates and end dates are coming up.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-danger btn-doDelete"><i class="fa fa-trash-o"></i> Delete Task</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="teamAddLinkedModal" tabindex="-1" role="dialog" aria-labelledby="teamAddLinkedModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="teamAddLinkedModalLabel">Choose Team for New Linked Task</h4>
            </div>
            <div class="modal-body">
                <p>Multiple teams can link to this task. Which team do you want to link as?</p>
                <div class="form-group">
                    <select class="form-control" id="selectLinkedTeam"></select>
                </div>
                <span class="hiddenParentId collapse" style="visibility: hidden;"></span>
                <span class="hiddenTid collapse" style="visibility: hidden;"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Cancel</button>
                <button type="button" class="btn btn-success btn-doLink"><i class="fa fa-plus-circle"></i> Add Task</button>
            </div>
        </div>
    </div>
</div>


<?php echo $this->Js->writeBuffer(); ?>