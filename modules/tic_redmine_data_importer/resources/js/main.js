/* Téicée JS Script - Drupal 7.x - Redmine Data Importer Module - Start */

var tic_redmine_data_importer_verbose = false;

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

    // clear special input
    autocomplete_deluxe_override.clear();

    tic_redmine_data_importer_ajax_call_search("card_number", card_number);
}

function tic_redmine_data_importer_callback(result)
{
    //Redmine REST API success or not ?
    if (result.status !== 1)
    {
		alert("Le serveur Redmine ne répond pas");
		return ;
    }

    console.log(result);

    if (result.already_recorded == 1)
    	alert("Attention ! Ce numéro de carte est déjà enregistré dans la base de donneés Cartothèque.");

    //Search is a success or not ?
    if (result.found !== 1)
    {
		alert("Aucune carte ne correspond au numéro indiqué.");
		return ;
    }

    /* DEBUG TIC taxonomy relational dictionary */
    if (tic_redmine_data_importer_verbose)
	    console.log("tic_taxonomy_relational_dictionary_node", result.tic_taxonomy_relational_dictionary_node);
    
    //autofill result based on relational data
    tic_redmine_data_importer_auto_hydrate_fields(result.issue);
}

/* special s */

function tic_redmine_data_importer_thematical_select_parent_onchange()
{
	//place the parent selector
	if (tic_redmine_data_importer_verbose)
		console.log("test", jQuery('#select-thematiques-hdf-parent'));


	//jQuery('#select-thematiques-hdf-parent').val()

	var select = document.getElementById("select-thematiques-hdf-parent");
	if (select !== null)
	{
		var tid_parent = parseInt(select.options[select.selectedIndex].value);


		console.log("tid_parent = " + tid_parent);

		if (tid_parent == 0 || select.options[select.selectedIndex].value.length == 0)
		{
			if (tic_redmine_data_importer_verbose)
				console.log("tid_parent is on 0");

			//set the children sublist to 0 option
			var select_children = document.getElementById("select-thematiques-hdf-children");
			if (select_children !== null)
			{
				//remove all options
				select_children.innerHTML = "<option>- Sélectionner -</option>";
			}
			//hide the children sublist selection
			tic_redmine_data_importer_thematical_select_children_hide();
			return ;
		}

		if (tic_redmine_data_importer_verbose)
			console.log("url : " + "/tic_taxonomy_relational_dictionary/communicate/select/" + tid_parent);

		jQuery.get('/tic_taxonomy_relational_dictionary/communicate/select/' + tid_parent).done(function(result){tic_redmine_data_importer_thematical_select_parent_onchange_ajax_callback(result);});
    	return ;
	}

}

function tic_redmine_data_importer_thematical_select_parent_onchange_ajax_callback(result)
{
	if (tic_redmine_data_importer_verbose)
		console.log("tic_redmine_data_importer_thematical_select_parent_onchange_ajax_callback :", result);

	if (!result.command_found)
	{
		alert("Impossible de communiquer avec le module Drupal 7.x Téicée TIC Taxonomy Relational Dictionary");
		return ;
	}
	else
	{
		if (typeof result.data !== "undefined" && typeof result.data.children !== "undefined")
		{
			//place the content into new options in the select children

			var select_children = document.getElementById("select-thematiques-hdf-children");
			if (select_children !== null)
			{

				var options = "";

				for (var key in result.data.children)
				{
					if (tic_redmine_data_importer_verbose)
						console.log(key, result.data.children[key]);
					options += "<option value=\"" + key + "\">" + result.data.children[key] + "</option>";
				}

				if (tic_redmine_data_importer_verbose)
					console.log("current options", select_children.options);

				select_children.innerHTML = "<option value=\"\" selected>- Sélectionner -</option>" + options;

				tic_redmine_data_importer_thematical_select_children_show();

				//AUTOSET ALL CORRESPONDANCE WITH THE NODE

				//Appel au module TIC TRD -> script JS

				var node = tic_taxonomy_relational_dictionary_search_in_node_groups("carto themes auto link", result.data.tid, result.data.vid);

				if (tic_redmine_data_importer_verbose)
					console.log("found node is ", node);

				//autofill with the good values

				/* s */

				//Parcourir le noeud et pour chaque connexion présente set les bonnes valeurs :

				var relations = [

					{vocabulary_machine_name:"themes_ISO", input_id:"select-thematiques-hdf-parent", input_type:"select", vid:15, reference:false},
					{vocabulary_machine_name:"themes_INSPIRE", input_id:"", input_type:"select", vid:16, reference:false},
					{vocabulary_machine_name:"thematiques_HdF", input_id:"", input_type:"select", vid:17, reference:true}

				];

				for (var p = 0; relations[p]; p++)
				{
					//pour chaque vocabulaire checker toutes les connexions
					if (relations[p].reference == true)
						continue ;

					for (var u = 0; node.connections[u]; u++)
					{
						if (parseInt(node.connections[u].vid) !== parseInt(relations[p].vid))
							continue ;

						if (relations[p].input_type == "select")
							tic_redmine_data_importer_auto_hydrate_fields_functions.select(relations[p], node.connections[u].term.name);
						else if (relations[p].input_type == "select_autocomplete")
							tic_redmine_data_importer_auto_hydrate_fields_functions.select_autocomplete(relations[p], node.connections[u].term.name);

					}
				}

				/* e */
			}
		}
	}
}

function tic_redmine_data_importer_thematical_select_children_hide()
{
	var item = document.getElementsByClassName("form-item form-type-select form-item-thematiques-hdf-children");
	if (item !== null)
	{
		item[0].style.display = "none";
	}
}

function tic_redmine_data_importer_thematical_select_children_show()
{
	var item = document.getElementsByClassName("form-item form-type-select form-item-thematiques-hdf-children");
	if (item !== null)
	{
		item[0].style.display = "block";
	}
}

function tic_taxonomy_relational_dictionary_search_in_node_groups(nodeGroupName, tid = 0, vid = 0)
{
	if (nodeGroupName.length == 0)
		return false;

	if (tic_redmine_data_importer_verbose)
		console.log("tic_trd_node_group", tic_trd_node_group);

	if (tic_redmine_data_importer_verbose)
		console.log("tic_taxonomy_relational_dictionary_search_in_node_groups ===-----> (tid : " + tid + " & vid : " + vid + ")");
	//recherche un noeud selon une valeur de thématique.
	//search recursively in all taxonomy nodes

	//load the node group by name first
	if (tic_redmine_data_importer_verbose)
		console.log("searching node group : " + nodeGroupName);

	//then search the term
	var nodeGroupLinks = false;

	for (var i = 0; tic_trd_node_group[i]; i++)
	{
		if (typeof tic_trd_node_group[i].nodegroup !== "undefined"
			&& typeof tic_trd_node_group[i].nodegroup.name !== "undefined"
			&& tic_trd_node_group[i].nodegroup.name == nodeGroupName)
		{
			nodeGroupLinks = tic_trd_node_group[i].nodegroup.links;
			break ;
		}
	}

	if (!nodeGroupLinks)
	{
		console.warn("TIC:TRD -> specified node group has not been found");
		return false;
	}

	if (tic_redmine_data_importer_verbose)
		console.log("found this node group links", nodeGroupLinks);

	//parcourir les connexions des noeuds à la recherche du terme indiqué par son tid

	var currentNode = false;				

	//1 - parcourir les noeuds
	for (var x = 0; nodeGroupLinks[x]; x++)
	{
		//2 - parcourir les connexions des noeuds et faire la comparaison puis lister les nodes utilisées
		for (var g = 0; nodeGroupLinks[x].link.node.connections[g]; g++)
		{
			if (nodeGroupLinks[x].link.node.connections[g].tid == tid
				&& nodeGroupLinks[x].link.node.connections[g].vid == vid)
			{
				currentNode = nodeGroupLinks[x].link.node;

				break ;
			}
		}
		if (!!currentNode)
			break ;
	}

	if (!currentNode)
	{
		console.warn("TIC:TRD -> node unfound");
		return false;
	}

	return currentNode;
}

/* special e */

function tic_redmine_data_importer_ajax_call_search(name, value)
{
    jQuery.get('/tic_redmine_data_importer/get/number/' + value).done(function(result){tic_redmine_data_importer_callback(result);});
    return ;
}

var tic_redmine_data_importer_auto_hydrate_fields_functions = {};

tic_redmine_data_importer_auto_hydrate_fields_functions.select = function(relation, value)
{
	var select = document.getElementById(relation.div_id);
    if (select !== null)
    {
		for (var n = 0; select.options[n]; n++)
		{
		    if (select.options[n].text.trim().localeCompare(value) == 0)
			select.selectedIndex = n;
		}
    }
}

tic_redmine_data_importer_auto_hydrate_fields_functions.select_autocomplete = function(relation, value)
{
	var values = value.split(relation.separator);    
    var select = document.getElementById(relation.div_id);
    var select_values_found = [];
    for (var p = 0; values[p]; p++)
    {
		if (values[p] == "-")
		{
		    select_values_found.push("_none");
		    continue ;
		}
		for (var x = 0; select.options[x]; x++)
		{
		    if (select.options[x].text.trim().localeCompare(values[p]) == 0)
		    {
			select_values_found.push(select.options[x].value);
			break ;
		    }
		}
    }
    if (select_values_found.length > 0)
	jQuery('#' + relation.div_id).val(select_values_found).trigger("chosen:updated");
}

function tic_redmine_data_importer_auto_hydrate_fields(cardInfoObj)
{
    //launching the selection result display, then place them on selection

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

			 var regexp = /1:(\d+(?:\s+\d+)*)/g;
			 var res = value.match(regexp);

			 if (res.length == 1)
			 	var value = res[0].trim();
			 else
			 	alert("Echelle fournie impossible à déterminer");
		    
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
			tic_redmine_data_importer_auto_hydrate_fields_functions.select(relations[i], value);
		}
		else if (["special-autocomplete-deluxe"].indexOf(relations[i].input_type) !== -1)
		{
			tic_redmine_data_importer_auto_hydrate_fields_functions.select_autocomplete(relations[i], value);
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
