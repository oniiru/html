<?php
require_once dirname(__FILE__) . '/../class-payperdownload.php';
$ppd = new PayPerDownload();

$path = dirname(__FILE__).'/../../files/';
$url_root = DL_PAY_PER_DOWNLOAD_PLUGIN_URL . '/files/';

if( !current_user_can('manage_options')) {
	$url = wp_login_url();
	header('Location: '.$url );
	return;
}

if($_POST) {
	if($_POST['add']) {
	
		// validate the inputs
		$is_valid = ($_FILES['file']['error'] <=0) &&
					(!empty($_POST['name'])) &&
					(!empty($_POST['cost'])) &&
					(is_numeric($_POST['cost']));
		$errors = array();
		if(empty($_POST['name'])) {
			$errors[] = "Error: Please supply a name.";
		}
		if(empty($_POST['cost'])) {
			$errors[] = "Error: Please supply a cost.";
		}
		if(!is_numeric($_POST['cost'])) {
			$errors[] = "Error: The cost must be a valid number.";
		}
		if($_FILES['file']['error'] > 0) {
			$errors[] = "Error: Invalid file. err=".$_FILES['file']['error'];
		}
		if(count($errors)) {
			echo "<div class='updated'>";
			foreach($errors as $error) {
				echo "<p><strong>$error</strong></p>";
			}
			echo "</div>";
		} else {
			$name = $_POST['name'];
			$cost = floatval($_POST['cost']);
			$file = $_FILES['file']['name'];
			
			$file_path = $path . $file;
			if(file_exists($file_path)) {
				// a file with this name already exists
				echo "<div class='updated'><p><strong>";
				echo "Error: ".$_FILES['file']['name']." already exists. Delete first.";
				echo "</strong></p></div>";
			} else {
				// move the file to the folder
				if(move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
					if($ppd->add_product($name, $cost, $file)!=0) {
						echo "<div class='updated'><p><strong>";
						echo "Product added.";
						echo "</strong></p></div>";
					} else {
						echo "<div class='updated'><p><strong>";
						echo "Failed to add the product to the database";
						echo "</strong></p></div>";
					}
				
				} else {
					echo "<div class='updated'><p><strong>";
					echo "Error: Failed to move the uploaded file.";
					echo "</strong></p></div>";
				}
			}
		}
		
	}
	else if($_POST['delete']) {
		// Delete the checked files.
		$files = $_POST['files'];
		if(!is_null($files)) {
			foreach($files as $file) {
				
				// fetch this product
				$product = $ppd->get_product($file);
				
				// delete the file on disk
				$file_path = $path . $product['file'];
				if(file_exists($file_path)) {
					unlink($file_path);
				}
				
				// delete the db entry
				$ppd->delete_product($file);
	
				echo "<div class='updated'><p><strong>";
				echo "Product deleted.";
				echo "</strong></p></div>";
			}
		}
	}
}

// path to this plugin's directory
$files_on_disk = get_files($path);

// get the products from the db
$products = $ppd->all_products();

if(count($files_on_disk) != count($products)) {
	echo "<div class='updated'>";
	echo "<h4>Database and Files Are Out Of Sync</h4><div style='overflow:auto;'>";
	echo "<div style='float:left;margin:0 10px;'><h5>On Disk</h5>";
	echo "<ul>";
	foreach($files_on_disk as $file) {
		echo "<li>$file</li>";
	}
	echo "</ul></div>";
	echo "<div style='float:left;margin:0 10px;'><h5>In The Database</h5>";
	echo "<ul>";
	foreach($products as $product) {
		$file_in_db = $product['file'];
		echo "<li>$file_in_db</li>";
	}
	echo "</ul></div></div>";
	echo "</div>";
}

function get_files($path) {
	$results = array();
	$ignore = array( 
		'cgi-bin', 
		'.', 
		'..', 
		'.htaccess', 
		'index.php', 
		'._',
		'.DS_Store'
		);
	$handler = opendir($path);
	while(false !== ($file = readdir($handler) ) ) {

		if( !in_array( $file, $ignore ) ) {

			$results[] = $file;
		}
	}
	closedir($handler);
	return $results;
}
?>
<style>
	form.products li {
		overflow: auto;
	}
	form.products li.header {
		font-weight: bold;
	}
	form.products li span {
		display: block;
		float: left;
	}
	form.products span.chk {
		width: 25px;
	}
	form.products span.name {
		width: 250px;
	}
	form.products span.cost {
		width: 75px;
	}
	form.products span.file {
		width: 250px;
	}
	form.products span.download {
		width: 75px;
	}
	form.products span.html {
		width: 300px;
	}
	
	form.add p {
		overflow: auto;
	}
	form.add label {
		display: block;
		float: left;
		width: 100px;
		text-align: right;
		vertical-align: middle;
		margin: 0 5px;
		padding: 6px 0;
	}
	form.add input[type=text],
	form.add input[type=file] {
		float: left;
		width: 200px;
	}
</style>

<div id="diglabs-admin-wrap" class="wrap">
	<h2>Pay Per Download - Admin</h2>

	<p>Below is a list of registered products that are protected by this plugin. To remove a product, check the associated check box and click the 'Delete Checked' button. <strong>The associated file will be deleted, it is your responsibility to ensure you have a back up.</strong></p>
	<h3>Products</h3>
	<form class='products' name="stripe_payment_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<ul>
			<li class='header'>
				<span class='chk'>&nbsp;</span>
				<span class='name'>Name</span>
				<span class='cost'>Cost</span>
				<span class='file'>File</span>
				<span class='html'>Form HTML Element (copy/paste)</span>
			</li>
		<?php foreach($products as $key => $product): ?>
			<li>
				<span class='chk'><input type="checkbox" name="files[]" value="<?php echo $product['id']; ?>" /></span>
				<span class='name'><?php echo $product['name']; ?></span>
				<span class='cost'><?php echo $product['cost']; ?></span>
				<span class='file'><?php echo $product['file']; ?></span>
				<span class='html'>[stripe_pay_per_download id='<?php echo $product['id']; ?>']</span>
			</li>
		<?php endforeach; ?>
		</ul>
		<input class="button-primary" name="delete" type="submit" value="Delete Checked" />
	</form>
	<p>To create a payment form for one of the above files, copy the <strong>HTML Element</strong> (contains the <code>stripe_pay_per_download</code> short code) for the file and place between the <code>stripe_form_begin</code> and <code>stripe_form_end</code> tags.</p>
	<br /><br />
	
	<h3>Add Product</h3>
	<p>Use this form to add a product to the list of protected files above.</p>
	<form class='add' name="stripe_payment_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
		<p>
			<label for='name'>Name</label>
			<input name='name' type='text' />
		</p>
		<p>
			<label for='cost'>Cost</label>
			<input name='cost' type='text' />
		</p>
		<p>
			<label for='file'>File</label>
			<input name='file' type='file' />
		</p>
		<p>
			<label>&nbsp;</label>
			<input class="button-primary" name='add' type='submit' value='Add Product' />
		</p>
	</form>
</div>