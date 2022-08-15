<?php
require VENDOR_DIR.'autoload.php';
use Dompdf\Dompdf;

class Utility extends Database {

    protected $responseBody;

    function __construct($db) {
        $this->db = $db;
        $this->responseBody	= array();
    }

    /**
     * convert ojects to array
     *
     * @param array $array
     * @return object
     */
    public function arrayToObject($array)
    {
        return (object) $array;

    }

    /**
     * convert arrays to object
     *
     * @param object $object
     * @return array
     */
    public function objectToArray($object)
    {
	    return (array) $object;

    }

    public function randID($character, $length = 5) { 
        $alphabets = 'aeioubcdfghjklmnpqrstvwxyz'; 
        $numbers = '0123456789'; 

        $idnumber = '';
        switch ($character) {
            case 'numeric':
            case 'num':
                for ($i = 0; $i < $length; $i++) { 
                    $idnumber.= substr($numbers, (rand()%(strlen($numbers))), 1);
                } 
                break;

            case 'alphabetic':
            case 'alpha':
                for ($i = 0; $i < $length; $i++) { 
                    $idnumber.= substr($alphabets, (rand()%(strlen($alphabets))), 1);
                } 
                break;
        }

         
        return $idnumber; 
    }

    public function niceDateFormat($date) {
        $timestamp = strtotime($date);
        $niceFormat = date('D j, M Y h:ia', $timestamp);

        return $niceFormat;
    }

    public function displayFlashMessage()
    {
        if (isset($_SESSION[FLASH])) { ?>
            <script>
                <?php foreach ($_SESSION[FLASH] as $key => $flashMsg) {
                    if ($flashMsg['type'] == FLASH_ERROR) { ?>
                        toastr.error('<?php echo $flashMsg['message'] ?>');
                    <?php }
                    elseif ($flashMsg['type'] == FLASH_SUCCESS) { ?>
                        toastr.success('<?php echo $flashMsg['message'] ?>');
                    <?php }
                    elseif ($flashMsg['type'] == FLASH_INFO) { ?>
                        toastr.info('<?php echo $flashMsg['message'] ?>');
                    <?php }
                    elseif ($flashMsg['type'] == FLASH_WARNING) { ?>
                        toastr.warning('<?php echo $flashMsg['message'] ?>');
                    <?php }
                } ?>
            </script>
            <?php unset($_SESSION[FLASH]);
        }

        return;
    }

    public function addMessage($message, $type, $redirectUrl = null)
    {
        $message = [
            'message' => $message,
            'type' => $type
        ];

        if (!isset($_SESSION[FLASH])) {
            $_SESSION[FLASH] = [$message];
        } else {
            array_push($_SESSION[FLASH], $message);
        }

        if (isset($redirectUrl)) $this->doRedirect($redirectUrl);
    }

    public function returnFormInput($name)
    {
        $formInput = '';
        if (isset($_SESSION['formInput'][$name])) {
            $formInput = $_SESSION['formInput'][$name];
            unset($_SESSION['formInput'][$name]);
        }

        echo $formInput;
    }

    public function returnSelectInput($name, $optionValue)
    {
        $selected = '';
        if (isset($_SESSION['formInput'][$name])) {
            $formInput = $_SESSION['formInput'][$name];

            if ($formInput == $optionValue) {
                unset($_SESSION['formInput'][$name]);
                $selected = 'selected';
            }

        }

        echo $selected;
    }

    public function returnSelectInputFromDB($value, $dbArray)
    {
        $selected = '';

        if (is_array($dbArray) AND in_array($value, $dbArray)) {
            $selected = 'selected';
        }

        echo $selected;
    }

    public function doRedirect($redirectUrl = null)
    {
        $redirectUrl = !empty($redirectUrl) ? $redirectUrl : BASE_URL;
        header("Location: ".$redirectUrl);
    }
}

?>