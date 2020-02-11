/* Téicée JS Script - Drupal 7.x - Redmine Data Importer Module - Start */

    var relations = [

	{
	    "common":"titre",
	    "div_id":"edit-title",
	    "var_path":"subject",
	    "input_type":"text"
	},
	{
	    "common":"description",
	    "div_id":"edit-field-description-und-0-value",
	    "var_path":"description",
	    "input_type":"textarea"
	},
	/*
	{
	    "common":"date de réalisation",
	    "div_id":"edit-field-date-de-creation-und-0-value-datepicker-popup-0",
	    "var_path":"start_date",
	    "input_type":"date"
	},
	*/
	{
	    "common":"Catégorie ISO",
	    "div_id":"edit-field-categorie-und",
	    "cf_name":"Catégorie ISO",
	    "input_type":"select"
	},
	{
	    "common":"thématique",
	    "div_id":"edit-field-thematique-und",
	    "cf_name":"Thématique",
	    "separator":";",
	    "input_type":"special-autocomplete-deluxe"
	},
	{
	    "common":"Mots Clés Inspire",
	    "div_id":"edit-field-mots-cles-thesaurus-und",
	    "cf_name":"Mots clés Inspire",
	    "separator":";",
	    "input_type":"special-autocomplete-deluxe"
	},
	{
	    "common":"Collection",
	    "div_id":"edit-field-collections-und",
	    "cf_name":"Collection",
	    "separator":";",
	    "input_type":"special-autocomplete-deluxe"
	},
	{
	    "common":"Mots Clés Complémentaires",
	    "div_id":"autocomplete-deluxe-input",
	    "cf_name":"Mots clés complémentaires",
	    "separator":";",
	    "input_type":"special-keyword-adder-autocomplete-deluxe"
	},
	/*
	{
	    "common":"Source et date des données",
	    "div_id":"edit-field-source-des-donnees-und-0-value",
	    "cf_name":"Répertoire de stockage",
	    "input_type":"textarea"
	},
	*/
	/*
	{
	    "common":"Source et date des données append",
	    "div_id":"edit-field-source-des-donnees-und-0-value",
	    "var_path":"created_on",
	    "input_type":"textarea",
	    "append":true
	},
	*/
	{
	    "common":"Auteur",
	    "div_id":"edit-field-auteur-und",
	    "var_path":"author/name",
	    "input_type":"select",
	    "filter":"author"
	},
	/*
	{
	    "common":"Emprise données fournies",
	    "div_id":"edit-field-emprise-geographique-und",
	    "cf_name":"Emprise données fournies",
	    "input_type":"select",
	    "filter":"emprise geographique"
	},
	*/
	{
	    "common":"Echelle de réalisation",
	    "div_id":"edit-field-echelle-und",
	    "cf_name":"Echelle",
	    "input_type":"select",
	    "filter":"echelle"
	}
	
    ];



function tic_redmine_data_importer_at_page_load()
{
    //    console.log("page loaded");
    setTimeout(tic_redmine_data_importer_clear_all_fields, 500);
}

function tic_redmine_data_importer_clear_all_fields()
{
    // clear all inputs at start and before prompting new map info

    for (var i = 0; relations[i]; i++)
    {
	var element = document.getElementById(relations[i].div_id);

	if (element !== null)
	{
	    element.value = "";
	}

	// out of relations
	var element = document.getElementById("edit-field-source-des-donnees-und-0-value");
	if (element !== null)
	    element.value = "";
	
	// clear special input
	autocomplete_deluxe_override.clear();
    }

    // clear the date picker :
    var date_picker_input = document.getElementById("edit-field-date-de-creation-und-0-value-datepicker-popup-0");
    if (date_picker_input !== null)
	date_picker_input.value = "";
}

tic_redmine_data_importer_at_page_load();

function tic_redmine_data_importer_init()
{
    //lookup for the card number input first :
    var card_number = jQuery("#edit-field-numero-de-carte-und-0-value").val().trim();
    var result = false;

    if (card_number.length == 0)
    {
	alert("Le numéro de carte à rechercher n'est pas indiqué");
	return ;
    }

    tic_redmine_data_importer_clear_all_fields();
    tic_redmine_data_importer_ajax_call_search("card_number", card_number);
}

function tic_redmine_data_importer_callback(result)
{
    console.log("result", result);

    //Redmine REST API success or not ?
    if (result.status !== 1)
    {
	alert("Le serveur Redmine ne répond pas");
	return ;
    }

    //Search is a success or not ?
    if (result.found !== 1)
    {
	alert("Aucune carte ne correspond au numéro indiqué.");
	return ;
    }

    if (result.integration_cartotheque !== 1)
    {
	alert("Cette carte n'a pas autorisation à être intégrée à la cartothèque.");
	return ;
    }

    //autofill result based on relational data
    tic_redmine_data_importer_auto_hydrate_fields(result.issue);
}

function tic_redmine_data_importer_ajax_call_search(name, value)
{
    tic_redmine_data_importer_clear_all_fields();
    jQuery.get('/tic_redmine_data_importer/get/number/' + value).done(function(result){tic_redmine_data_importer_callback(result);});
    return ;
}

function tic_redmine_data_importer_auto_hydrate_fields(cardInfoObj)
{
    //launching the selection result display, then place them on selection

    function apply_filter(filter, value)
    {
	switch (filter)
	{
	    case "author":
	    
		 var initials = "";
		 var name = value.split(" ");
		 for (var v = 0; name[v]; v++)
		 {
		     name[v] = name[v].trim();
		     initials += name[v][0].toUpperCase();
		 }
		 //then add the initials
		 var prefix = "Région HdF / DPSR / SIG - ";
		 value = prefix + initials;
	    break;
	    case "emprise geographique":

	         value = ("Régional" == value) ? "Région" : value;
	    
	    break;
	    case "echelle":

		 //récupération de l'échelle
		 //espace + nombres seulement + :

		 var position_to_slice = value.indexOf("(");
		 var value = value.slice(0, position_to_slice).trim();
	    
	    break;
	}
	return value;
    }

    for (var i = 0; relations[i]; i++)
    {
	var value = false;
	
	if (typeof relations[i].var_path !== "undefined")
        {	
	    var path = relations[i].var_path.split("/");

	    //init on first in path
	    value = cardInfoObj[path[0]];
	    
	    if (path.length > 1)
	    {	    
		for (var x = 1; path[x]; x++)
		{
		    value = value[path[x]];
		}
	    }
	}

	if (typeof relations[i].cf_name !== "undefined")
	{
	    for (var n = 0; cardInfoObj.custom_fields[n]; n++)
	    {
		if (cardInfoObj.custom_fields[n].name.trim() == relations[i].cf_name)
		{
		    value = cardInfoObj.custom_fields[n].value;
		    break ;
		}
	    }
	}

	//Apply filter for transformation
	if (typeof relations[i].filter !== "undefined")
	    value = apply_filter(relations[i].filter, value);
	
	if (["text", "textarea", "date"].indexOf(relations[i].input_type) !== -1)
	{
	    if (typeof relations[i].append !== "undefined" && relations[i].append == true)
		value = jQuery("#" + relations[i].div_id).val() + "\n" + value;
	    jQuery("#" + relations[i].div_id).val(value);
	}
	else if (["select"].indexOf(relations[i].input_type) !== -1)
	{
	    var select = document.getElementById(relations[i].div_id);
	    if (select !== null)
	    {
		for (var n = 0; select.options[n]; n++)
		{
		    if (select.options[n].text.trim().localeCompare(value) == 0)
			select.selectedIndex = n;
		}
	    }
	}
	else if (["special-autocomplete-deluxe"].indexOf(relations[i].input_type) !== -1)
	{
	    var values = value.split(relations[i].separator);    
	    var select = document.getElementById(relations[i].div_id);
	    var select_values_found = [];
	    for (var p = 0; values[p]; p++)
	    {
		if (values[p] == "-")
		{
		    select_values_found.push("_none");
		    continue ;
		}
		if (select)
		{
		    for (var x = 0; select.options[x]; x++)
		    {
			if (select.options[x].text.trim().localeCompare(values[p]) == 0)
			{
			    select_values_found.push(select.options[x].value);
			    break ;
			}
		    }
		}
		else
		    console.log("select is null", select, relations[i].div_id);
	    }
	    if (select_values_found.length > 0)
		jQuery('#' + relations[i].div_id).val(select_values_found).trigger("chosen:updated");
	}
	else if (["special-keyword-adder-autocomplete-deluxe"].indexOf(relations[i].input_type) !== -1)
	{
	    //si plusieurs autocomplete-deluxe dans le formulaire, ajouter --1 --2 à la fin de chaque div_id => créer une fonction de pré calcul
	    var values = value.split(relations[i].separator);
	    for (var x = 0; values[x]; x++)
	    {
		autocomplete_deluxe_override.addValue(relations[i].div_id, values[x]);
	    }
	}
    }
}

/* Téicée JS Script - Drupal 7.x - Redmine Data Importer Module - End */
