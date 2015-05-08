( 1 ) Copy template files to your theme.


( 2 ) Add this function to your theme functions.php
function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');


<option selected value="">Kies uw bank...</option>
<option value="0031">ABN Amro</option>
<option value="0721">ING</option>
<option value="0021">Rabobank</option>
<option value="0751">SNS Bank</option>
<option value="0761">ASN Bank</option>
<option value="0801">Knab</option>
<option value="0771">RegioBank</option>
<option value="0511">Triodos Bank</option>
<option value="0161">Van Lanschot Bankiers</option>