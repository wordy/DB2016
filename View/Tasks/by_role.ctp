<?php
    echo $this->Html->script('compile');
    $this->set('title_for_layout', 'Tasks');

    // Teams currently being shown via compile settings 
    $cs_teams = array();
    if(!empty($cSettings['Teams'])){
        foreach ($cSettings['Teams'] as $tid){
            $cs_teams[] = $teamIdCodeList[$tid];
        }
    }
    // Current URL
    $cURL = $this->params->here;
        
    $this->Js->buffer("
    
    
    
    ");

?>
<div class="container-fluid">  
    <div id="page-content" class="row">
        <div id="taskListWrap">
            <?php echo $this->element('task/compile_by_role');?>
        </div>
        <div id="taskLegend">
            <?php //echo $this->element('task/task_legend'); ?>
        </div>
    </div>
</div>

<?php echo $this->Js->writeBuffer(); ?>