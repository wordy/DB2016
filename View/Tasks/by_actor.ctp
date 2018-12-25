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
                        <i class="fa fa-sliders"></i>  &nbsp;&nbsp;View Options
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
            <?php echo $this->element('task/compile_by_actor');?>
        </div>
        <div id="taskLegend">
            <?php //echo $this->element('task/task_legend'); ?>
        </div>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>