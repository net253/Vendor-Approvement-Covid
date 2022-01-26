<?php 
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    date_default_timezone_set('Asia/Bangkok');

    $link = mysqli_connect('localhost', 'root', '', 'vam');
    mysqli_set_charset($link, 'utf8');
    $requestMethod = $_SERVER["REQUEST_METHOD"];

    $dataJson = file_get_contents("php://input");
    $data = json_decode($dataJson);

    $visitorToken = "NzCo10w5eyDMtlrfvOppYBuyS0vyvpxgdCx01ToMayl"; 
    $guardToken = "";

    function notify_message($message,$sToken){
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
        date_default_timezone_set("Asia/Bangkok");
        $sMessage = $message;
        $chOne = curl_init(); 
        curl_setopt( $chOne, CURLOPT_URL, "https://notify-api.line.me/api/notify"); 
        curl_setopt( $chOne, CURLOPT_SSL_VERIFYHOST, 0); 
        curl_setopt( $chOne, CURLOPT_SSL_VERIFYPEER, 0); 
        curl_setopt( $chOne, CURLOPT_POST, 1); 
        curl_setopt( $chOne, CURLOPT_POSTFIELDS, "message=".$sMessage); 
        $headers = array( 'Content-type: application/x-www-form-urlencoded', 'Authorization: Bearer '.$sToken.'', );
        curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt( $chOne, CURLOPT_RETURNTRANSFER, 1); 
        // $result = curl_exec( $chOne ); 
        curl_exec( $chOne ); 

        //Result error 
        // if(curl_error($chOne)) 
        // { 
        //     echo 'error:' . curl_error($chOne); 
        // } 
        // else { 
        //     $result_ = json_decode($result, true); 
        //     echo "status : ".$result_['status']; echo "message : ". $result_['message'];
        // } 
        // curl_close( $chOne );   
    }
    
    if($requestMethod == 'POST'){
        if(!empty($data)){
            $number = $data->number;
            $name = $data->name;
            $company = $data->company;
            $toVisit = $data->toVisit;
            $visitDate = $data->visitDate;
            $status = $data->status;
            $note = $data->note;
        
            $sql = "UPDATE t_visitor SET status ='$status', note ='$note' WHERE number ='$number';";
            $result = mysqli_query($link, $sql);

            if ($result) {
                if($status == "approve"){
                    $msg = "\n" . "No: " . $number ."\n" . "Name: " . $name . "\n" . "Organization/Company: " . $company . "\n" . "To Visit: " . $toVisit . "\n" . "Visit date: " . $visitDate . "\n" . "Status: อนุมัติ" . "\n" . "Note: " . $note; 
                    notify_message($msg, $visitorToken);
                    // notify_message($msg, $guardToken);
                }else {
                    $msg = "\n" . "No: " . $number ."\n" . "Name: " . $name . "\n" . "Organization/Company: " . $company . "\n" . "To Visit: " . $toVisit . "\n" . "Visit date: " . $visitDate . "\n" . "Status: ไม่อนุมัติ". "\n" . "Note: " . $note  ; 
                    notify_message($msg, $visitorToken);  
                }
                echo json_encode(['status' => 'ok', 'message' => 'Update Data Complete']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error insertHistory']);    
            }
        }

        // if(!empty($data)){
        //     $company = $data->company;
        //     $datetime = $data->datetime;
        //     $status = $data->status;
        //     $sql = "SELECT * FROM t_visitor WHERE company = '$company' AND datetime = '$datetime ';";
        //     $sqlResult = mysqli_query($link, $sql);
        //     $arr = array();
        //     while ($row = mysqli_fetch_assoc($sqlResult)) {
        //         $arr[] = $row;
        //     } 
        //     $visitorJsonString = json_encode($arr);
            
        //     $visitorInfo = json_decode($visitorJsonString);

        //     if(!empty($visitorInfo)){
        //         $i = 0;
        //         $visitor = "";
        //         while($i < count($visitorInfo)){
        //             $name = $visitorInfo[$i]->name;
        //             $company = $visitorInfo[$i]->company;
        //             $toVisit = $visitorInfo[$i]->toVisit;
        //             $purposeVisit = $visitorInfo[$i]->purposeVisit;
        //             $visitDate = $visitorInfo[$i]->visitDate;
        //             $vaccineDose1 = $visitorInfo[$i]->vaccineDose1;
        //             $vaccineDose2 = $visitorInfo[$i]->vaccineDose2;
        //             $doseDate1 = $visitorInfo[$i]->doseDate1;
        //             $doseDate2 = $visitorInfo[$i]->doseDate2;
        //             $atk = $visitorInfo[$i]->atk;
        //             $atkDate = $visitorInfo[$i]->atkDate;
        //             // $status = $status;
        //             $datetime = $visitorInfo[$i]->datetime;
            
        //             if($i+1 == count($visitorInfo)){
        //                 $comma = "";
        //             }else{
        //                 $comma = ",";
        //             }

        //             $visitor .= "('$name','$company','$toVisit','$purposeVisit','$visitDate','$vaccineDose1','$vaccineDose2','$doseDate1','$doseDate2','$atk','$atkDate','$status','$datetime')$comma";
        //             $i++;
        //         }

        //         $insertHistory = "INSERT INTO t_history (name, company, toVisit, purposeVisit, visitDate, vaccineDose1, vaccineDose2, doseDate1, doseDate2, atk, atkDate, status, datetime) VALUES $visitor ;";

        //         $resultHistory = mysqli_query($link, $insertHistory);

        //         if ($resultHistory) {
        //             $deleteCompany = "DELETE FROM t_visitor WHERE company = '$company';";
        //             $resultDelete = mysqli_query($link, $deleteCompany);
        //             if($resultDelete){
        //                 echo json_encode(['status' => 'ok','message' => 'Update Data Complete']);
        //                 $msg = "\n" . "Name: " . $visitorInfo[0]->name . "\n" . "Organization/Company: " . $visitorInfo[0]->company . "\n" . "To Visit: " . $visitorInfo[0]->toVisit . "\n" . "Visit date: " . $visitorInfo[0]->visitDate . "\n" . "Status: " . $status ; 
        //                 notify_message($msg, $visitorToken);
        //                 if($status != "disapproved"){
        //                     $msg = "\n" . "Name: " . $visitorInfo[0]->name . "\n" . "Organization/Company: " . $visitorInfo[0]->company . "\n" . "To Visit: " . $visitorInfo[0]->toVisit . "\n" . "Visit date: " . $visitorInfo[0]->visitDate . "\n" . "Status: " . $status ; 
        //                     // notify_message($msg, $guardToken);
        //                 }
        //             }else {
        //                 echo json_encode(['status' => 'error','message' => 'Error deleteCompany']);    
        //             }
        //         } else {
        //             echo json_encode(['status' => 'error','message' => 'Error insertHistory']);    
        //         }

        //         $msg = "\n" . "Name: " . $visitorInfo[0]->name . "\n" . "Organization/Company: " . $visitorInfo[0]->company . "\n" . "To Visit: " . $visitorInfo[0]->toVisit . "\n" . "Visit date: " . $visitorInfo[0]->visitDate . "\n" . "Status: " . $status ; 
        //         notify_message($msg, $visitorToken);
        //         // echo $msg;
        //     }
        // }
    }
?>