<?php
/*
Plugin Name: درگاه پرداخت پارس پال برای Restrict Content Pro
Version: 1.0.0
Requires at least: 3.5
Description: درگاه پرداخت <a href="http://www.parspal.com/" target="_blank"> پارس پال </a> برای افزونه Restrict Content Pro
Plugin URI: http://webforest.ir/
Author: حنّان ابراهیمی ستوده
Author URI: http://hannanstd.ir/
License: GPL 2
*/
if (!defined('ABSPATH')) exit;
require_once('HANNANStd_Session.php');
if (!class_exists('RCP_ParsPal') ) {
	class RCP_ParsPal {
	
		public function __construct() {
			add_action('init', array($this, 'ParsPal_Verify_By_HANNANStd'));
			add_action('rcp_payments_settings', array($this, 'ParsPal_Setting_By_HANNANStd'));
			add_action('rcp_gateway_ParsPal', array($this, 'ParsPal_Request_By_HANNANStd'));
			add_filter('rcp_payment_gateways', array($this, 'ParsPal_Register_By_HANNANStd'));
			if (!function_exists('RCP_IRAN_Currencies_By_HANNANStd'))
				add_filter('rcp_currencies', array($this, 'RCP_IRAN_Currencies_By_HANNANStd'));
		}

		public function RCP_IRAN_Currencies_By_HANNANStd( $currencies ) {
			unset($currencies['RIAL']);
			$currencies['تومان'] = __('تومان', 'rcp_parspal');
			$currencies['ریال'] = __('ریال', 'rcp_parspal');
			return $currencies;
		}
				
		public function ParsPal_Register_By_HANNANStd($gateways) {
			global $rcp_options;
			$gateways['ParsPal'] = $rcp_options['parspal_name'] ? $rcp_options['parspal_name'] : __( 'پارس پال', 'rcp_parspal');
			return $gateways;
		}

		public function ParsPal_Setting_By_HANNANStd($rcp_options) {
		?>	
			<hr/>
			<table class="form-table">
				<?php do_action( 'RCP_ParsPal_before_settings', $rcp_options ); ?>
				<tr valign="top">
					<th colspan=2><h3><?php _e( 'تنظیمات پارس پال', 'rcp_parspal' ); ?></h3></th>
				</tr>				
				<tr valign="top">
					<th>
						<label for="rcp_settings[parspal_sandbox_mode]"><?php _e( 'فعالسازی حالت آزمایشی', 'rcp_parspal' ); ?></label>
					</th>
					<td>
						<input type="checkbox" value="1" name="rcp_settings[parspal_sandbox_mode]" id="rcp_settings[parspal_sandbox_mode]" <?php if( isset( $rcp_options['parspal_sandbox_mode'] ) ) checked('1', $rcp_options['parspal_sandbox_mode']); ?>/>
						<span class="description"><?php _e( 'در صورتی که مایل به استفاده از حالت آزمایشی درگاه پارس پال هستید این گزینه را تیک بزنید . نیازی به وارد کردن مرچنت و پسورد نیست .', 'rcp_parspal' ); ?></span>
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label for="rcp_settings[parspal_merchant]"><?php _e( 'مرچنت پارس پال', 'rcp_parspal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[parspal_merchant]" style="width: 300px;" name="rcp_settings[parspal_merchant]" value="<?php if( isset( $rcp_options['parspal_merchant'] ) ) { echo $rcp_options['parspal_merchant']; } ?>"/>
					</td>
				</tr>	
				<tr valign="top">
					<th>
						<label for="rcp_settings[parspal_password]"><?php _e( 'پسورد پارس پال', 'rcp_parspal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[parspal_password]" style="width: 300px;" name="rcp_settings[parspal_password]" value="<?php if( isset( $rcp_options['parspal_password'] ) ) { echo $rcp_options['parspal_password']; } ?>"/>
					</td>
				</tr>				
				<tr valign="top">
					<th>
						<label for="rcp_settings[parspal_query_name]"><?php _e( 'نام لاتین درگاه', 'rcp_parspal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[parspal_query_name]" style="width: 300px;" name="rcp_settings[parspal_query_name]" value="<?php echo $rcp_options['parspal_query_name'] ? $rcp_options['parspal_query_name'] : 'ParsPal'; ?>"/>
						<div class="description"><?php _e( 'این نام در هنگام بازگشت از بانک در آدرس بازگشت از بانک نمایان خواهد شد . از به کاربردن حروف زائد و فاصله جدا خودداری نمایید .', 'rcp_parspal' ); ?></div>
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label for="rcp_settings[parspal_name]"><?php _e( 'نام نمایشی درگاه', 'rcp_parspal' ); ?></label>
					</th>
					<td>
						<input class="regular-text" id="rcp_settings[parspal_name]" style="width: 300px;" name="rcp_settings[parspal_name]" value="<?php echo $rcp_options['parspal_name'] ? $rcp_options['parspal_name'] : __( 'پارس پال', 'rcp_parspal'); ?>"/>
					</td>
				</tr>
				<tr valign="top">
					<th>
						<label><?php _e( 'تذکر ', 'rcp_parspal' ); ?></label>
					</th>
					<td>
						<div class="description"><?php _e( 'از سربرگ مربوط به ثبت نام در تنظیمات افزونه حتما یک برگه برای بازگشت از بانک انتخاب نمایید . ترجیحا نامک برگه را لاتین قرار دهید .<br/> نیازی به قرار دادن شورت کد خاصی در برگه نیست و میتواند برگه ی خالی باشد .', 'rcp_parspal' ); ?></div>
					</td>
				</tr>
				<?php do_action( 'RCP_ParsPal_after_settings', $rcp_options ); ?>
			</table>
			<?php
		}
		
		public function ParsPal_Request_By_HANNANStd($subscription_data) {
			
			global $rcp_options;
			
			$query = $rcp_options['parspal_query_name'] ? $rcp_options['parspal_query_name'] : 'ParsPal';
			$amount = $subscription_data['price'];
			//fee is just for paypal recurring or ipn gateway ....
			//$amount = $subscription_data['price'] + $subscription_data['fee']; 
			if ($rcp_options['currency'] == 'ریال' || $rcp_options['currency'] == 'RIAL' || $rcp_options['currency'] == 'ریال ایران' || $rcp_options['currency'] == 'Iranian Rial (&#65020;)')
				$amount = $amount/10;
			$parspal_payment_data = array(
				'user_id'             => $subscription_data['user_id'],
				'subscription_name'     => $subscription_data['subscription_name'],
				'subscription_key'	 => $subscription_data['key'],
				'amount'           => $amount
			);			
			
			$HANNANStd_session = HANNAN_Session::get_instance();
			@session_start();
			$HANNANStd_session['parspal_payment_data'] = $parspal_payment_data;
			$_SESSION["parspal_payment_data"] = $parspal_payment_data;	
			
			//Action For ParsPal or RCP Developers...
			do_action( 'RCP_Before_Sending_to_ParsPal', $subscription_data );	
		
			
			//Start of ParsPal
			$Price = intval($amount);
            $ReturnPath = add_query_arg('gateway', $query, $subscription_data['return_url']);
			$ResNumber = $subscription_data['key'];
			$Paymenter = $subscription_data['user_name'];
			$Email = $subscription_data['user_email'];
			$Description = sprintf(__('خرید اشتراک %s برای کاربر %s', 'rcp_parspal'), $subscription_data['subscription_name'],$subscription_data['user_name']);
			$Mobile = '-';
			
			//Filter For ParsPal or RCP Developers...
			$Description = apply_filters( 'RCP_ParsPal_Description', $Description, $subscription_data );
			$Mobile = apply_filters( 'RCP_Mobile', $Mobile, $subscription_data );
			
			if( isset( $rcp_options['parspal_sandbox_mode'] ) ) 
			{
				$MerchantID = '100001';
				$Password = 'abcdeFGHI';
				$WebServiceUrl = 'http://sandbox.parspal.com/WebService.asmx?wsdl';
			}
			else 
			{
				$MerchantID = $rcp_options['parspal_merchant'];
				$Password = $rcp_options['parspal_password'];
				$WebServiceUrl = 'http://merchant.parspal.com/WebService.asmx?wsdl';
			}	
			$client = new SoapClient($WebServiceUrl);
			$res = $client->RequestPayment(array("MerchantID" => $MerchantID , "Password" =>$Password , "Price" =>$Price, "ReturnPath" =>$ReturnPath, "ResNumber" =>$ResNumber, "Description" =>$Description, "Paymenter" =>$Paymenter, "Email" =>$Email, "Mobile" =>$Mobile));
			$PayPath = $res->RequestPaymentResult->PaymentPath;
			$Status = $res->RequestPaymentResult->ResultStatus;
			if($Status == 'Succeed')
			{
				echo "<script type='text/javascript'>window.onload = function () { top.location.href = '" . $PayPath . "'; };</script>";
			}
			else
			{
				wp_die( sprintf(__('متاسفانه پرداخت به دلیل خطای زیر امکان پذیر نمی باشد . <br/><b> %s </b>', 'rcp_parspal'), $this->Fault($Status)) );
			}
			//End of ParsPal
				
			exit;
		}
		
		public function ParsPal_Verify_By_HANNANStd() {
			
			if ( !class_exists('RCP_Payments') )
				return;
			
			if (!isset($_GET['gateway']))
				return;
			
			global $rcp_options, $wpdb, $rcp_payments_db_name;
			@session_start();
			$HANNANStd_session = HANNAN_Session::get_instance();
			if (isset($HANNANStd_session['parspal_payment_data']))
				$parspal_payment_data = $HANNANStd_session['parspal_payment_data'];
			else 
				$parspal_payment_data = isset($_SESSION["parspal_payment_data"]) ? $_SESSION["parspal_payment_data"] : '';
			
			$query = $rcp_options['parspal_query_name'] ? $rcp_options['parspal_query_name'] : 'ParsPal';
						
			if 	( ($_GET['gateway'] == $query) && $parspal_payment_data )
			{
				
				$user_id 			= $parspal_payment_data['user_id'];
				$subscription_name 	= $parspal_payment_data['subscription_name'];
				$subscription_key 	= $parspal_payment_data['subscription_key'];
				$amount 			= $parspal_payment_data['amount'];
				
				/*
				$subscription_price = intval(number_format( (float) rcp_get_subscription_price( rcp_get_subscription_id( $user_id ) ), 2)) ;
				*/
				
				$subscription_id    = rcp_get_subscription_id( $user_id );
				$user_data          = get_userdata( $user_id );
				$payment_method =  $rcp_options['parspal_name'] ? $rcp_options['parspal_name'] : __( 'پارس پال', 'rcp_parspal');
				
				if( ! $user_data || ! $subscription_id || ! rcp_get_subscription_details( $subscription_id ) )
					return;
				
				$new_payment = 1;
				if( $wpdb->get_results( $wpdb->prepare("SELECT id FROM " . $rcp_payments_db_name . " WHERE `subscription_key`='%s' AND `payment_type`='%s';", $subscription_key, $payment_method ) ) )
					$new_payment = 0;

				unset($GLOBALS['new']);
				$GLOBALS['new'] = $new_payment;
				global $new;
				$new = $new_payment;
				
				if ($new_payment == 1) {
				
					//Start of ParsPal
					if( isset( $rcp_options['parspal_sandbox_mode'] ) ) 
					{
						$MerchantID = '100001';
						$Password = 'abcdeFGHI';
						$WebServiceUrl = 'http://sandbox.parspal.com/WebService.asmx?wsdl';
					}
					else 
					{
						$MerchantID = $rcp_options['parspal_merchant'];
						$Password = $rcp_options['parspal_password'];
						$WebServiceUrl = 'http://merchant.parspal.com/WebService.asmx?wsdl';
					}	
					$client = new SoapClient($WebServiceUrl);
					$Price = intval($amount);
					if(isset($_POST['status']) && $_POST['status'] == 100){
						$Status = $_POST['status'];
						$Refnumber = $_POST['refnumber'];
						$Resnumber = $_POST['resnumber'];
						$res = $client->VerifyPayment(array("MerchantID" => $MerchantID , "Password" =>$Password , "Price" =>$Price,"RefNum" =>$Refnumber ));
						$Status = $res->verifyPaymentResult->ResultStatus;
						$PayPrice = $res->verifyPaymentResult->PayementedPrice;
						if($Status == 'success') {
							$payment_status = 'completed';
							$fault = 0;
							$transaction_id = $Refnumber;
						}
						else {
						$payment_status = 'failed';
						$fault = $Status;
						$transaction_id = 0;
						}
					} 
					else {
						$payment_status = 'cancelled';
						$fault = 0;
						$transaction_id = 0;
					}
					//End of ParsPal
				
				
				
					unset($GLOBALS['payment_status']);
					unset($GLOBALS['transaction_id']);
					unset($GLOBALS['fault']);
					unset($GLOBALS['subscription_key']);
					$GLOBALS['payment_status'] = $payment_status;
					$GLOBALS['transaction_id'] = $transaction_id;
					$GLOBALS['subscription_key'] = $subscription_key;
					$GLOBALS['fault'] = $fault;
					global $parspal_transaction;
					$parspal_transaction = array();
					$parspal_transaction['payment_status'] = $payment_status;
					$parspal_transaction['transaction_id'] = $transaction_id;
					$parspal_transaction['subscription_key'] = $subscription_key;
					$parspal_transaction['fault'] = $fault;
				
		
					if ($payment_status == 'completed') 
					{
				
						$payment_data = array(
							'date'             => date('Y-m-d g:i:s'),
							'subscription'     => $subscription_name,
							'payment_type'     => $payment_method,
							'subscription_key' => $subscription_key,
							'amount'           => $amount,
							'user_id'          => $user_id,
							'transaction_id'   => $transaction_id
						);
					
						//Action For ParsPal or RCP Developers...
						do_action( 'RCP_ParsPal_Insert_Payment', $payment_data, $user_id );
					
						$rcp_payments = new RCP_Payments();
						$rcp_payments->insert( $payment_data );
					
					
						rcp_set_status( $user_id, 'active' );
						rcp_email_subscription_status( $user_id, 'active' );
				
						if( ! isset( $rcp_options['disable_new_user_notices'] ) ) {
							wp_new_user_notification( $user_id );
						}
					
					
						update_user_meta( $user_id, 'rcp_signup_method', 'live' );
						//rcp_recurring is just for paypal or ipn gateway
						update_user_meta( $user_id, 'rcp_recurring', 'no' ); 
					
						$subscription = rcp_get_subscription_details( rcp_get_subscription_id( $user_id ) );
						$member_new_expiration = date( 'Y-m-d H:i:s', strtotime( '+' . $subscription->duration . ' ' . $subscription->duration_unit . ' 23:59:59' ) );
						rcp_set_expiration_date( $user_id, $member_new_expiration );	
						delete_user_meta( $user_id, '_rcp_expired_email_sent' );
									
						$log_data = array(
							'post_title'    => __( 'تایید پرداخت', 'rcp_parspal' ),
							'post_content'  =>  __( 'پرداخت با موفقیت انجام شد . کد تراکنش : ', 'rcp_parspal' ).$transaction_id,
							'post_parent'   => 0,
							'log_type'      => 'gateway_error'
						);

						$log_meta = array(
							'user_subscription' => $subscription_name,
							'user_id'           => $user_id
						);
						
						$log_entry = WP_Logging::insert_log( $log_data, $log_meta );
				

						//Action For ParsPal or RCP Developers...
						do_action( 'RCP_ParsPal_Completed', $user_id );				
					}	
					
					
					if ($payment_status == 'cancelled')
					{
					
						$log_data = array(
							'post_title'    => __( 'انصراف از پرداخت', 'rcp_parspal' ),
							'post_content'  =>  __( 'تراکنش به دلیل انصراف کاربر از پرداخت ، ناتمام باقی ماند .', 'rcp_parspal' ),
							'post_parent'   => 0,
							'log_type'      => 'gateway_error'
						);

						$log_meta = array(
							'user_subscription' => $subscription_name,
							'user_id'           => $user_id
						);
						
						$log_entry = WP_Logging::insert_log( $log_data, $log_meta );
					
						//Action For ParsPal or RCP Developers...
						do_action( 'RCP_ParsPal_Cancelled', $user_id );	

					}	
					
					if ($payment_status == 'failed') 
					{
									
						$log_data = array(
							'post_title'    => __( 'خطا در پرداخت', 'rcp_parspal' ),
							'post_content'  =>  __( 'تراکنش به دلیل خطای زیر ناموفق باقی باند :', 'rcp_parspal' ).'<br/>'.$this->Fault($fault),
							'post_parent'   => 0,
							'log_type'      => 'gateway_error'
						);

						$log_meta = array(
							'user_subscription' => $subscription_name,
							'user_id'           => $user_id
						);
						
						$log_entry = WP_Logging::insert_log( $log_data, $log_meta );
					
						//Action For ParsPal or RCP Developers...
						do_action( 'RCP_ParsPal_Failed', $user_id );	
					
					}
			
				}
				add_filter( 'the_content', array($this,  'ParsPal_Content_After_Return_By_HANNANStd') );
				//session_destroy();	
			}
		}
		 
		
		public function ParsPal_Content_After_Return_By_HANNANStd( $content ) { 
			
			global $parspal_transaction, $new;
			
			$HANNANStd_session = HANNAN_Session::get_instance();
			@session_start();
			
			$new_payment = isset($GLOBALS['new']) ? $GLOBALS['new'] : $new;
			
			$payment_status = isset($GLOBALS['payment_status']) ? $GLOBALS['payment_status'] : $parspal_transaction['payment_status'];
			$transaction_id = isset($GLOBALS['transaction_id']) ? $GLOBALS['transaction_id'] : $parspal_transaction['transaction_id'];
			$fault = isset($GLOBALS['fault']) ? $this->Fault($GLOBALS['fault']) : $this->Fault($parspal_transaction['fault']);
			
			if ($new_payment == 1) 
			{
			
				$parspal_data = array(
					'payment_status'             => $payment_status,
					'transaction_id'     => $transaction_id,
					'fault'     => $fault
				);
				
				$HANNANStd_session['parspal_data'] = $parspal_data;
				$_SESSION["parspal_data"] = $parspal_data;	
			
			}
			else
			{
				if (isset($HANNANStd_session['parspal_data']))
					$parspal_payment_data = $HANNANStd_session['parspal_data'];
				else 
					$parspal_payment_data = isset($_SESSION["parspal_data"]) ? $_SESSION["parspal_data"] : '';
			
				$payment_status = isset($parspal_payment_data['payment_status']) ? $parspal_payment_data['payment_status'] : '';
				$transaction_id = isset($parspal_payment_data['transaction_id']) ? $parspal_payment_data['transaction_id'] : '';
				$fault = isset($parspal_payment_data['fault']) ? $this->Fault($parspal_payment_data['fault']) : '';
			}
			
			$message = '';
			
			if ($payment_status == 'completed') {
				$message = '<br/>'.__( 'پرداخت با موفقیت انجام شد . کد تراکنش : ', 'rcp_parspal' ).$transaction_id.'<br/>';
			}
			
			if ($payment_status == 'cancelled') {
				$message = '<br/>'.__( 'تراکنش به دلیل انصراف شما نا تمام باقی ماند .', 'rcp_parspal' );
			}
			
			if ($payment_status == 'failed') {
				$message = '<br/>'.__( 'تراکنش به دلیل خطای زیر ناموفق باقی باند :', 'rcp_parspal' ).'<br/>'.$fault.'<br/>';
			}
			
			return $content.$message;
		}
		
		private static function Fault($error) {
			$response	= '';
			switch($error){
			
                case 'Ready' :
					$response	=  __( 'هیچ عملیاتی انجام نشده است .', 'rcp_parspal' );
				break;

				case 'GatewayUnverify' :
					$response	=  __( 'درگاه شما غیر فعال می باشد .', 'rcp_parspal' );
				break;

				case 'GatewayIsExpired' :
					$response	=  __( 'درگاه شما فاقد اعتبار می باشد .', 'rcp_parspal' );
				break;
                                                
				case 'GatewayIsBlocked' :
					$response	=  __( 'درگاه شما مسدود شده است .', 'rcp_parspal' );
				break;
										
				case 'GatewayInvalidInfo' :
					$response	=  __( 'مرچنت یا رمز عبور شما اشتباه وارد شده است .', 'rcp_parspal' );
				break;
												
				case 'UserNotActive' :
					$response	=  __( 'کاربر غیرفعال شده است .', 'rcp_parspal' );
				break;
												
				case 'InvalidServerIP' :
					$response	=  __( 'IP سرور نامعتبر می باشد .', 'rcp_parspal' );
                break;
												
				case 'Failed' :
					$response	=  __( 'عملیات با مشکل مواجه شد .', 'rcp_parspal' );
				break;
												
				case 'NotMatchMoney' :
					$response	=  __( 'مبلغ واریزی با مبلغ درخواستی یکسان نمی باشد .', 'rcp_parspal' );
				break;
												
				case 'Verifyed' :
					$response	=  __( 'قبلا پرداخت شده است .', 'rcp_parspal' );
				break;
												
				case 'InvalidRef' :
					$response	=  __( 'شماره رسید قابل قبول نمی باشد .', 'rcp_parspal' );
				break;		
			
			}
			
			return $response;
		}
		
	}
}
new RCP_ParsPal();
?>