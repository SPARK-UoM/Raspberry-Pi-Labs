<?php
    $config = array(
        array(0, 'UNSHEDULED', 'UNSHEDULED'),
        array(0, 'UNSHEDULED', 'UNSHEDULED'),
        array(0, 'UNSHEDULED', 'UNSHEDULED'),
        array(0, 'UNSHEDULED', 'UNSHEDULED'),
    );


    if(isset($_POST["submit"])){

        for($i=1; $i <= 4; ++$i){

            if (isset($_POST["Status_dev".$i])){
                if(empty($_POST["Status_dev".$i]) || $_POST["Status_dev".$i] == false){
                    $config[$i-1][0] = false; 
                }else{
                    $config[$i-1][0] = true;
                }
            }else{
                $config[$i-1][0] = false; 
            }

            if (isset($_POST["Start_dev".$i])){
                if(!empty($_POST["Start_dev".$i])) $config[$i-1][1] = $_POST["Start_dev".$i];
            }

            if (isset($_POST["End_dev".$i])){
                if(!empty($_POST["End_dev".$i])) $config[$i-1][2] = $_POST["End_dev".$i];
            }

        }// end of the for loop

        //updating the Configuration file
        $config_file = fopen("config.txt", "w");
        fwrite($config_file, "Spark-Projct1-RPI-Configuration\n-------------------------------\n\n[FORCELOG]\n");
        for($i=0; $i < 4; ++$i){
            if($config[$i][0] == "") $config[$i][0] = 0;
            fwrite($config_file, "Device_No_".$i."=".$config[$i][0]."\n");
        }

        fwrite($config_file, "\n[SHEDULE]\n");
        for($i=0; $i < 4; ++$i){
            fwrite($config_file, "Device_No_".$i."=".$config[$i][1]."-->".$config[$i][2]."\n");
        }

        fclose($config_file);
    }// end of checking submit button


    header("Location: index.php");
?>