function AlternateNameList (div_id, ingredient_id, alternate_name, var_name, add_url, remove_url)
{
	this.add_url = add_url;
	if (this.add_url.length > 0 && this.add_url [this.add_url.length - 1] != '/')
		this.add_url += '/';
	this.remove_url = remove_url;
	if (this.remove_url.length > 0 && this.remove_url [this.remove_url.length - 1] != '/')
		this.remove_url += '/';

	this.div_id = div_id;
	this.ingredient_id = ingredient_id;
	this.var_name = var_name;

	this.alternate_name = new Array ();
	if (typeof alternate_name == 'object' && alternate_name.length > 0)
		this.alternate_name = alternate_name;

	this.show = false;

	this.getHTMLNames = function ()
	{
		var html = '';
		for (var index = 0; index < this.alternate_name.length; index++)
		{
			html += '<div>' + this.alternate_name [index] + ' (<span style="color:blue; cursor:pointer;" onclick="' + this.var_name + '.removeAlternateName (' + "'" + this.alternate_name [index] + "'" + ');">remove</span>)</div>';
		}
		return html;
	}

	this.getHTMLControl = function ()
	{
		if (this.show)
			return '<div><input type="text" name="AltName" id="AltName" value="" size="45" onkeydown="if (window.event.keyCode == 13) {' + this.var_name + '.addAlternateName (value); return false;}" /><input type="button" name="AddAltName" id="AddAltName" value="Add New Name" onclick="' + this.var_name + '.addAlternateName (document.getElementById (' + "'AltName'"  + ').value);" /></div><div><span style="cursor:pointer; color:blue;" onclick="' + this.var_name + '.toggle ();">finished adding names</span></div>';
		else
			return '<div><span style="cursor:pointer; color:blue;" onclick="' + this.var_name + '.toggle ();">add more names</span></div>';
	}

	this.getHTML = function ()
	{
		if (document.getElementById (this.div_id) == null)
			return 'Error: No such div';
		return this.getHTMLNames () + this.getHTMLControl ();
	}

	this.addAlternateName = function (name)
	{
		if (name.length > 0)
		{
			var name_input = document.getElementById ('AltName');
			name_input.value = '';

			name = name.toLowerCase (); // only taking lower case ingredient names

			// no duplicate ingredient names
			for (var i = 0; i < this.alternate_name.length; i++)
			{
				if (this.alternate_name [i] == name)
					return;
			}

			var this_obj = this;
			$.ajax({
				type: 'post',
				url: this.add_url + this.ingredient_id  + '/' + name,
				success: function(r) {
					this_obj.alternate_name [this_obj.alternate_name.length] = name;
					if (this_obj.show)
						document.getElementById (this_obj.div_id).innerHTML = this_obj.getHTML ();
				}
			});
			name_input.focus ();
		}
	}

	this.removeAlternateName = function (name)
	{
		var this_obj = this;
		if (name.length > 0)
		{
			$.ajax({
				type: 'post',
				url: this.remove_url + this.ingredient_id + '/' + name,
				success: function(r) {
					var new_arr = new Array ();
					for (var i = 0; i < this_obj.alternate_name.length; i++)
					{
						if (this_obj.alternate_name [i] != name)
							new_arr [new_arr.length] = this_obj.alternate_name [i];
					}
					this_obj.alternate_name = new_arr;
						document.getElementById (this_obj.div_id).innerHTML = this_obj.getHTML ();
				}
			});
		}
	}

	this.toggle = function ()
	{
		this.show = !this.show;
		document.getElementById (this.div_id).innerHTML = this.getHTML ();
		return this.show;
	}

	document.getElementById (this.div_id).innerHTML = this.getHTML ();
	return this;
}

