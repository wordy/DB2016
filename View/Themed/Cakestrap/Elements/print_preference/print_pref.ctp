<?php
    $this->set('title_for_layout', 'Print Preferences');

    $show_details = $this->Session->read('Auth.User.Compile.show_details');
    $filter_due_date = $this->Session->read('Auth.User.Compile.filter_due_date');
    
    $this->Js->buffer("
    
    $('body').on('click', '.ppDelete', function(){
        var result = confirm('Are you sure you want to reset ALL your preferences?');
            
        if(result){
            return true;
        }
        return false;
    });
    
    $('i.fa-print').each(function(i, element){
        var hide_task = $(this).data('hide_task');
        if(hide_task == 1){
            $(this).after('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
        }
    });
    
    $('i.fa-list-ul').each(function(i, element){
        var hide_detail = $(this).data('hide_detail');
        if(hide_detail == 1){
            $(this).after('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
        }
    });

    $('body').on('click', '.cpDet, .cpPrint', function(){
        var tspan = $(this);
        var sib_span = $(this).siblings();
        var cp_title = $('#cpTitle');
        var clicked = $(this).data('action');
        var tid = $(this).find('i').data('tid'); 
        var par_div = $(this).parent('div.task-buttons');
        var cur_hd = par_div.find('span.cpDet i').data('hide_detail');
        var cur_ht = par_div.find('span.cpPrint i').data('hide_task');
        var new_ht;
        var new_hd;
        var spin = $('#global-busy-indicator');

        if(clicked == 'print'){
            new_ht = (cur_ht == 0 ? 1 : 0);
        }
        else if(clicked == 'details'){
            new_hd = (cur_hd == 0 ? 1 : 0);
        }
        if(cur_hd == 1 || cur_ht == 1){
            par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-muted').removeClass('task-panel-heading-nodet');    
        }
        
        if(new_hd == 1 && new_ht == 0){
            par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-muted').addClass('task-panel-heading-nodet');    
            tspan.append('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
            if(sib_span){
                sib_span.eq(0).find('i.text-danger').remove();
            }
        }
        else if(new_ht == 1){
            par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-nodet').addClass('task-panel-heading-muted');
            tspan.append('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');

            if(sib_span){
                sib_span.eq(0).find('i').data('hide_detail',1);
                sib_span.eq(0).append('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
            }
        }
        else if(new_hd == 0 && new_ht == 1){
        }
        else if (new_ht == 0 && new_hd == 0){
            par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-nodet').removeClass('task-panel-heading-muted');
            tspan.find('i.text-danger').remove();

            if(sib_span){
                sib_span.eq(0).find('i.text-danger').remove();
            }
        }
        
        data = {'task':tid, 'hide_detail':new_hd, 'hide_task':new_ht};
        spin.fadeOut('fast');
        $.ajax( {
            data: data,
            url: '/print_prefs/changePref',
            beforeSend:function () {
                spin.fadeIn('fast');
            },
            success:function(data, textStatus) {
                par_div.find('span.cpPrint i').data('hide_task', data.hide_task);
                par_div.find('span.cpDet i').data('hide_detail', data.hide_detail);
                if(data.hide_detail == 1 && data.hide_task == 0){
                    par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-muted').addClass('task-panel-heading-nodet');    
                    tspan.append('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
                    if(sib_span){
                        sib_span.eq(0).find('i.text-danger').remove();
                    }
                }
                else if(data.hide_task == 1){
                    par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-nodet').addClass('task-panel-heading-muted');
                    tspan.append('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
                    if(sib_span){
                        sib_span.eq(0).append('<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>');
                    }
                }
                else if (data.hide_task == 0 && data.hide_detail == 0){
                    par_div.closest('div.task-panel-heading').removeClass('task-panel-heading-nodet').removeClass('task-panel-heading-muted');
                    tspan.find('i.text-danger').remove();
                    if(sib_span){
                        sib_span.eq(0).find('i.text-danger').remove();
                    }
                }
            },
            complete:function (XMLHttpRequest, textStatus) {
                spin.fadeOut('fast');
            },
            error: function(xhr, statusText, err){
                if(xhr.status == '401'){
                    var res_j = xhr.responseText;
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>Can\'t ignore that task. It is probably already ignored. Please refresh the page.</div>';
                    $('#ppErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                }
                else{
                    var msg = '<div class=\"alert alert-danger\" role=\"alert\"><b>Error: </b>'+err+'</div>';
                    $('#ppErrorStatus').html(msg).fadeIn('fast').delay(3000).fadeOut();
                }
            },
            type: 'post',
            dataType:'json',
        });
    });
        
        $('div.flash-success').delay(3000).fadeOut();
        
        $('button.pdfBut').on('click', function(e){
            var but = $(this);
            but.prop('disabled', true);
            
            setTimeout(function (){
                but.prop('disabled', false);
            }, 5000);
        });
   ");
?>
    <style>
        a.printButton:hover{
            text-decoration: none;
        }
    </style>
   
    <div class="row">
        <div class="col-md-12">
            <h1 id="cpTitle"><i class="fa fa-print"></i> Customize Printed Plan &nbsp;</h1>
            <p>Customize the tasks that appear in <b>your</b> printed plan. These settings are saved by user, even after logging out. Use this when you're ready to prepare your printed plan.</p>
            <div class="alert alert-info hidden-print">
                <p><b>Note: </b>What appears below is your current plan as set by your <b>Compile &amp; View Options.</b></p>
            </div>
        </div>
    </div>
    <div class="row hidden-print">
        <div class="col-sm-7">
            <ul>
                <li>Select <i class="fa fa-print fa-lg"></i> to hide the task entirely</li>  
                <li>Select <i class="fa fa-list-ul fa-lg"></i> to hide ONLY the task details <span class="text-info">(Only available if the task has details)</span></li>
                <li><b>Download PDF</b> generates a PDF for you. May take ~2 minutes for download to start.</li>
                <li><b>View in Browser</b> to print yourself, export to PDF yourself, or import into MS Word, etc.</li>
                <li><b>Plain Text</b> is useful for pasting into MS Excel. Text formatting of details may be lost.</li>
                <li>Delete all your preferences by clicking "Delete ALL Prefs" (Careful!)</li>
            </ul>
        </div>
        <div class="col-sm-5 well hidden-print">
                <div class="row">
                    <div class="col-md-6">
                    <?php
                        echo $this->Html->link(
                            '<button type="button" class="btn btn-block btn-primary sm-bot-marg pdfBut"><i class="fa fa-file-pdf-o"></i> Download PDF</button>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'compile',
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
                    <?php
                        echo $this->Html->link(
                            '<button type="button" class="btn btn-info btn-block sm-bot-marg"><i class="fa fa-desktop"></i> View In Browser</button>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'compile',
                                '?'=>array(
                                    'view'=>'plain'
                                ),
                            ),
                            array(
                                'escape'=>false,
                                'class'=>'printButton',
                            )
                        );
                    ?> 
                    <?php
                        echo $this->Html->link(
                            '<button type="button" class="btn btn-default btn-block sm-bot-marg"><i class="fa fa-file-excel-o"></i> Plain Text (Excel)</button>',
                            array(
                                'controller' => 'tasks',
                                'action' => 'compile',
                                '?'=>array(
                                    'view'=>'excel'
                                ),
                            ),
                            array(
                                'escape'=>false,
                                'class'=>'printButton',
                            )
                        );
                    ?> 
                </div>
                <div class="col-md-6">
                    <?php
                    echo $this->Html->link(
                        '<button type="button" class="btn btn-block btn-danger ppDelete"><i class="fa fa-times"></i> Delete ALL Prefs</button>',
                        array(
                            'controller' => 'print_prefs',
                            'action' => 'resetPref',
                        ),
                        array(
                            'escape'=>false,
                            'class'=>'printButton',
                            
                        )
                    );
                ?>  
                </div>
            </div>    
        </div>
    </div>

    <div class="row">
        <div class="col-md-8 col-md-offset-2" id="ppErrorStatus">
            <?php echo $this->Session->flash('print_pref'); ?>
        </div>
    </div>


    <div class="row">
        <div id="printTaskListWrap">
            <?php 
                echo $this->element('print_preference/print_pref_list', array(
                    'tasks'=>$tasks,
                    'PrintPrefs'=>$PrintPrefs,
                ));
            ?>
        </div>        
    </div>
