<?php
    $this->set('title_for_layout', 'Event Info');
    
    echo $this->Html->css('eventinfo.css', array('inline'=>false));
    
    if(AuthComponent::user('id')){
        $this->Js->buffer("
        $('#cancelEdit').on('click', function(){
            //$('.editableInfo').destroy();
            location.reload();
            
            //$('#cancelEdit').addClass('hidden');
            //$('#makeEditable').text('Enable Editing').removeClass('btn-success').addClass('btn-primary');
        });
        
        $('#makeEditable').on('click', function(){
            var button = $(this);
            if($(this).text() == 'Enable Editing'){
                $(this).text('Save Changes').removeClass('btn-primary').addClass('btn-success');
                $('.editableInfo').summernote({
                    /*
                    onChange: function(contents, editable) {
                        console.log('onChange:', contents, editable);
                    }*/
                });
                $('#cancelEdit').removeClass('hidden');
            }
            else{
                $(this).text('Enable Editing').removeClass('btn-success').addClass('btn-primary');
                var eform = $('#eventInfoForm');
                var valCont = $('#eInfoVal');
                $(document).find('.editableInfo').each(function(){
                    var type = $(this).data('section');
                    var typedata = $(this).code();
                    $('<input>').attr({type: 'hidden', name: 'data[EventInfo]['+type+']', value: typedata}).appendTo(eform);
                });
    
                $.ajax( {
                    url: '".$this->Html->url(array('controller'=>'event_infos', 'action'=>'add'))."',
                    type: 'post',
                    data: eform.serialize(),
                    dataType:'json',
                    beforeSend:function () {
                        button.val('Saving...').attr('disabled', true);
                        //$('#cancelEdit').addClass('hidden');
                    },
                    success:function(data, textStatus) {
                        $('.editableInfo').destroy();
                        valCont.html('<span class=\"text-info\"><b>OK</b> '+data.message+'</span>').fadeIn('fast').delay(5000).fadeOut('fast');
                        $('#cancelEdit').addClass('hidden');
                    },
                    error: function(xhr, statusText, err){
                        var msg = $.parseJSON(xhr.responseText);
                        console.log(msg);
                        //console.log(xhr);
                        valCont.html('<span class=\"text-danger\"><b>ERROR:</b> '+msg.message+'</span>').fadeIn('fast').delay(5000).fadeOut('fast');
                        //console.log(xhr.responseText);
                    },                
                    complete:function (XMLHttpRequest, textStatus) {
                        button.val('Enable Editing').attr('disabled', false);
                    },
                });
            }
        });
    ");
    }
    
 

?>

<div class="row">

    <div class="col-md-4">
        <div class="db2014-sidebar affix" id="db2014nav">  
            <ul class="nav db2014-sidenav">
                <li><a href="#home">Home</a></li>
                
                <li><a href="#info">General Info</a>
                    <ul class="nav">
                        <li><a href="#info-schedule">Schedule</a></li>
                        <li><a href="#info-maps">Maps</a></li>
                    </ul>
                </li>

                <li><a href="#todo">Things To Do</a>
                    <ul class="nav">
                        <li><a href="#todo-rcphotobooth">Red Carpet Photobooth</a></li>
                        <li><a href="#todo-eventphotobooth">Photobooth</a></li>
                        <li><a href="#todo-booths">Booths</a></li>
                        <li><a href="#todo-entertainment">Entertainment</a></li>
                    </ul>
                </li>

                
                <li><a href="#prizes">Prizes</a>
                    <ul class="nav">
                        <li><a href="#prizes-table">Table Prizes</a></li>
                        <li><a href="#prizes-gr">Grand Raffle</a></li>
                    </ul>
                </li>

                <li><a href="#auction">Auction</a>
                    <ul class="nav">
                        <li><a href="#auction-sa">Silent Auction</a></li>
                        <li><a href="#auction-la">Live Auction</a></li>
                    </ul>
                </li>


                <li><a href="#food">Food</a>
                    <ul class="nav">
                        <li><a href="#food-gala">Gala Dinner</a></li>
                        <li><a href="#food-drinks">Beverages</a></li>
                        <li><a href="#food-booths">Food Booths</a></li>
                        <li><a href="#food-mb">Midnight Buffet</a></li>
                    </ul>
                </li>
            </ul>
            <?php if(AuthComponent::user('id')): ?>
                <br>
                <div class="row"><div class="col-xs-12 -top-marg"><button id="makeEditable" class="btn btn-primary">Enable Editing</button></div></div>
                <div class="md-top-marg sm-bot-marg" id="eInfoVal"></div>
                <div class="row"><div class="col-xs-12 lg-top-marg"><button id="cancelEdit" class="btn btn-danger hidden">Discard Changes</button></div></div>
            <?php endif;?>

        </div>
    </div>
    <div class="col-md-8">
        <div class="db2014-content">
            <a class="anchor" id="home"></a>
            <h1><?php 
                echo Configure::read('EventLongName');
                ?><small> Event Information</small></h1>
                
                
            <!--<div class="alert alert-danger">
                <b>NOTE: (Jan 4)</b><br/> In the days coming I'll be requesting information from various teams to fill in the sections of this site.  Also, information will change right up until event day, so this site will be updated as new information becomes available.
            </div>-->

            <a class="anchor" id="info"></a>
            <section>
                <p>This page will be updated with the most recently known information regarding <?php echo Configure::read('EventShortName');?>.</p>
            </section>
            
            <a class="anchor" id="info-schedule"></a>
            <section>
                <h2 class="text-yh"><i class="fa fa-calendar-o"></i> Schedule</h2>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Time</th>
                            <th>Event</th>
                        </tr>
                  </thead>
                  <tbody>
                        <tr>
                            <td>5:00pm</td>
                            <td>Red Carpet Reception Opens</td>
                        </tr>
                        <tr>
                            <td>6:00pm</td>
                            <td>General Registration Opens</td>
                        </tr>
                        <tr class="danger">
                            <td>6:30pm</td>
                            <td>Gala Doors Open</td>
                        </tr>
                        <tr class="danger">
                            <td>7:00pm</td>
                            <td>Photobooth Closes</td>
                        </tr>
                        <tr>
                            <td>7:00pm</td>
                            <td>Production Begins</td>
                        </tr>
                        <tr>
                            <td>7:58pm</td>
                            <td>Live Auction Begins</td>
                        </tr>
                        <tr>
                            <td>8:20pm</td>
                            <td>Dinner Service Begins</td>
                        </tr>
                        <tr class="success">
                            <td>10:00pm</td>
                            <td>Photobooth Reopens</td>
                        </tr>
                        <tr>
                            <td>10:20pm</td>
                            <td>Table Prize Pickup Opens</td>
                        </tr>
                        <tr>
                            <td>10:30pm</td>
                            <td>Silent Auction Closes</td>
                        </tr>
                        <tr class="info csevent">
                            <td>11:00pm</td>
                            <td>Silent Auction Re-Opens. </td>
                        </tr>
                        <tr>
                            <td>11:00pm</td>
                            <td>Last Chance to Submit Grand Raffle Tickets</td>
                        </tr>
                        <tr>
                            <td>11:10pm</td>
                            <td>Grand Raffle Draw in Gala Hall</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            <a class="anchor" id="info-maps"></a>
            <section class="lg-bot-marg">
                <h2 class="text-yh"><i class="fa fa-map-signs"></i> Maps</h2>
                <img src="http://cs.thebws.com/images/db2013-100level.png" width="80%" height="80%"/>
                <img src="http://cs.thebws.com/images/db2013-200level.png" width="80%" height="80%"/>
            </section>          

<?php 

echo $this->Form->create('EventInfo', array('id'=>'eventInfoForm', 'inputDefaults' => array('label' => false), 'role' => 'form')); ?>

<div class="editableInfo" data-section="entertainment"><?php echo $this->request->data['EventInfo']['entertainment']?></div>
<div class="editableInfo" data-section="prizes"><?php echo $this->request->data['EventInfo']['prizes']?></div>
<div class="editableInfo" data-section="food"><?php echo $this->request->data['EventInfo']['food']?></div>

<div class="editableInfo" data-section="auction"><?php echo $this->request->data['EventInfo']['auction']?></div>
<?php 
    echo $this->Form->end(); 
    
    
?>
</div>

</div>
</div>    



