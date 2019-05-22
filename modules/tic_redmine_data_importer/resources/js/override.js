var autocomplete_deluxe_override = {};

autocomplete_deluxe_override.addValue = function(div_id, itemName)
{
    var ui_item = {
	label:itemName,
	value:itemName
    };
    var jqObject = jQuery("#" + div_id);
    var parent = jqObject.parents('.autocomplete-deluxe-container');
    var value_container = parent.next();
    var value_input = value_container.find('input');
    var wrapper = "\"\"";
    var valueForm = document.createElement("input");
    valueForm.val = function(){return "";};
    var widget = {
	items:[ui_item.value],
	valueForm:valueForm
    };
    var item = new Drupal.autocomplete_deluxe.MultipleWidget.Item(widget, ui_item);
    item.element.insertBefore(jqObject);
    var new_value = ' ' + wrapper + ui_item.value + wrapper;
    var values = value_input.val();
    value_input.val(values + new_value);
    jqObject.val("");
}
