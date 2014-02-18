<?php
$this->Html->script("bootstrap-tag.min", array('block' => 'script'));

$this->Js->buffer("
		var tagcache = [];
		var tag_input = $('input.tags');
		if (tag_input.val()) {
			var tmptag = tag_input.val().split(',');
			for (var i in tmptag) {
				e = tmptag[i].trim().split(':');
				tagcache[e[1]] = e[0];
				tmptag[i] = e[1];
			}
			tag_input.val(tmptag.join(',').replace(/(^\s*,)|(,\s*$)/g, ''));
		}

		//we could just set the data-provide='tag' of the element inside HTML, but IE8 fails!
		if(! ( /msie\s*(8|7|6)/.test(navigator.userAgent.toLowerCase())) )
		{
			tag_input.tag(
			  {
				placeholder:tag_input.attr('placeholder'),
				//enable typeahead by specifying the source array
				source: function(query, process) {
					var url = tag_input.attr('url') + '&term='+ query;
					// we're going to get back tags in the format of
					// identifier:keyname
					// identifiers are often formatted as model;field;value!...
					// this means that its possible to scope tags to very specific scenarios.
					// it's also possible to leave off the field or value because the db query is an ilike match
					// tagcache is used when saving to re-connect identifiers to tags
					// if a tag is in the tagcache, its identifier is used instead of creating a super localised one
					$.get(url, function(data) {
						var procme = [];
						for (var i in data) {
							e = data[i].trim().split(':');
							tagcache[e[1]] = e[0];
							procme.push(e[1]);
						};
						process(procme);
					});
				}
					//defined in ace.js >> ace.enable_search_ahead
			  }
			);
		}
		else {
			//display a textarea for old IE, because it doesn't support this plugin or another one I tried!
			tag_input.after('<textarea id=\"'+tag_input.attr('id')+'\" name=\"'+tag_input.attr('name')+'\" rows=\"3\">'+tag_input.val()+'</textarea>').remove();
		}

		$('.tags').attr('style', 'width: 100%');

		$('form').on('submit', function(e) {
			var tags = $('input.tags').val();
			if (tags) {
				var tagarr = tags.split(',');
				for (var t in tagarr) {
					var it = tagarr[t].split(':');
					var tag;
					if (it[1]) tag = it[1]; else tag = it[0];
					if (tagcache[tag]) {
						tagarr[t] = tagcache[tag] + ':' + tag;
					} else {
						tagarr[t] = $('input.tags').attr('namespace') + ':' + tag;
						tagcache[tag] = $('input.tags').attr('namespace');
					}
				}
				$('input.tags').val(tagarr.join(',').replace(/(^\s*,)|(,\s*$)/g, ''));
			}
			//return false;
		});

");