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
  
  table {
      border-collapse: collapse;
      width: 100%;
      border: 1px solid #333;
  }

    tr, th{
        padding:3px;
        border: 1px solid #333;

    }
td {
    border: 1px solid #333;
    padding:3px;
}

tr.dateTr td{
    padding-top: 4px;
}


tr.dateTr {
    padding-top: 4px;

}

hr {
    height: 1px solid #000;
    color: #000;
    background-color: #000;
}

.wellops {
  min-height: 14px;
  padding: 3px;
  padding-left: 15px;
  margin-bottom: 3px;
  margin-left: 40px;
  margin-right: 0px;
  background-color: #f5f5f5;
  border: 1px solid #e3e3e3;
  border-left: 5px solid #ff0;
  border-radius: 4px;
}




</style>

</head>

<body>
    <?php echo $this->fetch('content'); ?>
</body>
</html>