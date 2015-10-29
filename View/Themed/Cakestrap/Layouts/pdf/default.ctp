<?php echo $this->Html->docType('html5'); ?> 
<html>

<head>
    <style>
        @page{
            margin-top:40px;
            margin-bottom: 40px;
            margin-left: 40px;
            margin-right; 40px;
        }
        
        .bolder { font-weight: bolder;}

        body{
  /*
            margin-top: 30px;
            margin-left: 40px;
            margin-right: 40px;
            margin-bottom: 30px;
*/
      /*font-family: "dejavu sans condensed", arial, verdana, sans-serif;*/
            font-family: "verdana", "sans-serif";
            font-size: 12px;
  }
  


hr {
    height: 1px solid #000;
    color: #000;
    background-color: #000;
}

table {
      border-collapse: collapse;

      width: 100%;
  }

    tr, th{
        padding:2px;
          border: 1px solid #000;

          margin-bottom: 30px;


    }
td {
  border: 1px solid #000;
    padding:2px;

}

tr.dateTr td{
    border: 0px !important;
    padding-top: 4px;
    margin-left: -5px;
}


tr.dateTr {
        border: 0px !important;

    padding-top: 4px;
    margin-left: -5px;

}





</style>

</head>

<body>
    <?php echo $this->fetch('content'); ?>
</body>
</html>