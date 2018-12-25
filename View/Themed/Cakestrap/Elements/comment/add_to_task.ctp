<?php
    if(!isset($tid)){
        $tid = $task['Task']['id'];    
    }

    if (AuthComponent::user('id')){
        $user_id = AuthComponent::user('id'); 
        $user_handle = AuthComponent::user('handle');
        $user_role = AuthComponent::user('user_role_id');
        $user_teams = AuthComponent::user('TeamsList');
    }
    
    $this->Js->buffer("
        $('body').on('submit','#acAddTo".$tid."', function(e){
            var subBut = $(this).find('.acSubmitButton');
            var valCont = $(this).find('.acValidationContent');
            var spinner = $(this).find('.acSpinner');
            var comment = $(this).find('.comment-text');
            var comment_text = comment.code();
            
            if(comment_text == '' || comment_text == '<p><br></p>'){
                return false;    
            }
            
            
            $.ajax( {
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: 'post',
                dataType:'html',
                beforeSend:function () {
                    valCont.fadeOut('fast');                
                    subBut.val('Saving...');
                    subBut.attr('disabled','disabled');
                    spinner.fadeIn();
                },
                success:function(data, textStatus) {
                    $('#acAddTo".$tid."').trigger('reset');
                    spinner.fadeOut('fast');
                    // Refresh
                    $('#cbt".$tid."').load('/comments/byTask/".$tid."', function(response, status, xhr){
                        if(status == 'success'){
                            $('#cbt".$tid."').html(response);
                        }
                    });
                },
                complete:function (XMLHttpRequest, textStatus) {
                    spinner.fadeOut('fast');
                    subBut.val('Post Comment');
                    subBut.attr('disabled',false);
                },
                error: function(xhr, statusText, err){
                    valCont.html(xhr.responseText).fadeIn('fast');
                }
            });
            e.preventDefault();
            e.stopImmediatePropagation();
            return false;
        });

        $('#acText".$tid."').summernote({
        height: 100,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol']],
            ['insert', ['link']],
            ['misc', ['undo','redo','help']],
        ]
      });

        $('a.clear-comment').on('click', function(e){
            e.preventDefault();
            var ctext = $('#acText".$tid."');
            ctext.code('');
            return false;
        });

    ");


?>

    <?php echo $this->Form->create('Comment', array(
        'action'=>'addTo',
        'class'=>'formAddToTask',
        'type'=>'post',
        'data-tid'=> $tid, 
        'id'=>'acAddTo'.$tid, 
        'novalidate' => true,
        'inputDefaults' => array(
            'label' => false), 
        'role' => 'form')); 
    ?>

            
<div class="row" id="acTask<?php echo $tid;?>">
    <div class="col-xs-12">
        <div class="row sm-bot-marg">
            <div class="col-xs-12">
                <?php 
                    echo $this->Form->input('task_id', array(
                        'value'=>$tid,
                        'id'=>'acTaskId'.$tid,
                        'type'=>'hidden')); 
                    
                    echo $this->Form->input('user_id', array(
                        'type'=>'hidden',
                        'value'=>$user_id,
                        'class' => 'form-control'));
                ?>
                
                <div class="row sm-bot-marg"> 
                    <div class="col-xs-12">
                        <?php echo $this->Form->input('text', array(
                            'id'=>'acText'.$tid,
                            'placeholder'=>'Add comment here', 
                            'class'=>'comment-text input-details form-control', 
                            'type' => 'textarea')); 
                        ?>
                    </div>
                </div>
                
                <div class="row sm-bot-marg">
                    <div class="col-xs-12 col-md-7">
                        <?php 
                            //echo '<span class="pull-right">';
                            echo $this->Form->submit('Post Comment', array(
                                'id'=>'acSubmitButton_'.$tid, 
                                'div'=>false, 
                                'class' => 'acSubmitButton submit btn btn-large btn-success'));
                        
                            echo '&nbsp;&nbsp;';
                            echo $this->Html->link('Cancel', array(
                                'action'=>'compile'), 
                                array(
                                    'id'=>'cBox'.$tid,
                                    'class'=>'clear-comment btn btn-large btn-danger'));
                            echo '&nbsp;&nbsp;';
                            echo '<span class="acSpinner" style="display: none;"><i class="fa fa-cog fa-lg fa-spin"></i></span>'; 
                       ?>
                    </div>
                    <div class="col-xs-12 col-md-5">
                        <div class="pull-right">
                            <small><i class="fa fa-user"></i>&nbsp;Posted by <?php echo $user_handle; ?></small>
                        </div>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-xs-12">
                            
                            <div class="acValidationContent" id="acvalidation_content_<?php echo $tid?>"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!--col-md-9-->
    </div><!--row-->

<?php
    echo $this->Form->end(); 
    echo $this->Js->writeBuffer(); 
?>  