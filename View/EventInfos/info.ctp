<?php
    $this->set('title_for_layout', 'Event Info');
    
    $this->layout = 'basic';
    
    //echo $this->Html->css('eventinfo.css', array('inline'=>false));
    $this->Js->buffer("
        $('#menu-toggle').click(function(e) {
            e.preventDefault();
            $('#wrapper').toggleClass('toggled');
        });
        
    ");
    
    
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
            if(!$(this).hasClass('editable')){
                $(this).toggleClass('editable');
                $(this).html('<i class=\"fa fa-save\"></i> Save Changes').removeClass('btn-primary').addClass('btn-success');
                $('.editableInfo').summernote({
                    toolbar: [
                        ['style', ['style','bold', 'italic', 'underline', 'strikethrough', 'clear']],
                        ['para', ['ul', 'ol']],
                        ['insert', ['table']],
                        ['code', ['codeview']],
                    ],
                    onImageUpload: function(customEvent, files) {
                        return false;
                    },
                });
                    
                $('#cancelEdit').removeClass('hidden');
            }
            else{
                $(this).html('<i class=\"fa fa-edit\"></i> Enable Editing').removeClass('btn-success').addClass('btn-primary');
                $(this).toggleClass('editable');
                
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
                        $('#cancelEdit').addClass('hidden');
                    },
                    success:function(data, textStatus) {
                        $('.editableInfo').destroy();
                        valCont.html('<div class=\"alert alert-success\"><b>OK</b> '+data.message+'</div>').fadeIn('fast').delay(5000).fadeOut('fast');    
                        $('#cancelEdit').addClass('hidden');
                    },
                    error: function(xhr, statusText, err){
                        if(xhr.responseText){
                            var emsg = $.parseJSON(xhr.responseText);
                            valCont.html('<div class=\"alert alert-danger\"><b>ERROR:</b> '+emsg.message+'</div>').fadeIn('fast').delay(5000).fadeOut('fast');
                        }
                    },                
                    complete:function (XMLHttpRequest, textStatus) {
                        button.val('<i class=\"fa fa-edit\"></i> Enable Editing').attr('disabled', false);
                    },
                });
            }
        });
    ");
    }
    
 

?>

<style>
    /*!
 * Start Bootstrap - Simple Sidebar HTML Template (http://startbootstrap.com)
 * Code licensed under the Apache License v2.0.
 * For details, see http://www.apache.org/licenses/LICENSE-2.0.
 * credit: http://ironsummitmedia.github.io/startbootstrap-simple-sidebar/
 */

/* Toggle Styles */

#wrapper {
    padding-left: 0;
    margin-top: -40px;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
}

#wrapper.toggled {
    padding-left: 250px;
}

#sidebar-wrapper {
    z-index: 1000;
    position: fixed;
    left: 250px;
    width: 0;
    height: 100%;
    margin-left: -250px;
    overflow-y: auto;
    background: #333;
    -webkit-transition: all 0.5s ease;
    -moz-transition: all 0.5s ease;
    -o-transition: all 0.5s ease;
    transition: all 0.5s ease;
}

#wrapper.toggled #sidebar-wrapper {
    width: 250px;
}

#page-content-wrapper {
    width: 100%;
    position: relative;
    padding: 15px;
}

#wrapper.toggled #page-content-wrapper {
    position: absolute;
    margin-right: -250px;
}

/* Sidebar Styles */

.sidebar-nav {
    position: absolute;
    top: 0;
    width: 250px;
    margin: 0;
    padding: 0;
    padding-top: 40px;
    list-style: none;
}

.sidebar-nav li {
    text-indent: 20px;
    line-height: 40px;
}

.sidebar-nav li a {
    display: block;
    text-decoration: none;
    color: #999999;
}

.sidebar-nav li a:hover {
    text-decoration: none;
    color: #fff;
    background: rgba(255,255,255,0.2);
}

.sidebar-nav li a:active,
.sidebar-nav li a:focus {
    text-decoration: none;
}

.sidebar-nav > .sidebar-brand {
    height: 65px;
    font-size: 18px;
    line-height: 60px;
}

.sidebar-nav > .sidebar-brand a {
    color: #999999;
}

.sidebar-nav > .sidebar-brand a:hover {
    color: #fff;
    background: none;
}

@media(min-width:768px) {
    #wrapper {
        padding-left: 250px;
    }

    #wrapper.toggled {
        padding-left: 0;
    }

    #sidebar-wrapper {
        width: 250px;
    }

    #wrapper.toggled #sidebar-wrapper {
        width: 0;
    }

    #page-content-wrapper {
        padding: 20px;
        position: relative;
    }

    #wrapper.toggled #page-content-wrapper {
        position: relative;
        margin-right: 0;
    }
}
</style>

<div id="wrapper">

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand"><a href="#">Event Information</a></li>
                <li><a href="#schedule"><i class="fa fa-calendar-o"></i> Schedule</a></li>
                <li><a href="#maps"><i class="fa fa-map-o"></i> Maps</a></li>
                <li><a href="#todo"><i class="fa fa-map-signs"></i> Things to Do</a></li>
                <li><a href="#prizes"><i class="fa fa-trophy"></i> Prizes</a></li>
                <li><a href="#food"><i class="fa fa-cutlery"></i> Food &amp; Beverages</a></li>
                <li><a href="#auction"><i class="fa fa-gavel"></i> Auction</a></li>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
<a id="home"><br><br></a>

            <h1><?php 
                echo Configure::read('EventLongName');
                ?> Event Information</h1>
                
            <a class="anchor" id="info"></a>
            <section>
                <p>This page will be updated with the most recently known information regarding <?php echo Configure::read('EventShortName');?>.</p>
            </section>

                        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle"><i class="fa fa-navicon"></i> Toggle Menu</a>
                        <?php if(AuthComponent::user('id')): ?>
                
                <button id="makeEditable" class="btn btn-primary"><i class="fa fa-edit"></i> Enable Editing</button>
                <button id="cancelEdit" class="btn btn-danger hidden"><i class="fa fa-close"></i> Discard Changes</button>
                <div class="row">
                    <div class="col-xs-12 sm-top-marg">
                        <div id="eInfoVal"></div>        
                    </div>
                </div>
                

            <?php endif;?>

<br>
<a id="schedule"><br><br></a>
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

<a id="maps"><br><br></a>
            <section class="lg-bot-marg">
                <h2 class="text-yh"><i class="fa fa-map"></i> Maps</h2>
                <img src="http://cs.thebws.com/images/db2013-100level.png" class="img-responsive"/>
                <img src="http://cs.thebws.com/images/db2013-200level.png" class="img-responsive"/>
            </section>          

<?php 

echo $this->Form->create('EventInfo', array('id'=>'eventInfoForm', 'inputDefaults' => array('label' => false), 'role' => 'form')); ?>
<a id="todo"><br><br></a>
<h2><span class="text-yh"><i class="fa fa-map-signs"></i> Things To Do</span></h2>
<div class="editableInfo" data-section="entertainment"><?php echo $this->request->data['EventInfo']['entertainment']?></div>

<a id="prizes"><br><br></a>
<h2><span class="text-yh"><i class="fa fa-trophy"></i> Prizes</span></h2>
<div class="editableInfo" data-section="prizes"><?php echo $this->request->data['EventInfo']['prizes']?></div>

<a id="food"><br><br></a>
<h2><span class="text-yh"><i class="fa fa-cutlery"></i> Food &amp; Beverages</span></h2>
<div class="editableInfo" data-section="food"><?php echo $this->request->data['EventInfo']['food']?></div>

<a id="auction"><br><br></a>
<h2><span class="text-yh"><i class="fa fa-gavel"></i> Auction</span></h2>
<div class="editableInfo" data-section="auction"><?php echo $this->request->data['EventInfo']['auction']?></div>
<?php 
    echo $this->Form->end(); 
    
    
?>
                    </div>
                </div>
            </div>
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->














   



