( 1 ) Copy template files to your theme.


( 2 ) Add this function to your theme functions.php
function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');