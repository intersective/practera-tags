var tagcache = [];

var handleTags = function (field) {
	var tag_input = $(field);
	var identifier = tag_input.attr('identifier');
	var url = tag_input.attr('url');
	var tags = $.parseJSON(tag_input.attr('tags'));

	// handle pre-loaded data
	// the assumption here is that the data is either in identifer:tag, identifier:tag... format or
	// it's in tag, tag, tag format.
	//
	if (tag_input.val()) {
		var tmptag = tag_input.val().split(',');
		for (var i in tmptag) {
			e = tmptag[i].trim().split(':');
			if (e[1]) { tag = e[1].trim(); id = e[0].trim(); } else { tag=e[0].trim(); id=identifier; }
			if (!tagcache[tag]) tagcache[tag] = id;
			tmptag[i] = tag;
		}
		tag_input.val(tmptag.join(',').replace(/(^\s*,)|(,\s*$)/g, ''));
	}
	//we could just set the data-provide='tag' of the element inside HTML, but IE8 fails!
	if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) ) {
		var tagopts = {
			maximumSelectionSize: tag_input.attr('maxselect'),
			placeholder: tag_input.attr('placeholder'),
			tokenSeparators: [ ',', ':' ],
			tags: tags
		};

		if (url) {
	 		tagopts.minimumInputLength = 3;
	 		tagopts.ajax = {
				url: url,
				dataType: 'json',
	            quietMillis: 100,
	  			data: function (term, page) {
					return {
						identifier: identifier,
						term: term
					};
				},
				// we're going to get back tags in the format of
				// identifier:keyname
				// identifiers are often formatted as model;field;value!...
				// this means that its possible to scope tags to very specific scenarios.
				// it's also possible to leave off the field or value because the db query is an ilike match
				// tagcache is used when saving to re-connect identifiers to tags
				// if a tag is in the tagcache, its identifier is used instead of creating a super localised one
				// tagcache is array(tag => fulltagwithID) -- in the case of conflicts the least specific tag is kept
				// if we have
				results: function (data, page) {
					var procme = [];
					for (var i in data) {
						e = data[i].trim().split(':');
						// if we have just one result, it's tag only so append the provided identifier
						if (e[1]) { tag = e[1].trim(); id = e[0].trim(); } else { tag=e[0].trim(); id=identifier; }
						// if we don't have this tag in the cache already, put it in
						if (!tagcache[tag]) tagcache[tag] = id;
						procme.push({id: tag, text: tag});
					}
					console.log(procme);
					return { results: procme };
				}
			};
			tagopts.createSearchChoice = function (term) {
				return {id: term, text: term};
			};
			tagopts.initSelection = function (element, callback) {
		        var procme = element.val().split(",");
		        var data = [];
		        for (var i in procme) {
					e = procme[i].trim().split(':');
					// if we have just one result, it's tag only so append the provided identifier
					if (e[1]) { tag = e[1].trim(); id = e[0].trim(); } else { tag=e[0].trim(); id=identifier; }
					// if we don't have this tag in the cache already, put it in
					if (!tagcache[tag]) tagcache[tag] = id;
					data.push({id: tag, text: tag});
				}
		        callback(data);
			};

		}
		tag_input.select2(tagopts);
	} else {
		//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
		tag_input.after('<textarea id=\"'+tag_input.attr('id')+'\" name=\"'+tag_input.attr('name')+'\" rows=\"3\">'+tag_input.val()+'</textarea>').remove();
	}

	$('form').on('submit fileuploadsubmit', function(e) {
		var tags = $(field).val();
		if (tags) {
			var tagarr = tags.split(',');
			for (var t in tagarr) {
				var val = tagarr[t];
				if (val.indexOf(':') >= 0) continue;
				if (tagcache[val]) {
					tagarr[t] = tagcache[val] + ':' + val;
				} else {
					// didn't find, so create localized tag
					tagarr[t] = identifier + ':' + val;
					tagcache[val] = identifier;
				}
			}

			$(field).val(tagarr.join(',').replace(/(^\s*,)|(,\s*$)/g, ''));
		}
		return true;
	});
};

