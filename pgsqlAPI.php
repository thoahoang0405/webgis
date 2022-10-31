<?php
    if(isset($_POST['functionname']))
    {
        $paPDO = initDB();
        $paSRID = '4326';
        $paPoint = $_POST['paPoint'];
        $functionname = $_POST['functionname'];
      
        
        $aResult = "null";
        if ($functionname == 'getGeoCMRToAjax')
            $aResult = getGeoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getInfoCMRToAjax')
            $aResult = getInfoCMRToAjax($paPDO, $paSRID, $paPoint);
        else if ($functionname == 'getBv')
            $aResult=getBv($paPDO, $paSRID, $paPoint);
       
        echo $aResult;
    
        closeDB($paPDO);
    }

    function initDB()
    {
        // Kết nối CSDL
        $paPDO = new PDO('pgsql:host=localhost;dbname=benhvien;port=5433', 'postgres', '12345');
        return $paPDO;
    }
    function query($paPDO, $paSQLStr)
    {
        try
        {
            // Khai báo exception
            $paPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Sử đụng Prepare 
            $stmt = $paPDO->prepare($paSQLStr);
            // Thực thi câu truy vấn
            $stmt->execute();
            
            // Khai báo fetch kiểu mảng kết hợp
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            
            // Lấy danh sách kết quả
            $paResult = $stmt->fetchAll();   
            return $paResult;                 
        }
        catch(PDOException $e) {
            echo "Thất bại, Lỗi: " . $e->getMessage();
            return null;
        }       
    }
    function closeDB($paPDO)
    {
        // Ngắt kết nối
        $paPDO = null;
    }
   
    function getResult($paPDO,$paSRID,$paPoint)
    {
       
        $paPoint = str_replace(',', ' ', $paPoint);

        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"benhvien\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";

        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }
   /**
    * lấy ra geom với point chọn từ bản đồ
    */
    function getGeoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
      
        $paPoint = str_replace(',', ' ', $paPoint);
        
        $mySQLStr = "SELECT ST_AsGeoJson(geom) as geo from \"benhvien\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
       
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                return $item['geo'];
            }
        }
        else
            return "null";
    }


    
    /**
     * lấy ra thông tin vùng có point được chọn
     */
    function getInfoCMRToAjax($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
       
        $mySQLStr = "SELECT phone, open_ho, close_ho,name, addr_stree from \"benhvien\" where ST_Within('SRID=".$paSRID.";".$paPoint."'::geometry,geom)";
        
        $result = query($paPDO, $mySQLStr);
        
        if ($result != null)
        {
            
            $resFin = '<table>';
            // Lặp kết quả
            foreach ($result as $item){
                $resFin = $resFin.'<tr><td> '.$item['name'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Số điện thoại:  '.$item['phone'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Mở cửa: '.$item['open_ho'].' - '.$item['close_ho'].'</td></tr>';
                $resFin = $resFin.'<tr><td>Địa chỉ:  '.$item['addr_stree'].'</td></tr>';

            
                break;
            }
            $resFin = $resFin.'</table>';
            return $resFin;
        }
        else
            return "chưa có thông tin";
    }
    function getBv($paPDO,$paSRID,$paPoint)
    {
        
        $paPoint = str_replace(',', ' ', $paPoint);
       
        $mySQLStr =  "SELECT benhvien.gid,  benhvien.name ,  benhvien.addr_stree
        from  \"benhvien\" 
        where  ST_Distance('SRID=".$paSRID.";".$paPoint."'::geometry,geom)<0.005 ";

        
        $result = query($paPDO, $mySQLStr);
        
                 
        if ($result != null)
        {   
            $resFin = '
            <table class="table">
  <thead>
    <tr>
    <th scope="col" colspan="1" style="min-width: 70px">id</th>
      <th scope="col" colspan="2" style="min-width: 200px" >Tên Bệnh viện</th>
      <th scope="col" colspan="3" style="min-width: 200px; ">Địa Chỉ</th>
    </tr>
  </thead>
  <tbody>'; 
            
             foreach ($result as $value){
                 $resFin = $resFin.'<tr>
                 <td colspan="1" style="min-width: 70px;"class="trId2">'.$value['gid'].'</td>';
                 $resFin = $resFin.'<td colspan="2" style="min-width: 200px">'.$value['name'].'</td>';
               
                 $resFin = $resFin.'<td colspan="3" style="min-width: 200px">'.$value['name'].'</td>
                
                 </tr>';
            //     $resFin = $resFin.'<br>'; 
             }
             $resFin = $resFin.'</tbody>
             </table>'; 
            
             echo $resFin;
        }
        else
            echo "không tìm thấy kết quả";
    }
   
    /**
     * lấy ra vùng với id vừa tìm kiếm
     */
    
    if(isset($_POST['id'])){
        $id = $_POST['id'];
        $paPDO = initDB();
        $mySQLStr = "SELECT  ST_AsGeoJson(geom) as geo
            from  \"benhvien\" 
            where benhvien.gid = '$id'";
        //echo $mySQLStr;
        //echo "<br><br>";
        $result = query($paPDO, $mySQLStr);
      
        
        if ($result != null)
        {
            // Lặp kết quả
            foreach ($result as $item){
                echo $item['geo'];
            }
        }
        else
            echo "null";
    }
/**
 * lấy ra thông tin tìm kiếm
 */
    if(isset($_POST['search'])){
        $search= $_POST['search'];
        $paPDO = initDB();
        $mySQLStr = "SELECT benhvien.gid,  benhvien.name ,  benhvien.addr_stree
            from  \"benhvien\" 
            where benhvien.name like '%$search%'";
     
        $result = query($paPDO, $mySQLStr);
       
               
        if ($result != null)
        {   
            $resFin = '
            <table class="table">
  <thead>
    <tr>
    <th scope="col" colspan="1" style="min-width: 70px">id</th>
      <th scope="col" colspan="2" style="min-width: 200px" >Tên Bệnh viện</th>
      <th scope="col" colspan="3" style="min-width: 200px; ">Địa Chỉ</th>
    
    </tr>
  </thead>
  <tbody>'; 
            
             foreach ($result as $value){
                 $resFin = $resFin.'<tr>
                 <td colspan="1" style="min-width: 70px;"class="trId">'.$value['gid'].'</td>';
                 $resFin = $resFin.'<td colspan="2" style="min-width: 200px">'.$value['name'].'</td>';
                 $resFin = $resFin.'<td colspan="3" style="min-width: 200px">'.$value['addr_stree'].'</td>
                
                 </tr>';
            //     $resFin = $resFin.'<br>'; 
             }
             $resFin = $resFin.'</tbody>
             </table>'; 
            
             echo $resFin;
        }
        else
            echo "không tìm thấy kết quả";

    }
 
    
?>
