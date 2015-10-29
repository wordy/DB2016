<?php //echo $this->Html->docType('html5'); ?> 
<html>

<head>
    <style>
        @page { margin: 0px; }

.bolder { font-weight: bolder;}
  
        body{
            margin-top: 40px;
            margin-left: 40px;
            margin-right: 40px;
            margin-bottom: 40px;
      /*font-family: "dejavu sans condensed", arial, verdana, sans-serif;*/
            font-family: "arial", "verdana", "sans-serif";
            font-size: 12px;
            font-weight: normal;
            font-style: normal;
            font-variant: none;
      

  }

.table-bordered{
    margin-left:auto;
    margin-right:auto;
    
    border-collapse:collapse;
    border: 1px solid black;
}

 .table-bordered td{
    margin:0px;
    padding: 2px;
}             



.table-bordered td, .table-bordered th {
    border: 1px solid #000000 !important;
}

.table-bordered tr, .table-bordered th {
    border-bottom: 1px solid #000000 !important;
}


.table-nobotmarg {
  margin-bottom: 0px;
}

.badge-white {
    color: #000000;
    background-color: #ffffff;
}

.badge-black {
    color: #ffffff;
    background-color: #101010;
}
.badge-warning {
    color: #ffffff;
    background-color: #8A0808;
  }

.badge-success {
    color: #ffffff;
    background-color: #0B610B;
}

.btn-xxs {
  padding: 3px 3px 3px 3px;
  font-size: 10px;
}

.alert {
  padding: 15px;
  margin-bottom: 20px;
  border: 1px solid transparent;
  border-radius: 4px;
}

.alert-info {
  color: #3a87ad;
  background-color: #d9edf7;
  border-color: #bce8f1;
}

.alert-danger {
  color: #b94a48;
  background-color: #f2dede;
  border-color: #eed3d7;
}


hr {
    border: 0;
    height: 1px;
    background: #000;
}

h1,h2,h3,h4,h5,h6,{
  font-weight: 500;
}

h1,
.h1 {
  font-size: 26px !important ;
}

h2,
.h2 {
  font-size: 20px  !important;
}

h3,
.h3 {
  font-size: 18px !important;
}

h4,
.h4 {
  font-size: 14px  !important;
}

h5,
.h5 {
  font-size: 12px !important;
}

h6,
.h6 {
  font-size: 10px !important;
}

h1,
h2,
h3 {
  margin-top: 0px;
  margin-bottom: 5px;
}

h4,
h5,
h6 {
  margin-top: 10px;
  margin-bottom: 15px;
}



.sm-top { margin-top: 10px;}

</style>

</head>

<body>
    <?php echo $this->fetch('content'); ?>
</body>
</html>