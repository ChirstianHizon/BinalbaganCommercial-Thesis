  <?php
  class Product{
    public $db;
    public function __construct(){
      $this->db = new mysqli(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
      if(mysqli_connect_errno()){
        echo "Database connection error.";
        exit;
      }
    }

    public function test(){
      return "CLASS OK";
    }

    public function addNewBarcodeonID($prdid){

      $sql = "SELECT cat_id AS CATID FROM tbl_product WHERE prd_id = '$prdid'";
      $result = mysqli_query($this->db,$sql);
      $row = mysqli_fetch_assoc($result);
      $cat = $row['CATID'];

      $code = "ITEM-".$cat."-".$prdid;
      $sql = "INSERT INTO tbl_barcode(bar_code,prd_id)
         VALUES('$code','$prdid')";
      $result = mysqli_query($this->db,$sql) or die(mysqli_error() . "CLASS ERROR");
      return $result;
    }

    public function addProduct($name,$desc,$price,$category,$level,$optimal,$warning,$image){

      $name = mysqli_real_escape_string($this->db,$name);
      $desc = mysqli_real_escape_string($this->db,$desc);
      $price =   mysqli_real_escape_string($this->db,$price);
      $level =   mysqli_real_escape_string($this->db,$level);
      $optimal =   mysqli_real_escape_string($this->db,$optimal);
      $warning =   mysqli_real_escape_string($this->db,$warning);
      $image =   mysqli_real_escape_string($this->db,$image);


      $sql = "SELECT COALESCE(COUNT(prd_id),0) AS COUNT FROM tbl_product WHERE prd_name = '$name'";
      $result = mysqli_query($this->db,$sql);
      $row = mysqli_fetch_assoc($result);
      $count = $row['COUNT'];
      if($count <= 0){
        $sql = "INSERT INTO tbl_product(prd_name,prd_desc,prd_datestamp,prd_timestamp,prd_level,prd_optimal,prd_warning,prd_price,prd_image,cat_id)
           VALUES('$name','$desc',NOW(),NOW(),'$level','$optimal','$warning','$price','$image','$category')";
           $result = mysqli_query($this->db,$sql) or die(mysqli_error() . $sql);
           if($result == 1){
             //GETS THE LAST ID USED IN QUERY
             $result = mysqli_insert_id($this->db);
           }
           return $result;
      }else{
        return false;
      }


    }

    public function deleteProduct($id){
      $sql = "UPDATE tbl_product SET
      prd_status = '0'
      WHERE prd_id = '$id'";
      $result = mysqli_query($this->db,$sql) or die(mysqli_error() . "CLASS ERROR");
      return $result;
    }

	public function getProduct(){
      $sql = "SELECT * FROM tbl_product WHERE prd_status = 1 ORDER BY prd_id DESC";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }

    public function getProductCount(){
      $sql = "SELECT COALESCE(COUNT(prd_id),0) AS COUNT FROM tbl_product  WHERE prd_status = 1 ORDER BY prd_id DESC";
      $result = mysqli_query($this->db,$sql);
      $row = mysqli_fetch_assoc($result);
      $result = $row['COUNT'];
      return $result;
    }

    public function getProductLevel($id){
      $sql = "SELECT prd_level FROM tbl_product WHERE prd_id = '$id'";
      $result = mysqli_query($this->db,$sql);
      $row = mysqli_fetch_assoc($result);
      $level = $row['prd_level'];
      return $level;
    }

    public function getSpecificProduct($id){
      $sql = "SELECT * FROM tbl_product WHERE prd_id = '$id' ";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }

    public function updateProduct($id,$name,$desc,$price,$category,$optimal,$warning,$image){

      $name = mysqli_real_escape_string($this->db,$name);
      $desc = mysqli_real_escape_string($this->db,$desc);
      $category = mysqli_real_escape_string($this->db,$category);
      $price =   mysqli_real_escape_string($this->db,$price);
      $optimal =   mysqli_real_escape_string($this->db,$optimal);
      $warning =   mysqli_real_escape_string($this->db,$warning);
      $image =   mysqli_real_escape_string($this->db,$image);

      $sql = "UPDATE tbl_product SET
      prd_name = '$name',
      prd_desc = '$desc',
      prd_price = '$price',
      cat_id = '$category',
      prd_optimal = '$optimal',
      prd_warning = '$warning',
      prd_image = '$image'
      WHERE prd_id = '$id'";
      $result = mysqli_query($this->db,$sql) or die(mysqli_error() . "CLASS ERROR");
      return $result;
    }

    public function updateProductStock($id,$level,$curr,$empid,$type,$salesid,$supid){
      $sql = "SELECT sprice_price AS price FROM  tbl_supplier_prices WHERE sup_id = '$supid' AND prd_id = '$id' limit 1";
      $result = mysqli_query($this->db,$sql) or die(mysqli_error() . $sql);
      $row = mysqli_fetch_assoc($result);
      $price = $row['price'];

      $sql = "UPDATE tbl_product SET
      prd_level = '$level'
      WHERE prd_id = '$id'";
      $result = mysqli_query($this->db,$sql) or die(mysqli_error() . "CLASS ERROR");

      $sql = "INSERT INTO tbl_product_log(prd_id,log_qty,log_datestamp,log_timestamp,emp_id,log_type,sales_id,sup_id,supp_price)
        VALUES('$id','$curr',NOW(),NOW(),'$empid','$type','$salesid','$supid','$price')";
      $result = mysqli_query($this->db,$sql) or die(mysqli_error() . $sql);
      return $supid;
    }

    public function getProductwCategory(){
      $sql = "SELECT * FROM (tbl_product INNER JOIN tbl_category ON tbl_product.cat_id = tbl_category.cat_id)";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }

    public function getSpecificProductwCategory($id){
      $sql = "SELECT * FROM (tbl_product INNER JOIN tbl_category ON tbl_product.cat_id = tbl_category.cat_id) WHERE prd_id = '$id'";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }

    public function getProductByID($id,$limit){
      $sql = "SELECT *
      FROM tbl_product
      INNER JOIN tbl_category ON tbl_category.cat_id = tbl_product.cat_id
      WHERE prd_id < '$id'
      ORDER BY prd_id DESC limit $limit";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }

    public function getProductByIDwQuery($id,$limit,$query){
      $sql = "SELECT *
      FROM tbl_product
      INNER JOIN tbl_category ON tbl_category.cat_id = tbl_product.cat_id
      WHERE prd_id < '$id' AND prd_name LIKE '%$query%'
      ORDER BY prd_id DESC limit $limit";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }

    public function getWarningProducts(){
      $sql = "SELECT prd_name,cat_name,prd_level,prd_id
      FROM tbl_product
      INNER JOIN tbl_category ON tbl_product.cat_id = tbl_category.cat_id
      WHERE prd_level <= prd_warning AND prd_status = 1
      ";
      $result = mysqli_query($this->db,$sql);
      if($result){
        while($row = mysqli_fetch_assoc($result)){
          $list[] = $row;
        }
        if(empty($list)){return false;}
        return $list;
      }else {
        return $result;
      }
    }










  }//END OF CLASS
