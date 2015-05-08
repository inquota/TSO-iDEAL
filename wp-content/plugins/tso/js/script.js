jQuery(document).ready(function(){
	
	jQuery('#child_add').click(function(event){
		
		event.preventDefault();
		
		jQuery('ul#children').append('<li>Kind <input type="text" placeholder="Achternaam" name="data[Child][child_last_name][]" required="required"><input type="text" placeholder="Voornaam" name="data[Child][child_first_name][]" required="required">Groep <select name="data[Child][group][]"> <option value="" selected="selected">--- Maak een keuze ----</option> <option value="1">1</option> <option value="1a">1a</option> <option value="1b">1b</option> <option value="2">2</option> <option value="2a">2a</option> <option value="2b">2b</option> <option value="3">3</option> <option value="3a">3a</option> <option value="3b">3b</option> <option value="4">4</option> <option value="4a">4a</option> <option value="4b">4b</option> <option value="5">5</option> <option value="5a">5a</option> <option value="5b">5b</option> <option value="6">6</option> <option value="6a">6a</option> <option value="6b">6b</option> <option value="7">7</option> <option value="7a">7a</option> <option value="7b">7b</option> <option value="8">8</option> <option value="8a">8a</option> <option value="8b">8b</option> </select> </li>');
	});
	
});