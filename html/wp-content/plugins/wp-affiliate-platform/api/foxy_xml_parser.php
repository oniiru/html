<?
/**
 * FoxyCart XML to Affiliate Commission
 * 
 */
/*
	DESCRIPTION: =================================================================
	Writes the FoxyCart XML Datafeed to a simple CSV file.
	By default it will write to separate files per product, so a product ABC1 will write to ABC1.csv.
	By default it will record the customer name, product quantity, product code, product name, transaction date, and transaction ID.
	You can easily modify what fields it writes by editing the code below.
	
	PHP 5 (FIVE) is required.
	
	WARNING: =====================================================================
	It's not safe to leave your customer data sitting on your server.
	The ht.access file is provided for your convenience, but it must be renamed to .htaccess.
	Nobody but you will be held responsible if something bad happens because you left customer data out in the open.
	
	USAGE: =======================================================================
	- Place this file somewhere on your server.
	- Edit the $myKey to match the key you put in your FoxyCart admin.
	- Rename the ht.access to .htaccess in your data/ folder.
	- Change the IP addresses in the .htaccess folder to the IP addresses you need access from.
	- Save.
	- In the FoxyCart admin set the datafeed URL to the address for this script.
	
	TESTING: =====================================================================
	- Use http://wiki.foxycart.com/integration:foxycart:test_xml_post to test.
	- Test until you get your script working properly.
	- If you get an error about writing to the file you may need to change the permissions on the 'data' folder to 777.
	
	REQUIREMENTS: ================================================================
	- PHP5 with fopen and fwrite enabled
*/

// ======================================================================================
// CHANGE THIS DATA:
// Set the key you entered in your FoxyCart.com admin.
// ======================================================================================
$myKey = 'GVmcKpikZHVL1zp8qElQzE1bCjAS8heI4vPIJXXzvMbrcutxYFdsa4tGhUpj'; // your foxy cart datafeed key

// The filename that you'd like to write to.
// For security reasons, this file should either be outside of your public web root,
// or it should be written to a directory that doesn't have public access (like with an .htaccess directive).
$folder = 'test.csv';
$delimiter = ','; // Keep it as a comma to use a CSV format



// You can change the following data if you want to customize what data gets written.
if (isset($_POST["FoxyData"])) {
	// Get the raw data and initialize variables
	$output = '';
	$FoxyData_encrypted = urldecode($_POST["FoxyData"]);
	$FoxyData_decrypted = rc4crypt::decrypt($myKey,$FoxyData_encrypted);
	$xml = new SimpleXMLElement($FoxyData_decrypted);
	
	// Uncomment this line to debug
	// echo print_r($transactions, true);
	
	foreach ($xml->transactions->transaction as $transaction) {
		// Loop through to get the product code, name, customer name, date, and transaction ID
		$transaction_customer_name = $transaction->customer_last_name . ', ' . $transaction->customer_first_name;
		$transaction_date = $transaction->date;
		$transaction_id = $transaction->id;
		foreach ($transaction->transaction_details->transaction_detail as $product) {
			// Get the product details
			$product_code = $product->product_code;
			$product_name = $product->product_name;
			$product_quantity = $product->product_quantity;
			if ($product_code == '') {
				$product_code = $product_name;
			}
			
			// Create an array of what you want to write to the file
			$output[] = escape_quote($transaction_customer_name);
			$output[] = escape_quote($product_quantity);
			$output[] = escape_quote($product_name);
			$output[] = escape_quote($product_code);
			$output[] = escape_quote($transaction_date);
			$output[] = escape_quote($transaction_id);
			// Wrap the values in single quotes
			array_walk($output, 'quote');
			$output = implode($delimiter, $output) . "\n";
			
			// Write it to a file
			$fh = fopen($folder . $product_code . '.csv', 'a') or die("Couldn't open file for writing. Check your file and folder ownerships and permissions."); 
			fwrite($fh, $output);
			fclose($fh);
			
			// Uncomment for debugging
			// echo $folder . $product_code . '.csv' . ' :: ' . $output . "\n";
		}
	}
	
	// Write the file and return 'foxy' so FoxyCart doesn't feed the transaction again
	echo 'foxy';
} else {
	$fh = fopen($folder . 'errors.txt', 'a') or die("Couldn't open $file for writing!"); 
	fwrite($fh, 'error occurred on ' . time());
	fclose($fh);
	echo 'error';
}


// Function to escape commas, so commas in the user inputted data doesn't mess up our CSV
function quote(&$string, $key) {
	$string = '\'' . $string . '\'';
}
function escape_quote($string) {
	return str_replace("'", "\'", $string);
}






// ======================================================================================
// RC4 ENCRYPTION CLASS
// Do not modify.
// ======================================================================================
/**
 * RC4Crypt 3.2
 *
 * RC4Crypt is a petite library that allows you to use RC4
 * encryption easily in PHP. It's OO and can produce outputs
 * in binary and hex.
 *
 * (C) Copyright 2006 Mukul Sabharwal [http://mjsabby.com]
 *     All Rights Reserved
 *
 * @link http://rc4crypt.devhome.org
 * @author Mukul Sabharwal <mjsabby@gmail.com>
 * @version $Id: class.rc4crypt.php,v 3.2 2006/03/10 05:47:24 mukul Exp $
 * @copyright Copyright &copy; 2006 Mukul Sabharwal
 * @license http://www.gnu.org/copyleft/gpl.html
 * @package RC4Crypt
 */
 
/**
 * RC4 Class
 * @package RC4Crypt
 */
class rc4crypt {
	/**
	 * The symmetric encryption function
	 *
	 * @param string $pwd Key to encrypt with (can be binary of hex)
	 * @param string $data Content to be encrypted
	 * @param bool $ispwdHex Key passed is in hexadecimal or not
	 * @access public
	 * @return string
	 */
	function encrypt ($pwd, $data, $ispwdHex = 0)
	{
		if ($ispwdHex)
			$pwd = @pack('H*', $pwd); // valid input, please!
 
		$key[] = '';
		$box[] = '';
		$cipher = '';
 
		$pwd_length = strlen($pwd);
		$data_length = strlen($data);
 
		for ($i = 0; $i < 256; $i++)
		{
			$key[$i] = ord($pwd[$i % $pwd_length]);
			$box[$i] = $i;
		}
		for ($j = $i = 0; $i < 256; $i++)
		{
			$j = ($j + $box[$i] + $key[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}
		for ($a = $j = $i = 0; $i < $data_length; $i++)
		{
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipher .= chr(ord($data[$i]) ^ $k);
		}
		return $cipher;
	}
	/**
	 * Decryption, recall encryption
	 *
	 * @param string $pwd Key to decrypt with (can be binary of hex)
	 * @param string $data Content to be decrypted
	 * @param bool $ispwdHex Key passed is in hexadecimal or not
	 * @access public
	 * @return string
	 */
	function decrypt ($pwd, $data, $ispwdHex = 0)
	{
		return rc4crypt::encrypt($pwd, $data, $ispwdHex);
	}
}

?>