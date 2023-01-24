<?php
if ($_SERVER['HTTP_HOST'] == 'localhost') {
    $host = 'http://localhost/test_sarah/';
    //database content
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "test_sarah";
} elseif ($_SERVER['HTTP_HOST'] == 'eros.narola.online:551') {
    $host = 'https://eros.narola.online:551/pma/kap/data/test_sarah/';
    //database content
    $hostname = "192.168.1.203";
    $username = "test_sarah";
    $password = "7WqDHhCpZfdeWnu4";
    $database = "test_sarah";
} elseif ($_SERVER['HTTP_HOST'] == 'eros.narola.online') {
    $host = 'https://eros.narola.online:551/pma/kap/data/test_sarah/';
    //database content
    $hostname = "192.168.1.203";
    $username = "test_sarah";
    $password = "7WqDHhCpZfdeWnu4";
    $database = "test_sarah";
}

// Create connection

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Set MySQLi to throw exceptions 

try {
    $conn = new mysqli($hostname, $username, $password, $database);
} catch (mysqli_sql_exception $e) {
    // $e is the exception, you can use it as you wish 
    die("Unfortunately, the details you entered for connection are incorrect!");
}

// Get state name
$query = "SELECT id, stateName, abbr FROM state";
$result = $conn->query($query);
if ($result->num_rows > 0) {
    $stateNames = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
// Insert data into table
$address1 = $address2 = $city = $stateabbr = $zipCode = $options = $message = '';
$errors = array();
if (!empty($_POST)) {
    $address1 = isset($_POST['address1']) ? strtoupper($_POST['address1']) : '';
    $address2 = isset($_POST['address2']) ? strtoupper($_POST['address2']) : '';
    $city = isset($_POST['city']) ? strtoupper($_POST['city']) : '';
    $stateabbr = isset($_POST['stateName']) ? strtoupper($_POST['stateName']) : '';
    $zipCode = isset($_POST['zipCode']) ? strtoupper($_POST['zipCode']) : '';
    $options = strtoupper($_POST['fill_address_options']);
    // if (!preg_match("/^[0-9a-zA-Z ]+$/", $address1)) {
    //     $errors['address1_error'] = "Address must contain only alphabets";
    // }
    // if (!preg_match("/^[0-9a-zA-Z ]+$/", $address2)) {
    //     $errors['address2_error'] = "Address must contain only alphabets";
    // }
    // if (!preg_match("/^[0-9a-zA-Z ]+$/", $city)) {
    //     $errors['city_error'] = "Address must contain only alphabets";
    // }
    // if (!preg_match("/^([0-9]{5})(-[0-9]{4})?$/i", $zipCode)) {
    //     $errors['zipCode_error'] = "Enter valid Zip code";
    // }
    if (!$errors) {
        $usps_error = 0;
        if ($options == 'STANDARDIZED') {

            $request_doc_template = <<<EOT
            <?xml version="1.0"?>
            <AddressValidateRequest USERID="781NAROL0145">
                <Revision>1</Revision>
                <Address ID="0">
                    <Address1>$address1</Address1>
                    <Address2>$address2</Address2>
                    <City>$city</City>
                    <State>$stateabbr</State>
                    <Zip5>$zipCode</Zip5>
                    <Zip4/>
                </Address>
            </AddressValidateRequest>
            EOT;
            // prepare xml doc for query string
            $doc_string = preg_replace('/[\t\n]/', '', $request_doc_template);
            $doc_string = urlencode($doc_string);
            $url = "http://production.shippingapis.com/ShippingAPI.dll?API=Verify&XML=" . $doc_string;

            // perform the get
            $response = file_get_contents($url);
            $xml = simplexml_load_string($response);

            $error_description = isset($xml->Address->Error->Description) ? $xml->Address->Error->Description : '';

            $address1 = $xml->Address->Address1;
            $address2 = $xml->Address->Address2;
            $city = $xml->Address->City;
            $stateabbr = $xml->Address->State;
            $zipCode = $xml->Address->Zip5;

            if (!empty($error_description)) {
                $usps_error = 1;
                $message = $error_description;
                $error_message = 1;
            } else {
                if (empty($address1) || empty($address2) || empty($city) || empty($stateabbr) || empty($zipCode)) {
                    $usps_error = 1;
                    $message = "Invalid address selected.";
                    $error_message = 1;
                }
            }
        }

        if ($usps_error == 0) {
            $sql = "INSERT INTO tbl_address (address1,address2,city,stateAbbr,zipCode,options) VALUES ('$address1','$address2','$city','$stateabbr','$zipCode', '$options')";
            if (mysqli_query($conn, $sql)) {
                $message = "New record has been added successfully !";
            } else {
                echo "Error: " . $sql . ":-" . mysqli_error($conn);
            }
        }
    }
    mysqli_close($conn);
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Address</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
        .address-body {
            display: block;
            width: 50%;
            margin: 0 auto;
            border-radius: 0.3em;
            box-shadow: 0 0 0 0.8em lightgrey;
        }

        .address-body-modal,
        .address-body-modal2 {
            border: 1px solid lightgrey;
        }

        .btn-original {
            color: #fff;
            background-color: #0d6efd;
        }

        .btn-standardized {
            color: #fff;
            background-color: lightskyblue;
        }

        .switch-field {
            display: flex;
            margin-bottom: 36px;
            overflow: hidden;
        }

        .switch-field input {
            position: absolute !important;
            clip: rect(0, 0, 0, 0);
            height: 1px;
            width: 1px;
            border: 0;
            overflow: hidden;
        }

        .switch-field label {
            background-color: #e4e4e4;
            color: rgba(0, 0, 0, 0.6);
            font-size: 14px;
            line-height: 1;
            text-align: center;
            padding: 8px 16px;
            margin-right: -1px;
            border: 1px solid rgba(0, 0, 0, 0.2);
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.3), 0 1px rgba(255, 255, 255, 0.1);
            transition: all 0.1s ease-in-out;
        }

        .switch-field label:hover {
            cursor: pointer;
        }

        .switch-field input:checked+label {
            background-color: #0d6efd;
            color: #fff;
            box-shadow: none;
        }

        .switch-field label:first-of-type {
            border-radius: 4px 0 0 4px;
        }

        .switch-field label:last-of-type {
            border-radius: 0 4px 4px 0;
        }

        .form {
            max-width: 600px;
            font-family: "Lucida Grande", Tahoma, Verdana, sans-serif;
            font-weight: normal;
            line-height: 1.625;
            margin: 8px auto;
            padding: 16px;
        }

        h2 {
            font-size: 18px;
            margin-bottom: 8px;
        }
    </style>
</head>

<body>
    <div class="container p-5">
        <div class="address-body p-4">
            <h3>Address Validator</h3>
            <p>Validate/Standardize addresses using USPS</p>
            <hr>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="address_form_submit" name="address_form_submit">
                <div class="form-group mb-3">
                    <label for="" class="mb-2">Address Line 1</label>
                    <input type="text" name="address1" class="form-control" id="address1" aria-describedby="emailHelp" placeholder="Enter Address Line 1" value="<?php if (!empty($_POST['address1'])) {
                                                                                                                                                                        echo $_POST['address1'];
                                                                                                                                                                    }  ?>" required>
                    <span class="text-danger" id="address_error_span"><?php if (isset($errors['address1_error'])) echo $errors['address1_error'] ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="" class="mb-2">Address Line 2</label>
                    <input type="text" name="address2" class="form-control" id="address2" aria-describedby="emailHelp" placeholder="Enter Address Line 2" value="<?php if (!empty($_POST['address2'])) {
                                                                                                                                                                        echo $_POST['address2'];
                                                                                                                                                                    }  ?>" required>
                    <span class="text-danger" id="address2_error_span"><?php if (isset($errors['address2_error'])) echo $errors['address2_error'] ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="" class="mb-2">City</label>
                    <input type="text" name="city" class="form-control" id="city" aria-describedby="emailHelp" placeholder="Enter City" value="<?php if (!empty($_POST['city'])) {
                                                                                                                                                    echo $_POST['city'];
                                                                                                                                                }  ?>" required>
                    <span class="text-danger" id="city_error_span"><?php if (isset($errors['city_error'])) echo $errors['city_error'] ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="" class="mb-2">State</label>
                    <select class="form-control" id="stateName" name="stateName">
                        <option value="">Select State</option>
                        <?php
                        foreach ($stateNames as $stateName) {

                        ?>
                            <option value="<?php echo $stateName['abbr']; ?>" <?php echo (!empty($_POST['stateName']) && $_POST['stateName'] == $stateName['abbr']) ? 'selected' : ''; ?>><?php echo $stateName['stateName']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <span class="text-danger" id="state_error_span"><?php if (isset($errors['state_error'])) echo $errors['state_error'] ?></span>
                </div>
                <div class="form-group mb-3">
                    <label for="" class="mb-2">Zip Code</label>
                    <input type="text" name="zipCode" class="form-control" id="zipCode" aria-describedby="emailHelp" placeholder="Enter Zip Code (Eg: 94556 or 00501-99950)" value="<?php if (!empty($_POST['zipCode'])) {
                                                                                                                                                                echo $_POST['zipCode'];
                                                                                                                                                            }  ?>" required>
                    <span class="text-danger" id="zipCode_error_span"><?php if (isset($errors['zipCode_error'])) echo $errors['zipCode_error'] ?></span>
                </div>
                <input type="hidden" name="fill_address_options" id="fill_address_options" value="<?php echo isset($options) ? $options : 'ORIGINAL'; ?>">
                <div class="text-center">
                    <button type="button" id="address_popup-open" class="btn btn-primary" onclick="getInputValue();">
                        VALIDATE
                    </button>
                    <input type="button" class="btn btn-light" value="RESET" onclick="clearInput()">
                </div>
            </form>
        </div>
    </div>

    <button type="button" id="open_popup_ready" class="btn btn-primary d-none" data-bs-toggle="modal" data-bs-target="#addressModal">Open Popup</button>

    <!-- Modal -->
    <div class="modal fade" id="addressModal" tabindex="-1" aria-labelledby="addressModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addressModalLabel">Save Address</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Which address format do you want to save?</p>
                    <div class="switch-field">
                        <input type="radio" id="original" name="options" <?php echo (isset($options) && $options == "ORIGINAL") ? "checked" : (!isset($options) || $options == '' ? 'checked' : ''); ?> value="ORIGINAL" onchange="fill_address_options(this);" />
                        <label for="original">ORIGINAL</label>
                        <input type="radio" id="standardized" name="options" <?php if (isset($options) && $options == "STANDARDIZED") echo "checked"; ?> value="STANDARDIZED" onchange="fill_address_options(this);" onclick="api_Call();" />
                        <label for="standardized">STANDARDIZED (USPS)</label>
                    </div>

                    <div class="address-body-modal rounded p-3 mt-3 mb-2" id="address-body-modal">
                        <label for="">Address Line 1:</label>
                        <span id="fill_address1"><?php echo (!empty($_POST['address1'])) ? $_POST['address1'] : ''; ?></span><br>
                        <label for="">Address Line 2:</label>
                        <span id="fill_address2"><?php echo (!empty($_POST['address1'])) ? $_POST['address2'] : ''; ?></span><br>
                        <label for="">City:</label>
                        <span id="fill_city"><?php echo (!empty($_POST['address1'])) ? $_POST['city'] : ''; ?></span><br>
                        <label for="">State:</label>
                        <span id="fill_state"><?php echo (!empty($_POST['address1'])) ? $_POST['stateName'] : ''; ?></span><br>
                        <label for="">Zip Code:</label>
                        <span id="fill_zipCode"><?php echo (!empty($_POST['address1'])) ? $_POST['zipCode'] : ''; ?></span><br>
                    </div>
                    <div class="address-body-modal2 rounded p-3 mt-3 mb-2" id="address-body-modal2">
                        <?php
                        if (!empty($xml->Address)) {
                            $address1 = $xml->Address->Address1;
                            $address2 = $xml->Address->Address2;
                            $city = $xml->Address->City;
                            $stateabbr = $xml->Address->State;
                            $zipCode = $xml->Address->Zip5;
                        ?>

                            <label for="">Address Line 1:</label>
                            <span id="fill_address1"><?php echo (!empty($address1)) ? $address1 : ''; ?></span><br>
                            <label for="">Address Line 2:</label>
                            <span id="fill_address2"><?php echo (!empty($address2)) ? $address2 : ''; ?></span><br>
                            <label for="">City:</label>
                            <span id="fill_city"><?php echo (!empty($city)) ? $city : ''; ?></span><br>
                            <label for="">State:</label>
                            <span id="fill_state"><?php echo (!empty($stateabbr)) ? $stateabbr : ''; ?></span><br>
                            <label for="">Zip Code:</label>
                            <span id="fill_zipCode"><?php echo (!empty($zipCode)) ? $zipCode : ''; ?></span><br>
                        <?php
                        }

                        ?>
                    </div>

                </div>
                <?php if (!empty($message)) {
                    $label = isset($error_message) && $error_message == 1 ? 'danger' : 'success';
                ?>
                    <div class="alert alert-<?php echo $label; ?> m-3" role="alert" id="alert_popup">
                        <?php echo $message; ?>
                    </div>
                <?php
                }
                ?>

                <div class="modal-footer">
                    <button type="button" id="address_submit_save" class="btn btn-primary">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script type="text/javascript">
        document.getElementById('address-body-modal2').style.display = "none";
        <?php if (!empty($message)) {
        ?>
            document.getElementById('open_popup_ready').click();
            document.getElementById('address_submit_save').disabled = true;

        <?php } ?>
        if (document.querySelector('input[name="fill_address_options"]').value == 'STANDARDIZED') {
            document.getElementById('address-body-modal2').style.display = "block";
            document.getElementById('address-body-modal').style.display = "none";
        }

        function clearInput() {
            document.getElementById('address1').value = '';
            document.getElementById('address2').value = '';
            document.getElementById('city').value = '';
            document.getElementById('zipCode').value = '';

            document.getElementById('address_error_span').innerHTML = '';
            document.getElementById('address2_error_span').innerHTML = '';
            document.getElementById('city_error_span').innerHTML = '';
            document.getElementById('state_error_span').innerHTML = '';
            document.getElementById('zipCode_error_span').innerHTML = '';

            document.getElementById("stateName").selectedIndex = 0;
            document.getElementById('alert_popup').style.display = "none";
        }

        function getInputValue() {


            var address1 = document.getElementById("address1").value;
            var address2 = document.getElementById("address2").value;
            var city = document.getElementById("city").value;
            var zipCode = document.getElementById("zipCode").value;
            var state_val = document.getElementById("stateName").value;

            // var alphabet_validation = /^[0-9a-zA-Z ]+$/;
            var zipCode_validation = /^([0-9]{5})(-[0-9]{4})?$/i;


            if (address1 == '') {
                document.getElementById('address_error_span').innerHTML = 'Address field is required.';
            } else {
                document.getElementById('address_error_span').innerHTML = '';
            }
            // if (!alphabet_validation.test(address1)) {
            //     console.log('if');
            //     document.getElementById('address_error_span').innerHTML = 'Address must contain only alphabets.';
            // }

            if (address2 == '') {
                document.getElementById('address2_error_span').innerHTML = 'Address field is required.';
            } else {
                document.getElementById('address2_error_span').innerHTML = '';
            }
            // if (!alphabet_validation.test(address2)) {
            //     document.getElementById('address2_error_span').innerHTML = 'Address must contain only alphabets.';
            // }

            if (city == '') {
                document.getElementById('city_error_span').innerHTML = 'City field is required.';
            } else {
                document.getElementById('city_error_span').innerHTML = '';
            }
            // if (!alphabet_validation.test(city)) {
            //     document.getElementById('city_error_span').innerHTML = 'City must contain only alphabets.';
            // }

            if (state_val == '') {
                document.getElementById('state_error_span').innerHTML = 'State field is required.';
            } else {
                document.getElementById('state_error_span').innerHTML = '';
            }

            if (zipCode == '') {
                document.getElementById('zipCode_error_span').innerHTML = 'Zip code field is required.';
            } else {
                document.getElementById('zipCode_error_span').innerHTML = '';
            }

            if (!zipCode_validation.test(zipCode)) {
                document.getElementById('zipCode_error_span').innerHTML = 'Enter valid Zip code.';
            }

            // if (address1 != '' && address2 != '' && city != '' && zipCode != '' && state_val != '' && alphabet_validation.test(address1) && alphabet_validation.test(address2) && alphabet_validation.test(city) && zipCode_validation.test(zipCode)) {
            //     document.getElementById('open_popup_ready').click();
            // }

            if (address1 != '' && address2 != '' && city != '' && zipCode != '' && state_val != '' && zipCode_validation.test(zipCode)) {
                document.getElementById('open_popup_ready').click();
                api_Call();
                document.getElementById('address_submit_save').disabled = false;
            }
            // Displaying the value

            document.getElementById('fill_address1').innerHTML = address1;
            document.getElementById('fill_address2').innerHTML = address2;
            document.getElementById('fill_city').innerHTML = city;
            document.getElementById('fill_state').innerHTML = state_val;
            document.getElementById('fill_zipCode').innerHTML = zipCode;
            document.getElementById('alert_popup').style.display = "none";

        }

        function fill_address_options(src) {
            document.getElementById('fill_address_options').value = src.value;
            if (src.value == 'ORIGINAL') {
                document.getElementById('address-body-modal').style.display = "block";
                document.getElementById('address-body-modal2').style.display = "none";
            } else if (src.value == 'STANDARDIZED') {
                document.getElementById('address-body-modal2').style.display = "block";
                document.getElementById('address-body-modal').style.display = "none";
            }
        }

        document.getElementById("address_submit_save").addEventListener("click", address_form_submit);

        function address_form_submit() {
            document.forms['address_form_submit'].submit();
        }

        function api_Call() {
            var address1 = document.getElementById("address1").value;
            var address2 = document.getElementById("address2").value;
            var city = document.getElementById("city").value;
            var zipCode = document.getElementById("zipCode").value;
            var state_val = document.getElementById("stateName").value;
            // Creating Our XMLHttpRequest object 
            var xhr = new XMLHttpRequest();

            // Making our connection  
            var url = '<?php echo $host ?>api.php?address1=' + address1 + '&address2=' + address2 + '&city=' + city + '&state_val=' + state_val + '&zipCode=' + zipCode;
            xhr.open("GET", url, true);

            // function execute after request is successful 
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById('address-body-modal2').innerHTML = this.responseText;
                }
            }
            // Sending our request 
            xhr.send();
        }
    </script>
</body>

</html>