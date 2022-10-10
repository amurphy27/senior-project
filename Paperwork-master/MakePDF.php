<?php
    require_once 'vendor/autoload.php';
    require "Field.php";


    function getY($y)
    {
        return $y % 279.4;
    }



    function sendPDF($formID){
        $dotenv = Dotenv\Dotenv::createUnsafeImmutable("../../../../../../etc/paper");
        $dotenv->load();
        $servername = getenv('SERVERNAME');
        $username = getenv('USERNAME');
        $password = getenv('PASSWORD');

    try {
    // Create new PDF 215.9 w 279.4 h
    $mpdf = new \Mpdf\Mpdf(['tempDir' => '/tmp']); 
    $db = new PDO("mysql:host=$servername;dbname=paperwork", $username, $password);
    // set the PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    //get the formType
    $stmt = $db->prepare('SELECT formType FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formType = $stmt->fetch()['formType'];


    //get the formTitle
    $stmt = $db->prepare('SELECT formTitle FROM forms WHERE formID=:formID');
    $stmt->bindParam(':formID', $formID);
    $stmt->execute();
    $formTitle = $stmt->fetch()['formTitle'];


    // Reference the PDF you want to use 
    $mpdf->SetDocTemplate(__DIR__.'/formPDF/'. $formType .'.pdf',true);
    $pageCount =$mpdf->setSourceFile(__DIR__. '/formPDF/'. $formType .'.pdf');//count the number of page

    for ($p=1; $p<=($pageCount); $p++) {
        $mpdf->AddPage();
    
        $preFields = $db->query("SELECT * FROM templatefields WHERE formType=$formType AND yPos > 279.4*($p-1) AND yPos < 279.4 * $p ORDER BY FieldID ASC ");
        $fieldTemplatesDB = $preFields->fetchAll();
    
        $fieldsContent = $db->query("SELECT * FROM templatefields,fields WHERE FormID=$formID AND fields.FieldID = templatefields.FieldID AND yPos > 279.4*($p-1) AND yPos < 279.4 * $p ORDER BY templatefields.FieldID ASC ");
        $fieldsDB= $fieldsContent->fetchAll();
    
        $fields = array();
    
        $i = 0; 
        $count = count($fieldsDB);
        foreach($fieldTemplatesDB as $fieldTemplateDB)
        {
        $fieldTDB = new Field;
        $fieldTDB->fieldId = $fieldTemplateDB['FieldID'];
        $fieldTDB->x = $fieldTemplateDB['xPos'];
        $fieldTDB->y = $fieldTemplateDB['yPos'];
        $fieldTDB->width = $fieldTemplateDB['width'];
        $fieldTDB->height = $fieldTemplateDB['height'];
        if ($count !=0 ){
        if($fieldTemplateDB['FieldID']==$fieldsDB[$i]['FieldID'])
        {
            $fieldTDB->content = $fieldsDB[$i]['content'];
            if($i < ($count-1)){
                $i++;
            }
        }
        array_push($fields, $fieldTDB);
        }
        // adding a Cell using:
        // $pdf->Cell( $width, $height, $text, $border, $fill, $align);
        foreach($fields as $field)
        {
            $mpdf->SetFontSize('8'); // set font size
            $mpdf->SetXY($field->x,getY($field->y));
            if($field->content == "true"){
                $mpdf->Cell($field->width, $field->height, 'x', 0, 0,'L');
            }
            else if($field->content == "false"){
                $mpdf->Cell($field->width, $field->height,'', 0, 0, 'L');
            }
            else if(strncmp($field->content, "<div>", 5)===0){

                $field->content= str_replace("<div>","",$field->content) ;
                
                $field->content=str_replace("</div>","\n",$field->content);
                
                $field->content=str_replace("<br>","\n" ,$field->content) ;
                
                $mpdf->SetFontSize('10'); // set font size
                
                $mpdf->MultiCell($field->width, 5, $field->content, 0, 0);
            }
            else{
            $mpdf->Cell($field->width, $field->height, $field->content, 0, 0, 'L');
            }
        }
    }

}

    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
        //return the pdf as a String
        if(strcmp($formTitle,'') == 0){
            return $mpdf->Output($formType . '.pdf', 'S');
        }
        
        return $mpdf->Output($formTitle.'.pdf', 'S');
    }



?>