<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<?php error_reporting (E_ALL ^ E_NOTICE); ?>
<?php
    include('./lib/qrlib.php');
    function readCsvFile($filename)
    {
        if ( $file = fopen( "upload/" . $filename , 'r' ) ) {
            
            $start_row = 1; //define start row
            $i = 1; //define row count flag
            $details = array();
            while (($row = fgetcsv($file)) !== FALSE) {
                // print_r($row);
                $details[$i] = $row;
                // echo "<br>";
                $i++;
            }
            return $details;
        }
        
    }

    function convertToAssociative($details)
    {
        $finalArray = array();
        for($i = 1 ; $i<sizeof($details) ; $i++)
        {
            $temp = array();
            for($j = 0 ; $j<sizeof($details[0]) ; $j++)
            {
                if($details[0][$j] == "ASSESSMENT"){
                    $temp[$details[0][$j]] = number_format($details[$i][$j], 2,'.', ',');
                }else{
                    $temp[$details[0][$j]] = $details[$i][$j];
                }
            }
            array_push($finalArray,$temp);
        }
        // printData($finalArray);
        return $finalArray;
    }

    //print data beautifully
    function printData($data)
    {
        echo '<pre>'; print_r($data); echo '</pre>';
    }

    function generateQR($data)
    {
        
        $path = 'qrcodes/';
        $file = $path.$data['BUSINESS_NAME']."-".$data['BPLO_ACCOUNT_NUMBER'].".png";
        // printData($data['BUSINESS_NAME']);
        $text = "Account Number: ".$data['BPLO_ACCOUNT_NUMBER']."-".$data['CENRO_CODE']."\n"."Certificate Number: ".$data['CERTIFICATE_NUMBER']."\n"."Issuance: ".$data['ISSUANCE']."\n"."Expiry: ".$data['EXPIRY']."\n"."OR Number: ".$data['OR_NUMBER']."\n"."Assessment: P ".$data['ASSESSMENT'];

        // $ecc stores error correction capability('L')
        $ecc = 'L';
        $pixel_Size = 10;
        $frame_Size = 10;
        // Generates QR Code and Stores it in directory given
        QRcode::png($text, $file, $ecc, $pixel_Size, $frame_size);
    }

    function createZipFile()
    {
        // Get real path for our folder
        $rootPath = realpath('qrcodes/');

        // Initialize archive object
        $zip = new ZipArchive();
        $zip->open('QRCodes.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Create recursive directory iterator
        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file)
        {
            // Skip directories (they would be added automatically)
            if (!$file->isDir())
            {
                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);
            }
        }

        // Zip archive will be created only after closing object
        $zip->close();
        
        header('Content-Type: application/zip');
        header("Content-Disposition: attachment; filename='QRCodes.zip'");
        header('Content-Length: ' . filesize('QRCodes.zip'));
        header("Location: QRCodes.zip");
    }
    
   
    $details = readCsvFile("uploaded_file.csv");
    $details = array_values($details);
    $details = convertToAssociative($details);
    for($i = 0 ; $i < sizeof($details) ; $i++) 
    {
        generateQR($details[$i]);
    }
    createZipFile()
    // printData(array_flatten($details));

?>