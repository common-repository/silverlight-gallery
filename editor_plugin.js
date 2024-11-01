(function() {
	tinymce.create('tinymce.plugins.slGalleryPlugin', {

		init : function(ed, url) {
			ed.addCommand('mceSlGallery', function() {
				ed.windowManager.open({
					file : url + '/SlGalleryPP.htm',
					width : 280,
					height : 370,
					inline : 1
				}, {
					plugin_url : url, // Plugin absolute URL
					some_custom_arg : 'custom arg' // Custom argument
				});
			});


			ed.addButton('slgallery', {
				title : 'insert Silverlight Gallery',
				cmd : 'mceSlGallery',
				image : url + '/button/slGallery.png'
			});


			ed.onNodeChange.add(function(ed, cm, n) {
				cm.setActive('slgallery', n.nodeName == 'IMG');
			});
		},

		createControl : function(n, cm) {
			return null;
		},

		
		getInfo : function() {
			return {
				longname : 'Silverlight Gallery Plugin',
				author   :  'Regart.net',
				authorurl : 'http://regart.net',
				infourl : 'http://regart.net',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('slgallery', tinymce.plugins.slGalleryPlugin);
})();