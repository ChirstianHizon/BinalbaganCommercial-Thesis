<?php
  include '..\library\config.php';
  include '..\classes\class.product.php';
  include '..\classes\class.product_log.php';

  $product_log = new Product_Log();
  $product = new Product();


  $id = (isset($_POST['id']) && $_POST['id'] != '') ? $_POST['id'] : '';
  $todate = (isset($_POST['todate']) && $_POST['todate'] != '') ? $_POST['todate'] : '';
  $fromdate = (isset($_POST['fromdate']) && $_POST['fromdate'] != '') ? $_POST['fromdate'] : '';
  $type = (isset($_POST['type']) && $_POST['type'] != '') ? $_POST['type'] : '';

  switch ($type) {
    case 0:

      break;
    case 1:
    $html="";
    $list = $product_log->getAllProductLog();
    foreach($list as $value){
      $type = "";
      switch ($value['TYPE']) {
        case 0:
          $type = "IN";
          break;
        case 1:
          $type = "OUT";
          break;
      }
      $employee = $value["EMP_LNAME"].", ".$value['EMP_FNAME'];
      $html = $html.'<tr id="'.$value['LOG_ID'].'">'.
                  '<td>'.$value['PRD_NAME'].'</td>'.
                  '<td>'.$value['DATESTAMP'].'</td>'.
                  '<td>'.$type.'</td>'.
                  '<td>'.$employee.'</td>'.
                  '<td>'.$value['LOG_QTY'].'</td>'.
                  "</tr>";
    }

    echo json_encode(array("main" => $html));
    break;
    default:
      # code...
      break;
  }
