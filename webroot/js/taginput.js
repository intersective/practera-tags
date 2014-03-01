	var tagcache = [];

	var handleTags = function (field) {
		var tag_input = $(field);
		if (tag_input.val()) {
			var tmptag = tag_input.val().split(',');
			for (var i in tmptag) {
				e = tmptag[i].trim().split(':');
				if (e[1]) { tag = e[1].trim(); id = e[0].trim(); } else { tag=e[0].trim(); id=''; }
				if (id) { tagcache[tag] = id; } else { tagcache[tag] = tag; }
				tmptag[i] = tag;
			}
			tag_input.val(tmptag.join(',').replace(/(^\s*,)|(,\s*$)/g, ''));
		}

		//we could just set the data-provide='tag' of the element inside HTML, but IE8 fails!
		if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) ) {
			tag_input.tag({
				placeholder:tag_input.attr('placeholder'),
				//enable typeahead by specifying the source array
				source: function(query, process) {
					var url = tag_input.attr('url') + '?term='+ query;
					// we're going to get back tags in the format of
					// identifier:keyname
					// identifiers are often formatted as model;field;value!...
					// this means that its possible to scope tags to very specific scenarios.
					// it's also possible to leave off the field or value because the db query is an ilike match
					// tagcache is used when saving to re-connect identifiers to tags
					// if a tag is in the tagcache, its identifier is used instead of creating a super localised one
					// tagcache is array(tag => fulltagwithID) -- in the case of conflicts the least specific tag is kept
					// if we have
					$.get(url, function(data) {
						var procme = [];
						for (var i in data) {
							e = data[i].trim().split(':');
							if (e[1]) { tag = e[1].trim(); id = e[0].trim(); } else { tag=e[0].trim(); id=''; }

							if (!id) {
								// found the most generic possible - so use it
								tagcache[tag] = tag;
							} else if (tagcache[tag] == tag) {
								// found the most generic possible - so use it
								tagcache[tag] = tag;
							} else {
								// use the namespace
								tagcache[tag] = tag_input.attr('namespace');
							}
							procme.push(tag);
						}
						process(procme);
					});
				}
					//defined in ace.js >> ace.enable_search_ahead
			  });
		} else {
			//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
			tag_input.after('<textarea id=\"'+tag_input.attr('id')+'\" name=\"'+tag_input.attr('name')+'\" rows=\"3\">'+tag_input.val()+'</textarea>').remove();
		}

		$('form').on('submit fileuploadsubmit', function(e) {
			//console.log('ehre');
			var tags = $(field).val();
			if (tags) {
				var tagarr = tags.split(',');
				for (var t in tagarr) {
					// split identifier:tag
					var it = tagarr[t].split(':');
					var tag;
					// if there was an identifier, the tag is 2nd
					if (it[1]) tag = it[1].trim(); else tag = it[0].trim();
					// did we find the tag in the cache?
					if (tagcache[tag] == tag) {
						// there is a tag, just no ID
						tagarr[t] = tag;
					} else if (tagcache[tag]) {
						// restore the identifier
						tagarr[t] = tagcache[tag] + ':' + tag;
					} else {
						// didn't find, so create localized tag
						tagarr[t] = tag_input.attr('namespace') + ':' + tag;
						tagcache[tag] = tag_input.attr('namespace');
					}
				}
				tag_input.val(tagarr.join(',').replace(/(^\s*,)|(,\s*$)/g, ''));
			}
			//console.log(tag_input.val());
			return true;
		});

	};

