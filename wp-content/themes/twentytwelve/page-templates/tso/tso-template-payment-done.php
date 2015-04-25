<?php

if(isset($_SESSION['message'])){
	echo '<br />'.$_SESSION['message'];
}