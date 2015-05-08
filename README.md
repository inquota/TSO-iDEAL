# TSO-iDEAL
Dit is een koppeling voor TSO in combinatie met TargetPay iDEAL en Gravity Forms.

#### Installatie
1. Download alle code met de knop 'Download ZIP'.
2. Upload de 2 bestanden tso-ideal-check.php en tso-verify.php naar de root van je Wordpress folder (/).
3. Upload de map tso naar de plugins folder /wp-content/plugins.
4. Ga naar je plugins en activeer de plugin tso.
5. Ga naar TSO Settings en stel je TargetPay RTLO in.
6. Geef de Form ID op, waarin de TSO scholen dynamisch worden ingeladen.
7. Geef de Field ID op, waarin de TSO scholen dynamisch worden ingeladen.
8. Geef alle URL's op. Dit zijn de permalinks van de pagina's die je nog moet aanmaken.
9. Maak een map in in jouw theme, genaamd tso (wp-content/themes/[themenaam]/tso/).
10. Maak 6 nieuwe templates aan. Voer de onderstaande PHP include op de plek waar de TSO code geladen moet worden. Voor zie elke template van een template name: /** Template Name: TSO - Account Add  * */


tso-template-account-add.php
<?php include 'tso/tso-template-account-add.php'; ?>

tso-template-account-edit.php
<?php include 'tso/tso-template-account-edit.php'; ?>

tso-template-card.php
<?php include 'tso/tso-template-card.php'; ?>

tso-template-card-add.php
<?php include 'tso/tso-template-card-add.php'; ?>

tso-template-login.php
<?php include 'tso/tso-template-login.php'; ?>

tso-template-payment-done.php
<?php include 'tso/tso-template-payment-done.php'; ?>

11. Voeg de onderstaande code toe aan je functies.php
function register_session(){
    if( !session_id() )
        session_start();
}
add_action('init','register_session');