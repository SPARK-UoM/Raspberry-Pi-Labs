<?php
    $config = array(
        array(0, '', ''),
        array(0, '', ''),
        array(0, '', ''),
        array(0, '', ''),
    );

    $config_file = fopen("config.txt", "r");

    $config_lev = "None";

    // read the configuration file
    while(!feof($config_file)){
        $line = substr(fgets($config_file), 0, -1); // read line by line

        if ($line == "[FORCELOG]" || $line == "[SHEDULE]") $config_lev = $line; // store the configuration level
        
        $temp = explode("=", $line); // split the string from "=" character

        if ($config_lev == "[FORCELOG]" && count($temp) > 1){ 
            $device_id = (int)substr($temp[0], -1); // extract the device id from the last character of first element
            $config[$device_id][0] = (int)$temp[1]; // update config array with the value readed
        }// end of force log configuration

        if ($config_lev == "[SHEDULE]" && count($temp) > 1){ 
            $device_id = (int)substr($temp[0], -1); // extract the device id from the last character of first element

            $temp = explode("-->", $temp[1]); // split the string by "--> symbol"

            if($temp[0] != "UNSHEDULED") $config[$device_id][1] = $temp[0]; // update the start time to the array
            if($temp[1] != "UNSHEDULED") $config[$device_id][2] = $temp[1]; // update the end time to the array

        }// end of shedule configuration
    }//end of reading while loop

    fclose($config_file);
?>


<!DOCTYPE html>

<html>
    <head>
        <meta name="theme-color" content="#4285f4">
        <link rel="stylesheet" href="style.css">
        <title>Project 1</title>
    </head>

    <body>
        <div class = "Main-content-box">

        <div class = "Title-wrapper">
            <h1> IoT house device control </h1>
        </div>

        <div class = "Content-wrapper">
            <form action="submition.php" method="POST">
                <table>
                    <tr> <th>Device ID</th>  <th>Force ON ?</th> <th>Start Time</th> <th>End time</th></tr>

                    <?php
                    
                        for ($i = 1; $i <= 4; ++$i){
                            $check = "";
                            if($config[$i-1][0]) $check = "checked";
                            else $check = "";
                            echo "<tr> <td>Device No $i</td> <td> <input type = 'checkbox' name = 'Status_dev$i' $check> </td>  <td> <input type='time' name='Start_dev$i' value = '".$config[$i-1][1]."'> </td> <td> <input type='time' name='End_dev$i' value = '".$config[$i-1][2]."'> </td> </tr>\n";

                        }// end of the for loop
                    
                    ?>

                    
                </table>

                <br>

                <button type="submit" name="submit"> Submit Configuration </button>

            </form>

        </div>
        




        </div>    
    </body>
</html>