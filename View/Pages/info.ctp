<?php

    //$this->layout="public";
    $this->set('title_for_layout', 'Event Info');
    
    echo $this->Html->css('eventinfo.css', array('inline'=>false));

?>

<div class="row">

    <div class="col-md-3">
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
        </div>
    </div>
    <div class="col-md-9">
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
                <h2>Information</h2>
                <p>This page will be updated with the most recently known information regarding DB2015.</p>
            </section>
            
            <a class="anchor" id="info-schedule"></a>
            <section>
                <h3>Schedule</h3>
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
            <section>
                <h3>Maps</h3>
                <img src="http://cs.thebws.com/images/db2013-100level.png" width="80%" height="80%"/>
                <img src="http://cs.thebws.com/images/db2013-200level.png" width="80%" height="80%"/>
            </section>          

            <a class="anchor" id="todo"></a>
            <section>
                <h2>Things To Do</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>
            
            <a class="anchor" id="todo-rcphotobooth"></a>
            <section>
                <h3>Red Carpet Photobooth</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>
            
            <a class="anchor" id="todo-eventphotobooth"></a>    
            <section>
                <h3>Event Photobooth</h3>
                <ul>
                    <li>Patrons can come by and have their photos taken.  They will recieve a copy of their photos to take home. (No charge)</li>
                </ul>
            </section>

            <a class="anchor" id="todo-booths"></a>
            <section>
                <h3>Booths</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="todo-entertainment"></a>
            <section>
                <h3>Entertainment</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="prizes"></a>            
            <section>
                <h2>Prizes</h2>
            </section>
            
            <a class="anchor" id="prizes-table"></a>            
            <section>
                <h3>Table Prizes</h3>
                <p>Every guest attending will win one of ten table prizes.  There is no cost to the guests for table prize tickets.  Each guest receives a number from 1-10.  A live draw takes place in the ballroom to determine which table prizes correspond to which table prize number.  Table prizes can be picked up following the live draw at the table prize pickup area.</p>
                <ul>
                    <li>Prize #1</li>
                    <li>Prize #2</li>
                    <li>Prize #3</li>
                    <li>Prize #4</li>
                    <li>Tea</li>
                    <li>Raffle Ticket</li>
                    <li>Center Piece</li>
                    <li>Scarf</li>
                    <li>Dance Class</li>
                    <li>$20 Gift Certificate to Dum Sum King</li>
                </ul>
            </section>            

            <a class="anchor" id="prizes-gr"></a>
            <section>
                <h3>Grand Raffle</h3>
                <ul>
                    <li>
                        <b>Audi A3</b>
                        <ul>
                            <li>Courtesy of XXX</li>
                        </ul>
                    </li>
                    <li>
                        <b>YYY</b>
                        <ul>
                            <li>ZZZ</li>
                            <li>HHH</li>
                        </ul>    
                    </li>
                    <li>
                        <b>AAA</b>
                        <ul>
                            <li>Courtesy of XXX</li>
                        </ul>
                    </li>
                </ul>
            </section>            

            <a class="anchor" id="auction"></a>
            <section>
                <h2>Auction</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>
            
            <a class="anchor" id="auction-sa"></a>            
            <section>
                <h3>Silent Auction</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="auction-la"></a>            
            <section>
                <h3>Live Auction</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="food"></a>            
            <section>
                <h2>Food</h4>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="food-gala"></a>
            <section>
                <h3>Gala Dinner</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>
            
            <a class="anchor" id="food-drinks"></a>            
            <section>
                <h3>Beverages</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="food-booths"></a>            
            <section>
                <h3>Food Booths</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

            <a class="anchor" id="food-mb"></a>
            <section>
                <h3>Midnight Buffet</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse vitae orci a augue semper tristique. Aliquam eu ornare velit, nec ultrices eros. Cras sagittis fermentum nibh vel bibendum. Mauris non neque sit amet velit tincidunt adipiscing non ac felis. Duis interdum ipsum eget ligula venenatis dapibus. Praesent id augue at felis sodales suscipit. Fusce dictum porta elit, varius varius nisi pretium et. Praesent tincidunt iaculis est. Pellentesque massa odio, convallis in velit quis, venenatis pellentesque massa. Curabitur quis urna metus. Mauris ullamcorper a augue non ornare. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nunc vitae condimentum leo.</p>
            </section>

        </div>
    </div>
</div>    



