<script>
	(function(){
		$(document).on('click', '<?=$this->used_at?>', function() {

			if (parent.document.readyState === "complete" && window.parent.bootbox === 'undefined') {
				return true;
			}

			createCrudPopup({
				url: this.href
			});

			return false;
		});


		function createCrudPopup(data)
		{
			if(typeof window.parent.bootbox == 'undefined') {
				setTimeout(function() {
					createCrudPopup(data);
				}, 100);
				return;
			}

			var iframeId = 'popupIframe'+Math.floor((Math.random() * 10000) + 1);
			var iframe = $('<iframe style="width:100%" id="'+iframeId+'" src="'+data.url+'" frameborder="0" scrolling="no"></iframe>');
			iframe.load(function() {
				modal.find('.modal-title').text(this.contentDocument.title);
				$(this).contents().find('.ui.dividing.header').remove();
				$(this).contents().find('body').attr('style', 'background:white!important');

				$(this).contents().find('[crud-action=back]').remove();
				var saveAction = $(this).contents().find('[crud-action=save]');
				if(saveAction.length){
					saveAction.hide();
				} else {
					window.parent.$('.modal-footer').remove();
				}
				resizeIframe();
			});
			setInterval(resizeIframe, 100);

			var modal = window.parent.bootbox.dialog({
				message: iframe,
				title: 'Loading...',
				buttons: {
					cancel: {
						label: 'Cancel',
						className: 'btn-default'
					},
					save: {
						label: "Save and continue",
						className: "btn-primary",
						callback: function() {
							window.parent.$('[data-bb-handler=save]').text('Loading...');
							window.parent.$('#'+iframeId).contents().find('[crud-action=save]').click();
							window.parent.bootbox.hideAll();
							datatable.ajax.reload();
							return false;
						}
					}
				}
			});

			window.parent.popupIframe = iframe;
			window.parent.table = table;
			window.parent.datatable = datatable;

			function resizeIframe()
			{
				var heightIframe = iframe.contents().find('.view').height()+30;
				iframe.css("height", heightIframe);
			}

			setInterval(resizeIframe, 50);
		}
	})();
</script>

