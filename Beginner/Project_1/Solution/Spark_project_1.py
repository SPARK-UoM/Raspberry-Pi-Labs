# !usr/bin/python

# Pin allocation
# GPIO 4  -> Relay 1
# GPIO 17 -> Relay 2
# GPIO 22 -> Relay 3
# GPIO 27 -> Relay 4


# import necessary files
import RPi.GPIO as GPIO
from datetime import date, datetime
from time import sleep

GPIO_list = [4,17,22,27]

def read_config():
    """
    DOCSTRING: This function will read the config file and extract data
    return: will return a dictionary containing the configuration data
    """
    with open('config.txt', 'r') as config_file:
        content = config_file.readlines()

    device_log = {} # this dictionary will hold the configuration data extracted
    config_level = "" # this will hold the configuration level

    for line in content:
        line = line[:-1] # remove the new line character from the string

        if(line == "[FORCELOG]" or line == "[SHEDULE]"): config_level = line #look for the configuration level and update it

        if(config_level == '[FORCELOG]'):
            temp = line.split("=") # split the line by =
            if(len(temp) <= 1): continue # it is only worth only if split has two components

            device_log[temp[0]] = [int(temp[1]), "", ""] # build the dictionary

        if(config_level == '[SHEDULE]'):
            temp = line.split("=") # split the line by =
            if(len(temp) <= 1): continue # it is only worth only if split has two components
        
            device_log[temp[0]][1] = temp[1].split("-->")[0] # add start time
            device_log[temp[0]][2] = temp[1].split("-->")[1] # add start time

    return device_log

def setup_GPIOS():
    """
    DOCSTRING: This function will setup basic GPIO settings
    return: None
    """
    # set the numbring mode of the gpio pins
    GPIO.setmode(GPIO.BCM)
    # set GPIO pin for output
    for GPIO_pin in GPIO_list:
        GPIO.setup(GPIO_pin, GPIO.OUT)

def update_GPIO(config_data):
    """
    DOCSTRING: This function will update the GPIO pins within1 seconds
    """
    current_time = datetime.now() # get the current time from the system
    
    for key in config_data:
        relay_GPIO = GPIO_list[int(key[-1])] # devices are scrambles so get the id
        temp_config = config_data[key] # extract the configuration for the device

        # if the configuration says force on then on and continue
        if(temp_config[0]):
            GPIO.output(relay_GPIO, 0)
            print('Iot Sytem Status -># [+] {} Force Started'.format(key))
            continue

        # if configuration didnt forced to turn on check the time stamps
        else:
            if(temp_config[1] == "UNSHEDULED" or temp_config[1] == "UNSHEDULED"):
                print('Iot Sytem Status -># [-] {} not configured correctly!! Moving on'.format(key))
                GPIO.output(relay_GPIO, 1)
            
            else:
                try:
                    start_time = current_time.replace(hour=int(temp_config[1].split(":")[0]), minute= int(temp_config[1].split(":")[1]))
                    end_time = current_time.replace(hour=int(temp_config[2].split(":")[0]), minute= int(temp_config[2].split(":")[1]))

                    if(start_time < current_time and current_time < end_time):
                        GPIO.output(relay_GPIO, 0)
                        print('Iot Sytem Status -># [+] {} Started on Sheduled Time'.format(key))
                    else:
                        GPIO.output(relay_GPIO, 1)
                        print('Iot Sytem Status -># [+] {} Ended on Sheduled Time'.format(key))


                except:
                    print('Iot Sytem Status -># [-] {} not configured correctly!! Moving on'.format(key))
                    GPIO.output(relay_GPIO, 1)

    

    

def main():
    setup_GPIOS() # set GPIOs once

    while True:
        update_GPIO(read_config()) # read configuration and update GPIOS
        sleep(2) # sleep for 2 seconds

if __name__ == "__main__":
    
    try:
        main()
    except KeyboardInterrupt:
        exit()