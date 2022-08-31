// This code allows us to add and remove sites from the pr_product_sites table via Ajax
// It needs to be rewritten and simplified
// It uses jQuery to make the Ajax calls, but is otherwise pure Javascript. 

function SiteList (div_id, product_id, site_names, var_name, add_url, remove_url)
{
	this.add_url = add_url;
	if (this.add_url.length > 0 && this.add_url [this.add_url.length - 1] != '/')
		this.add_url += '/';
	this.remove_url = remove_url;
	if (this.remove_url.length > 0 && this.remove_url [this.remove_url.length - 1] != '/')
		this.remove_url += '/';

	this.div_id = div_id;
	this.product_id = product_id;
	this.var_name = var_name;

	this.site_names = new Array ();
	if (typeof site_names == 'object' && site_names.length > 0)
		this.site_names = site_names;

	this.show = false;

	this.getHTMLNames = function ()
	{
		var html = '';
		for (var index = 0; index < this.site_names.length; index++)
		{
			html += '<div>' + this.site_names [index] + ' (<span style="color:blue; cursor:pointer;" onclick="' + this.var_name + '.removeSite (' + "'" + this.site_names [index] + "'" + ');">remove</span>)</div>';
		}
		return html;
	}

	this.getHTMLControl = function ()
	{
		if (this.show)
			return '<div><input type="text" name="SiteName" id="SiteName" value="" size="45" onkeydown="if (window.event.keyCode == 13) {' + this.var_name + '.addSite (value); return false;}" /><input type="button" name="AddSite" id="AddSite" value="Add New Site" onclick="' + this.var_name + '.addSite (document.getElementById (' + "'SiteName'"  + ').value);" /></div><div><span style="cursor:pointer; color:blue;" onclick="' + this.var_name + '.toggle ();">finished adding sites</span></div>';
		else
			return '<div><span style="cursor:pointer; color:blue;" onclick="' + this.var_name + '.toggle ();">add more sites</span></div>';
	}

	this.getHTML = function ()
	{
		if (document.getElementById(this.div_id) == null)
			return 'Error: No such div';
		return this.getHTMLNames() + this.getHTMLControl();
	}

	this.addSite = function (name)
	{
		if (name.length > 0)
		{
			var name_input = document.getElementById('SiteName');
			name_input.value = '';

			name = name.toLowerCase (); // only taking lower case site IDs

			// no duplicate ingredient names
			for (var i = 0; i < this.site_names.length; i++)
			{
				if (this.site_names [i] == name)
					return;
			}

			var this_obj = this;
			$.ajax({
				type: 'post',
				url: this.add_url + this.product_id  + '/' + name,
				success: function(r) {
					this_obj.site_names [this_obj.site_names.length] = name;
					if (this_obj.show)
						document.getElementById(this_obj.div_id).innerHTML = this_obj.getHTML();
				}
			});
			name_input.focus();
		}
	}

	this.removeSite = function(name)
	{
		var this_obj = this;
		if (name.length > 0)
		{
			$.ajax({
				type: 'post',
				url: this.remove_url + this.product_id + '/' + name,
				success: function(r) {
					var new_arr = new Array ();
					for (var i = 0; i < this_obj.site_names.length; i++)
					{
						if (this_obj.site_names [i] != name)
							new_arr [new_arr.length] = this_obj.site_names [i];
					}
					this_obj.site_names = new_arr;
					document.getElementById(this_obj.div_id).innerHTML = this_obj.getHTML();
				}
			});
		}
	}

	this.toggle = function ()
	{
		this.show = !this.show;
		document.getElementById(this.div_id).innerHTML = this.getHTML();
		return this.show;
	}

	document.getElementById(this.div_id).innerHTML = this.getHTML();
	return this;
}

