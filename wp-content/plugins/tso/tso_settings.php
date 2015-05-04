<?php

global $wpdb;
$table_settings = $wpdb->prefix . 'tso_settings';

// get settings
$settings = $wpdb->get_row( "SELECT * FROM {$table_settings} WHERE id=1", OBJECT );

 $args = array(
	'sort_order' => 'ASC',
	'sort_column' => 'post_title',
	'hierarchical' => 1,
	'exclude' => '',
	'include' => '',
	'meta_key' => '',
	'meta_value' => '',
	'authors' => '',
	'child_of' => 0,
	'parent' => -1,
	'exclude_tree' => '',
	'number' => '',
	'offset' => 0,
	'post_type' => 'page',
	'post_status' => 'publish'
); 
$pages = get_pages($args);

if(isset($_POST['submit'])) :

	// save
		$wpdb->update( 
		$table_settings, 
				array( 
					'targetpay_rtlo' => $_POST['targetpay_rtlo'],
					'targetpay_testmode' => $_POST['targetpay_testmode'],
					'form_id' => $_POST['form_id'],
					'field_id' => $_POST['field_id'],
					'url_login' => $_POST['url_login'],
					'url_register' => $_POST['url_register'],
					'url_card_overview' => $_POST['url_card_overview'],
					'url_card_add' => $_POST['url_card_add'],
					'url_payment_done' => $_POST['url_payment_done'],
					'url_profile_edit' => $_POST['url_profile_edit'],
					'url_profile_created' => $_POST['url_profile_created'],
				), 
				array( 'id' => 1 )
			);
	echo '<meta http-equiv="refresh" content="0; URL=/wp-admin/admin.php?page=settings">';
endif;
?>

<div class="wrap">
	<?php    echo "<h2>" . __( 'Settings', 'tso' ) . "</h2>"; ?>
<form method="POST">
	
		<p>
			TargetPay RTLO (Layout code): <input type="text" name="targetpay_rtlo" required="required" value="<?php echo $settings->targetpay_rtlo; ?>" />
		</p>
		
		<p>
			TargetPay Betaling in Test Mode: 
			
			<select name="targetpay_testmode">
				<option value="<?php echo $settings->targetpay_testmode; ?>">Huidige optie: <?php echo $settings->targetpay_testmode; ?></option>
				<option value="1">Test Mode AAN</option>
				<option value="0">Test Mode UIT</option>
			</select>
			
			<input type="text" name="targetpay_rtlo" required="required" value="<?php echo $settings->targetpay_rtlo; ?>" />
		</p>
		
		<p>
			Gravity Forms Form ID: <input type="text" name="form_id" required="required" value="<?php echo $settings->form_id; ?>" /> TSO scholen worden alleen geladen in dit Field Id (Dropdown menu).
		</p>
		
		<p>
			Gravity Forms Field ID: <input type="text" name="field_id" required="required" value="<?php echo $settings->field_id; ?>" /> TSO scholen worden alleen geladen in dit Form Id.
		</p>
		
	<?php    echo "<h2>" . __( 'URLS', 'tso' ) . "</h2>"; ?>
	
	
	
	<table>
				<?php
		$array_urls = array(
							'URL voor inloggen'=> 'url_login',
							'URL voor registreren'=> 'url_register',
							'URL voor strippenkaart overzicht'=> 'url_card_overview',
							'URL voor strippenkaart afnemen'=> 'url_card_add',
							'URL voor betaling afgerond'=> 'url_payment_done',
							'URL voor profiel bewerken'=> 'url_profile_edit',
							'URL voor profiel aangemaakt'=> 'url_profile_created',
						);
		
		foreach($array_urls as $key=>$url) : ?>
		<tr>
			<td><?php echo $key; ?>:</td>
			<td>
				<?php
					//$queried_post = get_page_by_path(str_replace('/', '', $settings->$url),OBJECT,'post');
					//print_r($queried_post);
					?>
							<select name="<?php echo $url; ?>">
				<?php if(isset($settings->$url)) : ?>
					
					<option value="<?php echo $settings->$url; ?>"><?php echo $settings->$url; ?></option>
				<?php endif; ?>
				<?php foreach($pages as $page) : ?>
					<option value="/<?php echo $page->post_name; ?>/"><?php echo $page->post_title; ?></option>
				<?php endforeach; ?>
				
				
			</select></td>
		</tr>
		<?php endforeach; ?>
	</table>


		<p>
			<input type="submit" name="submit" value="Save" class="button button-primary button-large" />
		</p>
</form>
		
</div>
