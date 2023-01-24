<?php
$address1 = isset($_GET['address1']) ? $_GET['address1'] : '';
$address2 = isset($_GET['address2']) ? $_GET['address2'] : '';
$city = isset($_GET['city']) ? $_GET['city'] : '';
$stateAbbr = isset($_GET['state_val']) ? $_GET['state_val'] : '';
$zipCode = isset($_GET['zipCode']) ? $_GET['zipCode'] : '';

$request_doc_template = <<<EOT
<?xml version="1.0"?>
<AddressValidateRequest USERID="781NAROL0145">
	<Revision>1</Revision>
	<Address ID="0">
		<Address1>$address1</Address1>
		<Address2>$address2</Address2>
		<City>$city</City>
		<State>$stateAbbr</State>
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
$xml = simplexml_load_string($response) ;

$error_description = isset($xml->Address->Error->Description) ? $xml->Address->Error->Description : '';
$address1 = isset($xml->Address->Address1) ? $xml->Address->Address1 : '-';
$address2 = isset($xml->Address->Address2) ? $xml->Address->Address2 : '-';
$city = isset($xml->Address->City) ? $xml->Address->City : '-';
$state = isset($xml->Address->State) ? $xml->Address->State : '-';
$zipCode = isset($xml->Address->Zip5) ? $xml->Address->Zip5 : '-';


$html = '';
if($error_description != ''){
	$html .= '<span class="text-danger" id="error_span">'.$error_description.'</span><br><br>';
}

$html .= '<label for="">Address Line 1: </label>';
$html .= '<span id="fill_address1"> '.$address1.'</span><br>';
$html .= '<label for="">Address Line 2: </label>';
$html .= '<span id="fill_address2"> '.$address2.'</span><br>';
$html .= '<label for="">City: </label>';
$html .= '<span id="fill_city"> '.$city.'</span><br>';
$html .= '<label for="">State: </label>';
$html .= '<span id="fill_state"> '.$state.'</span><br>';
$html .= '<label for="">Zip Code: </label>';
$html .= '<span id="fill_zipCode"> '.$zipCode.'</span><br>';

echo $html;
?>