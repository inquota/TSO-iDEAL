<?php
global $wpdb;
$table_maillog = $wpdb->prefix . 'tso_maillog';
	
$pagenum = isset( $_GET['pagenum'] ) ? absint( $_GET['pagenum'] ) : 1;

$limit = 50; // number of rows in page
$offset = ( $pagenum - 1 ) * $limit;
$total = $wpdb->get_var( "SELECT COUNT(`id`) FROM {$table_maillog}" );
$num_of_pages = ceil( $total / $limit );

$oBookings = $wpdb->get_results( 
"
SELECT 
	*
FROM 
	{$table_maillog}
"
);
?>


<div class="wrap">
	<?php    echo "<h2>" . __( 'Mail Log', 'oscimp_trdom' ) . "</h2>"; ?>
<form method="POST">
	

	<table class="widefat fixed" cellspacing="0">
    <thead>
    <tr>
			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Ontvanger</th>
			<th class="manage-column column-columnname" scope="col">Onderwerp</th>
			<th class="manage-column column-columnname" scope="col">Bericht</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>
    </tr>
    </thead>

    <tfoot>
    <tr>

			<th class="manage-column column-columnname" scope="col">Id</th>
			<th class="manage-column column-columnname" scope="col">Ontvanger</th>
			<th class="manage-column column-columnname" scope="col">Onderwerp</th>
			<th class="manage-column column-columnname" scope="col">Bericht</th>
			<th class="manage-column column-columnname" scope="col">Aangemaakt op</th>

    </tr>
    </tfoot>

    <tbody>
    	<?php foreach($oBookings as $booking) : ?>
        <tr class="alternate">
            <td class="column-columnname"><?php echo $booking->id; ?></td>
			<td class="column-columnname"><?php echo $booking->receiver; ?></td>
			<td class="column-columnname"><?php echo $booking->subject; ?></td>
			<td class="column-columnname"><?php echo $booking->body; ?></td>
			<td class="column-columnname"><?php echo date('d-m-Y H:i:s', strtotime($booking->created_at . "+2 hours")); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php

$page_links = paginate_links( array(
    'base' => add_query_arg( 'pagenum', '%#%' ),
    'format' => '',
    'prev_text' => __( '&laquo;', 'text-domain' ),
    'next_text' => __( '&raquo;', 'text-domain' ),
    'total' => $num_of_pages,
    'current' => $pagenum
) );

if ( $page_links ) {
    echo '<div class="tablenav"><div class="tablenav-pages" style="margin: 1em 0">' . $page_links . '</div></div>';
}

?>
</form>
	
	
<div>
