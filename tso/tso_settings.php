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
					'tso_admin_mail' => $_POST['tso_admin_mail'],
					'url_login' => $_POST['url_login'],
					'url_register' => $_POST['url_register'],
					'url_card_overview' => $_POST['url_card_overview'],
					'url_card_add' => $_POST['url_card_add'],
					'url_payment_done' => $_POST['url_payment_done'],
					'url_profile_edit' => $_POST['url_profile_edit'],
					'url_profile_created' => $_POST['url_profile_created'],
					'url_password_change' => $_POST['url_password_change'],
					'url_profile_edit_done' => $_POST['url_profile_edit_done'],
					'url_password_forget' => $_POST['url_password_forget'],
				), 
				array( 'id' => 1 )
			);
	echo'<script>window.location="/wp-admin/admin.php?page=settings"; </script>';
endif;
?>

<div class="wrap">
	<?php    echo "<h2>" . __( 'Instellingen', 'tso' ) . "</h2>"; ?>
<form method="POST">
	
		<p>
			TargetPay RTLO (Layout code): <input type="text" name="targetpay_rtlo" required="required" value="<?php echo $settings->targetpay_rtlo; ?>" />
		</p>
		
		<p>
			TargetPay Betaling in Test Mode: 
			<?php
			if($settings->targetpay_testmode==1){
				$targetpay_testmode = 'Test Mode AAN';
			}else{
				$targetpay_testmode = 'Test Mode UIT';
			}
			?>
			<select name="targetpay_testmode">
				<option value="<?php echo $settings->targetpay_testmode; ?>">Huidige optie: <?php echo $targetpay_testmode; ?></option>
				<option value="1">Test Mode AAN</option>
				<option value="0">Test Mode UIT</option>
			</select>
	
		</p>
		
		<p>
			TSO Admin mail: <input type="email" name="tso_admin_mail" required="required" value="<?php echo $settings->tso_admin_mail; ?>" />
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
							'URL voor profiel wachtwoord aanpassen'=> 'url_password_change',
							'URL voor profiel bewerken aangepast'=> 'url_profile_edit_done',
							'URL voor wachtwoord vergeten'=> 'url_password_forget',
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
			<input type="submit" name="submit" value="Opslaan" class="button button-primary button-large" />
		</p>
</form>
		
</div>
