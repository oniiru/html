<?php

if( !class_exists( 'Dl_Plugin_Alt_Api' ) ) {
	
	class Dl_Plugin_Alt_Api {

		private $api_url;
		private $plugin_folder;
		private $plugin_file;

		public function __construct($api_url, $plugin_folder, $plugin_file) {
			$this->api_url = $api_url;
			$this->plugin_folder = $plugin_folder;
			$this->plugin_file = $plugin_file;
		}

		public function Check( $transient ) {

			// Check if the transient contains the 'checked' information.
			//	If no, just return its value without updating it.
			//
			if( empty( $transient->checked ) ) {
				return $transient;
			}
			
			// POST data to send to your API
			//
			$key = $this->plugin_folder . '/' . $this->plugin_file;
			$args = array(
					'action' => 'update-check',
					'plugin_name' => $this->plugin_folder,
					'version' => $transient->checked[ $key ]
				);

			// Send request checking for an update
			//
			$response = $this->Request( $args );

			// If response is false, don't alter the transient
			//
			$key = $this->plugin_folder . '/' . $this->plugin_file;
			if( false !== $response ) {
				$transient->response[ $key ] = $response;
			} else {
				unset( $transient->response[ $key ] );
			}

			return $transient;
		}	

		public function Info( $false, $action, $args ) {

			// Check if this plugin's API is about this plugin
			//
			if( $args->slug != $this->plugin_folder ) {
				return $false;
			}

			// POST data to send to your API
			//
			$key = $this->plugin_folder . '/' . $this->plugin_file;
			$args = array(
					'action' => 'plugin_information',
					'plugin_name' => $this->plugin_folder,
					'version' => $transient->checked[ $key ]
				);

			// Send request for detailed information
			//
			$response = $this->Request( $args );

			return $response;
		}


		private function Request( $args ) {

			// Send request
			//
			$request = wp_remote_post( $this->api_url, array( 'body' => $args ) );

			// Make sure the request was successful
			//
			if( is_wp_error( $request ) or wp_remote_retrieve_response_code( $request ) != 200 ) {
				// Request failed
				//
				return false;
			}

			// Read server response, which should be an object.
			//
			$response = unserialize( wp_remote_retrieve_body( $request ) );
			if( is_object( $response ) ) {

				return $response;

			} else {

				// Unexpected response.
				//
				return false;
				
			}

		}
	}
}

?>