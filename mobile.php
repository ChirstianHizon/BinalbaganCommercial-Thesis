<?php
include '../library/config.php';
include '../classes/class.sales.php';
include '../classes/class.product.php';
include '../classes/class.orders.php';
include '../classes/class.category.php';
include '../classes/class.cart.php';
include '../classes/class.product_log.php';
include '../classes/class.employee.php';
include '../classes/class.barcode.php';
include '../classes/class.delivery.php';
include '../classes/class.utility.php';
include '../classes/class.customer.php';

$customer = new Customer();
$utility = new Utility();
$delivery = new Delivery();
$employee = new Employee();
$sales = new Sales();
$product = new Product();
$order = new Order();
$category = new Category();
$barcode = new Barcode();
$product_log = new Product_Log();

$uname = (isset($_POST['uname']) && $_POST['uname'] != '') ? $_POST['uname'] : '';
$pass = (isset($_POST['pass']) && $_POST['pass'] != '') ? $_POST['pass'] : '';

$id = (isset($_POST['id']) && $_POST['id'] != '') ? $_POST['id'] : '';
$type = (isset($_POST['type']) && $_POST['type'] != '') ? $_POST['type'] : '';
$access = (isset($_POST['access']) && $_POST['access'] != '') ? $_POST['access'] : '';
$query = (isset($_POST['query']) && $_POST['query'] != '') ? $_POST['query'] : '';

$orderid = (isset($_POST['orderid']) && $_POST['orderid'] != '') ? $_POST['orderid'] : '';

$lat = (isset($_POST['lat']) && $_POST[''] != 'lat') ? $_POST['lat'] : '';
$lng = (isset($_POST['lng']) && $_POST['lng'] != '') ? $_POST['lng'] : '';

$coord = (isset($_POST['coord']) && $_POST['coord'] != '') ? $_POST['coord'] : '';

$todate = (isset($_POST['todate']) && $_POST['todate'] != '') ? $_POST['todate'] : '';
$fromdate = (isset($_POST['fromdate']) && $_POST['fromdate'] != '') ? $_POST['fromdate'] : '';
$supplier = (isset($_POST['supplier']) && $_POST['supplier'] != '') ? $_POST['supplier'] : '';

$uname =$utility->str_insert($uname, "'", "'");
$pass =$utility->str_insert($pass, "'", "'");

$uname =$utility->str_insert($uname, "/", "/");
$pass =$utility->str_insert($pass, "/", "/");

$access_mobile = "185f3f68183cea48c5c9fcb6cc8bcd56";
$access = md5($access);

if ($access != $access_mobile) {
  header("location: ../index.php");
} else {
  $type = (int)$type;
  switch ($type) {
    case 0:
    echo json_encode(array("main" => true));
    break;
    case 1:
    $login_status = $employee->checkLogin($uname, $pass);
    foreach ($login_status as $value) {
      if ($value['COUNT']) {
        echo json_encode(
          array(
            "main" => "OK",
            "status" => true,
            "uid"=>$value['ID'],
            "uname"=>$value['USERNAME'],
            "fname"=>$value['FNAME'],
            "lname"=>$value['LNAME'],
            "type"=>$value['TYPE'],
            "image"=>$value['IMAGE']
          )
        );
      } else {
        echo json_encode(
          array(
            "main" => "OK",
            "status" => false,
            "uname"=> $uname,
            "pass"=>$pass,
            "count"=> $value['COUNT']
          )
        );
      }
    }
    break;
    case 2:
    $paginate = (int)(isset($_POST['paginate']) && $_POST['paginate'] != '') ? $_POST['paginate'] : '';
    $prod_list = array();
    $id = (int)$id;
    if ($query != "") {
      $list =  $product->getProductByIDwQuery($id, $paginate, $query);
    } else {
      $list =  $product->getProductByID($id, $paginate);
    }
    if (!$list) {
      $prod_list['COUNTER'] = array("COUNTER" => 0);
      echo json_encode($prod_list);
      break;
    } else {
      $cnt = 0;
      foreach ($list as $value) {
        $cnt++;
      }
      $prod_list['COUNTER'] = array("COUNTER" => $cnt);
      $count = 1;
      foreach ($list as $value) {
        $level = $value['prd_level'];
        $warning = $value['prd_warning'];
        $optimal = $value['prd_optimal'];

        if ($level>=$optimal) {
          $status = "Optimal Level";
        } elseif ($level>=$warning) {
          $status = "Normal Level";
        } elseif ($level<=$warning && $level >0) {
          $status = "Warning Level";
        } elseif ($level<= 0) {
          $status = "Not Available";
        } elseif ($level< $warning || $level <$warning) {
          $status = "Data Inconsistent";
        } else {
          $status = "Data Not Available";
        }

        $prod_list[$count] =
        array(
          "ID" =>$value['prd_id'],
          "NAME" =>$value['prd_name'],
          "CATEGORY" =>$value['cat_name'],
          "AVAIL" =>$value['prd_status'],
          "STATUS" =>$status
        );
        $count++;
      }
      echo json_encode($prod_list);
    }
    break;
    case 3:
    $dash_data = array();
    $prod_count =  $product->getProductCount();
    $sales_total =  $sales->getTotalSales();
    $delivery_total = $order->getDeliveryCount();
    $pending_total = $order->getPendingCount();

    echo json_encode(array(
      "PRODUCT_COUNT" => number_format($prod_count),
      "SALES_TOTAL"=> number_format($sales_total,2),
      "DELIVERY_TOTAL"=>number_format($delivery_total),
      "PENDING_COUNT"=>number_format($pending_total)
    ));
      break;
      case 4:
      $list = $product->getSpecificProduct($id);
      foreach ($list as $value) {
        echo json_encode(array(
          "NAME"=> $value['prd_name'],
          "DESC"=> $value['prd_desc'],
          "CATEGORY"=>$category->getName($value['cat_id']),
          "PRICE"=> $value['prd_price'],
          "DATESTAMP"=> $value['prd_datestamp'],
          "TIMESTAMP"=> $value['prd_timestamp'],
          "STATUS"=> $value['prd_status'],
          "LEVEL"=> $value['prd_level'],
          "OPTIMAL"=> $value['prd_optimal'],
          "WARNING"=> $value['prd_warning'],
          "IMAGE"=> $value['prd_image']
        ));
      }
      break;
      case 5:
      $total = 0;
      $list = $barcode->getBarcodeList($id);
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      } else {
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {
          $prod_list[$count] =array("CODE" =>$value['bar_code']);
          $count++;
        }
        echo json_encode($prod_list);
      }

      break;
      case 6:
      $list = $order->getDeliveryOrders();
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      } else {
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {
          $prod_list[$count] =
          array(
            "ID" =>$value['order_id'],
            "FNAME" =>$value['cust_firstname'],
            "LNAME" =>$value['cust_lastname'],
            "DATE" =>$value['order_datestamp'],
            "TIME" =>$value['order_timestamp'],
            "STATUS" =>$value['order_status'],
            "TYPE" =>"$value['order_type']"
          );
          $count++;
        }
        echo json_encode($prod_list);
      }
      break;
      case 7:
      $list = $order->getSpecDeliveryOrders($id);
      if (!$list) {
        echo json_encode(array("main" => flase));
        break;
      } else {
        foreach ($list as $value) {
          echo json_encode(array(
            "main" => true,
            "ID" => $value['order_id'],
            "FNAME" => $value['cust_firstname'],
            "LNAME" => $value['cust_lastname'],
            "DATE" => $value['order_datestamp'],
            "TITEMS" => $value['TOTAL'],
            "TAMOUNT" => $value['TAMOUNT'],
            "STATUS" => $value['order_status']
          ));
        }
      }
      break;
      case 8:
      $result = $delivery->addDelivery($orderid);
      $order->startdelivery($orderid);
      echo json_encode(array("ID" => $result));
      break;
      case 9:
      $list = json_decode($coord,true);
      $result = 0;
      foreach ($list as $value) {
        $id = $value['delivid'];
        $temp = $delivery->addRoute($value['delivid'],$value['lat'],$value['lng'],$value['datetime']);
        $result = $result.$temp;
        // echo json_encode(array(
        //   "id" => $value['id'],
        //   "lat" => $value['lat'],
        //   "lng" => $value['lng'],
        //   "datetime" => $value['datetime'],
        //   "delivid" => $value['delivid']
        // ));
      }if($result >0){$result =true;}else{$result = false;}
      $result = $delivery->finishDelivery($id);
      $orderid = $delivery->getOrderId($id);
      $sales->updateSalesReceiveDatetime($orderid);
      $order->completeOrder($orderid);
      echo json_encode(array("RESULT" => $result));
      break;
      case 10:
      $list = $order->getDeliveryOrders();
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      } else {
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {
          $prod_list[$count] =
          array(
            "ID" =>$value['order_id'],
            "FNAME" =>$value['cust_firstname'],
            "LNAME" =>$value['cust_lastname'],
            "DATE" =>$value['order_datestamp'],
            "TIME" =>$value['order_timestamp'],
            "STATUS" =>$value['order_status'],
            "TYPE" =>$value['order_type'],
            "ADDRESS"=>$value['order_address']
          );
          $count++;
        }
        echo json_encode($prod_list);
      }
      case 11:
      $list = $delivery->getDeliveryDetails($id);
      if (!$list) {
        $prod_list = '';
        echo json_encode($prod_list);
        break;
      } else {
        foreach ($list as $value) {
          $prod_list =
          array(
            "DELID" =>$value['del_id'],
            "OID" =>$value['order_id'],
            "CUST_NAME" =>$value['cust_lastname'].", ".$value['cust_firstname'],
            "CUST_CONTACT"=>$value['cust_contact'],
            "CUST_ADDRESS"=>$value['add_name'],
            "CUST_NOTES"=>$value['add_notes'],
            "ORDER_DATE" =>$value['order_datestamp'],
            "AMOUNT" =>$value['AMOUNT'],
            "TOTAL" =>$value['TOTAL'],
            "DATE_RECIEVE" =>$value['del_end_datestamp'],
            "STATUS" =>$value['order_status'],
            "TYPE" =>$value['order_type']
          );
        }
        echo json_encode($prod_list);
      }
      break;
      case 12:
      $list = $order->getSpecOrderList($id);
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      } else {
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {
          $prod_list[$count] =
          array(
            "PRDID" =>$value['ID'],
            "NAME" =>$value['NAME'],
            "PRICE" =>$value['PRICE'],
            "QTY" =>$value['QTY'],
            "SUBTOTAL" =>$value['SUBTOTAL']
          );
          $count++;
        }
        echo json_encode($prod_list);
      }

      break;
      case 13:
      $list = $product_log->getAllProductLog($fromdate,$todate,"ALL");
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      }else{
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {

          switch ($value['TYPE']) {
            case 0:
            $type = "IN";
            break;
            case 1:
            $type = "OUT";
            break;
          }
          $status = $value['PRD_NAME'];
          $employee = $value["EMP_LNAME"].", ".$value['EMP_FNAME'];

          $price = ($value['SPRICE'] == null)?$price = $value['PRICE']:$value['SPRICE'];
          $total = $price * $value['LOG_QTY'];

          $supname = ($value['SUPNAME']== "")?$supname = "N/A":$value['SUPNAME'];

          $prod_list[$count] =
          array(
            "PRDNAME" =>$value['PRD_NAME'],
            "DATESTAMP" =>$value['DATESTAMP'],
            "TYPE" =>$type,
            "EMPLOYEE" =>$employee,
            "LOGQTY" =>$value['LOG_QTY'],
            "TOTAL"=>number_format($total,2),
            "SUPPLIER"=>$supname,
            "ID" =>$value['LOG_ID'],
          );
          $count++;
        }
        if($status){
          echo json_encode($prod_list);
        }else {
          $prodx_list['COUNTER'] = 0;
          echo json_encode($prodx_list);
        }
      }
      break;
      case 14:
      $total_items = 0;
      $total_amount = 0;
      $list = $sales->getAllSales($fromdate,$todate);
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      }else{
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {

          $sales_id = $value['ID'];
          if($value['CUSTOMER'] == 0  ){
            $customer_id = $employee->getEmplyeeName($value['EMPLOYEE']);
          }else{
            $customer_id = $customer->getCustomeName($value['CUSTOMER']);
          }
          $sales_type = $value['TYPE'];
          $sales_qty =$value['QUANTITY'];
          $sales_date = $value['DATE'];
          $sales_total = $value['TOTAL'];

          $total_items += $sales_qty;
          $total_amount += $sales_total;
          switch ($sales_type) {
            case 2:
            $sales_type = "WALK - IN";
            break;
            case 0:
            $sales_type = "PICK - UP";
            break;
            case 1:
            $sales_type = "DELIVERY";
            break;
            default:
            $sales_type = "TYPE ERROR";
            break;
          }

          $prod_list[$count] =
          array(
            "SID" =>$sales_id,
            "DATESTAMP" =>$sales_date,
            "USER" =>$customer_id,
            "TYPE" =>$sales_type,
            "QTY" =>$sales_qty,
            "TOTAL"=>number_format($sales_total,2)
          );
          $count++;
        }
        echo json_encode($prod_list);
      }
      break;
      case 15:
      $list = $delivery->getAllDelivery($fromdate,$todate);
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      }else{
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {

          $customer_name = $customer->getCustomeName($value['cust_id']);

          $prod_list[$count] =
          array(
            "ID" =>$value['del_id'],
            "ORDERID" =>$value['order_id'],
            "DATESTAMP" =>$value['receive_datestamp'],
            "CUSTOMER" =>$customer_name,
            "STATUS" =>$value['del_status'],
            "QTY"=>$value['prd_qty'],
            "PRICE"=>$value['prd_price']
          );
          $count++;
        }
        echo json_encode($prod_list);
      }
      break;
      case 16:
      $list = $order->getAllPendingOrders();
      if (!$list) {
        $prod_list['COUNTER'] = 0;
        echo json_encode($prod_list);
        break;
      }else{
        $cnt = 0;
        foreach ($list as $value) {
          $cnt++;
        }
        $prod_list['COUNTER'] = $cnt;
        $count = 1;
        foreach ($list as $value) {
            $customer_name = $customer->getCustomeName($value['CUSTOMER']);
          $prod_list[$count] =
          array(
            "ID"        =>$value['ID'],
            "TOTAL"   =>"P ".number_format($value['TOTAL'],2),
            "DATESTAMP" =>$value['DATE'],
            "STATUS"  =>$value['TYPE'],
            "CUSTOMER"    =>$customer_name,
            "QTY"       =>$value['QUANTITY']
          );
          $count++;
        }
        echo json_encode($prod_list);
      }

      break;
    }
  }
  /*
  SELECT *
  FROM tbl_order ordr
  INNER JOIN tbl_customer cst ON cst.cust_id = ordr.cust_id
  INNER JOIN tbl_order_list ordl ON ordl.order_id = ordr.order_id
  INNER JOIN tbl_product pd ON pd.prd_id = ordl.prd_id
  WHERE ordr.order_type = '1' AND ordr.order_status = '1'
  */
