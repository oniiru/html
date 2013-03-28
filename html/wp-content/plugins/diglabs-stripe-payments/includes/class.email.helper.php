<?php

class StripeEmailHelper{

	public function createBody($title, $msg, $data, $footer='') {
		$body = <<<MSG
<html>
	<body>
		<div style="padding:10px;background-color:#f2f2f2;">
			<div style="padding:10px;border:1px solid #eee;background-color:#fff;">
				<h2>$title</h2>
				<div style="margin:10px;">
					$msg
				</div>
				<table rules="all" style="border-color:#666;width:80%;margin:20px auto;" cellpadding="10">
					<!--table rows-->
				</table>
				$footer
			</div>
		</div>
	</body>
</html>
MSG;
	
		$rows = "";
		if(!is_null($data)){
			foreach($data as $key => $val) {
				$row = "<tr><td><strong>".$key."</strong></td><td>".$val."</td></tr>";
				$rows .= $row;
			}
		}
		$body = str_replace("<!--table rows-->", $rows, $body);
		
		return $body;
	}

	public function sendReceipt($to, $subject, $title, $msg, $data) {
		$body = '';
		if(function_exists('stripe_email_body')){
			$body = stripe_email_body($data);
		} else {
			$footer = '';
			if(function_exists('stripe_email_footer')) {
				$footer = stripe_email_footer($data);
			}
			$body = $this->createBody($title, $msg, $data, $footer);
		}
		
		$this->sendEmail($to, $subject, $body);
	}
	
	public function sendEmail($to, $subject, $body, $headers = "") {	
		if(function_exists('stripe_email_before_send')) {
			if(!stripe_email_before_send($to, $subject, $body, $headers)) {
				// email was cancelled
				return;
			}
		}
		add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));	
		wp_mail($to, $subject, $body, $headers);
	}
}

?>