<?php

global $wpdb;
$table_settings = $wpdb->prefix . 'tso_settings';

// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

if(isset($_POST['submit'])) :

	// save
		$wpdb->update( 
		$table_settings, 
				array( 
					'targetpay_rtlo' => $_POST['targetpay_rtlo'],
					'form_id' => $_POST['form_id'],
					'field_id' => $_POST['field_id'],
				), 
				array( 'id' => 1 )
			);
	echo '<meta http-equiv="refresh" content="0; URL=/wp-admin/admin.php?page=settings">';
endif;
?>

<style>
	p.urls input[type="text"] {
		width: 490px;
	}
</style>

<div class="wrap">
	<?php    echo "<h2>" . __( 'Settings', 'tso' ) . "</h2>"; ?>
<form method="POST">
	
		<p>
			TargetPay RTLO (Layout code): <input type="text" name="targetpay_rtlo" required="required" value="<?php echo $settings->targetpay_rtlo; ?>" />
		</p>
		
		<p>
			Gravity Forms Form ID: <input type="text" name="form_id" required="required" value="<?php echo $settings->form_id; ?>" /> TSO scholen worden alleen geladen in dit Field Id (Dropdown menu).
		</p>
		
		<p>
			Gravity Forms Field ID: <input type="text" name="field_id" required="required" value="<?php echo $settings->field_id; ?>" /> TSO scholen worden alleen geladen in dit Form Id.
		</p>
		
		<p>
			<input type="submit" name="submit" value="Save" class="button button-primary button-large" />
		</p>
	<?php    echo "<h2>" . __( 'URLS', 'tso' ) . "</h2>"; ?>
	
		<?php
		$array_urls = array(
							'URL voor inloggen'=> 'url_login',
							'URL voor registreren'=> 'url_register',
							'URL voor strippenkaart overzicht'=> 'url_card_overview',
							'URL voor strippenkaart afnemen'=> 'url_card_add',
							'URL voor betaling afgerond'=> 'url_payment_done',
						);
		
		foreach($array_urls as $key=>$url) : ?>
		<p class="urls">
			<?php echo $key; ?>: <input type="text" name="<?php echo $url; ?>" required="required" value="<?php echo $settings->$url; ?>" />
		</p>
		<?php endforeach; ?>
	
		
</form>
		
</div>
