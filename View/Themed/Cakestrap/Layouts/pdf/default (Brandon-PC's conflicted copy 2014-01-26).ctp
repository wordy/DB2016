<?php echo $this->Html->docType('html5'); ?> 
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
            /*font-family: "arial", "verdana", "sans-serif";*/
           font-family: "sans-serif";
            font-size: 12px;
            font-weight: normal;
            font-style: normal;
            font-variant: none;
      

  }

.table-bordered{
    border-collapse:collapse;
    border: 1px solid black;
}

 .table-bordered td{
    margin:0px;
    padding: 2px;
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


.btn-lt {
    color: #ffffff;
    background: #555;
    padding: 2px 4px !important;
    border: 1px solid #000;
    font-size: 10px !important;
    font-weight: bold;
    text-align: center;
    vertical-align: middle;
}

.btn-lpt {
    font-size: 10px !important;
    padding: 2px 4px;
    border: 1px solid #000;
    color: #ffffff;
    background: #555;
    font-weight: bold;
}

.btn-ctl {
    font-size: 10px !important;
    padding: 2px 4px;
    border: 1px solid #000;
    color: #ffffff;
    background: #00816C;
    font-weight: bold;
}


.btn-ctu {
    font-size: 10px !important;
    padding: 2px 4px !important;
    border: 1px solid #000;
    font-weight: bold;
}
</style>
</head>

<body>
    <?php echo $this->fetch('content'); ?>
</body>
</html>