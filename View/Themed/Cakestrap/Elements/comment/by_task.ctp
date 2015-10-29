<?php
    if (AuthComponent::user('id')){
        $user_id = AuthComponent::user('id'); 
        $user_handle = AuthComponent::user('handle');
        $user_role = AuthComponent::user('user_role_id');
        $user_teams = AuthComponent::user('TeamsList');
    }
    
    // if we dont have data, go get it with requestAction
    if(empty($comments)){
        $data = $this->requestAction(array(
            'controller' => 'comments', 
            //'action' => 'pageComments', $tid)
            'action' => 'byTask', $tid)
        ); 
        
        $comments = $data['comments'];
        //$paging = $data['paging']['Comment'];
    }
    
    // User "controls" data.  Used to assess whether they can delete a comment
    $uCon = array(
        'role'=>$user_role,
        'teams'=>array_keys($user_teams),
        'handle'=>$user_handle
    );
    
    $this->Js->buffer("
    
        function deleteComment(cid){
            if(cid !=null){        
                $.ajax( {
                    url: '/comments/delete/',
                    data: {cid:cid},
                    type: 'post',
                    dataType:'json',
                    success:function(data, textStatus) {
                        var tid = $('#ajax-content-load').find('#commentBody'+cid).data('tid');
                        $('#ajax-content-load').find('#commentBody'+cid).fadeOut();
                        $('#ajax-content-load').find('#commentBody'+cid).parents('.panel-tcom').parents('row').remove();
                        var cbody = $('#ajax-content-load').find('#commentBody'+cid).parents('.panel-body').find('.panel-tcom');
                    },
                });
            }    
        }
    
        $('.deleteComment').on('click', function(){
            var cid = $(this).data('cid');
            var cd_modal = $('#modalConfirmDelete');
            cd_modal.find('.hiddenCID').html(cid);
            cd_modal.modal('show');
        });
    
        $('#modalConfirmDelete').on('show.bs.modal', function(e) {
            var doDel = $(this).find('.btn-doCommentDelete');
            var cid = $(this).find('.hiddenCID').html();
            doDel.on('click', function(e){
                $(this).off(e);
                
                if(cid){
                    deleteComment(cid);
                    $('#modalConfirmDelete').modal('hide');
                }
            });
        });
    ");
?>

<div id="cbt<?php echo $tid?>" data-tid="<?php echo $tid?>">
    <div class="row">
        <div class="col-xs-12">
            <div class="panel panel-dark">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-comments-o"></i> Comments</h3>
                </div>
                <div class="panel-body">
                    <?php 
                        if (!empty($comments)){
                            echo '<div class="row"><div class="col-xs-12">';

                            foreach($comments as $com){ 
                                echo $this->Ops->commentByTaskWithDelete($com, $team, $uCon);
                            }
                            echo '</div></div>';
                        }
                        else{?>
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="alert alert-info slim-alert" role="alert" style="margin-bottom:0px;">
                                        <i class="fa fa-meh-o"></i> <b>Nothing Here: </b>No comments have been posted for this task.
                                    </div>
                                </div>
                            </div>    
                        <?php                            
                        }
                    ?>
                </div><!--panel body-->
                <div class="panel-footer">
                    <?php
                        echo $this->element('comment/add_to_task', array('tid'=>$tid));
                    ?>
                </div>
            </div><!--panel-->
        </div><!--col-md-9-->
    </div><!--row-->
    <div class="row">
        <div class="modal fade" id="modalConfirmDelete" tabindex="-1" role="dialog" aria-labelledby="modalConfirmDeleteLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="modalConfirmDeleteLabel">Confirm Comment Delete</h4>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this comment?  This cannot be undone!</p>
                        <span class="hiddenCID" style="display: none"></span>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <a class="btn btn-danger btn-doCommentDelete"><i class="fa fa-trash-o"></i> Delete Comment</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Comment styling from http://bootsnipp.com/snippets/featured/user-comment-example -->

<?php
    echo $this->Js->writeBuffer(); 
?>  