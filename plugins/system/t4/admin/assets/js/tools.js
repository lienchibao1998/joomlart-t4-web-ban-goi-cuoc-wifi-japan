/*	export
 - site setting
 - navigation
 - theme setting
	  - default {brand color, typo, page. headding}
	  - css custom
	  - font custom
 - layout setting
	  - bock
	  - config
*/

jQuery(document).ready(function ($) {
	var editor, editorVariabes, editorVarCustom;
	// export
	$('.btn-action[data-action="tool.export"]').click(function () {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Export&id=' + tempId;
		var groups = '';
		// get groups to export
		if ($('#tool-export-groups').val()) {
			var groups = $('.tool-export-groups-wrap input:checked').map(function () { return this.value }).toArray();
			if (!groups.length) {
				alert(T4Admin.langs.toolExportNoSelectedGroupsWarning);
				return;
			}
			groups = groups.join(',');
		}
		// location.href = url;
		$.post(url, { task: 'export', groups: groups }).done(function (response) {
			if (response.data) {
				finishExport();
				window.location.href = response.data;
			} else if (response.error) {
				T4Admin.Messages(response.error, 'error');
			}
		});
	})
	var finishExport = function () {
		T4Admin.Messages(T4Admin.langs.ExportDataSuccessfuly, 'message');
		clearForm('export');
	}
	$('#tool-export-groups').on('change', function () {
		var val = $(this).val();
		if (val) {
			// selected, show groups with uncheck all
			$('.tool-export-groups-wrap').show().find('input').prop('checked', false);
		} else {
			$('.tool-export-groups-wrap').hide();
		}
	})
	var proccessImportAjax = function (data, url) {
		var i = 0;
		$.ajax({
			url: url,
			data: data,
			type: 'post',
			processData: false,
			cache: false,
			contentType: false,
			xhr: function () {
				var xhr = new window.XMLHttpRequest();

				// Upload progress
				xhr.upload.addEventListener("progress", function (evt) {
					if (evt.lengthComputable) {
						if (i == 0) {
							i = 1;
							var percentComplete = 0;
							var id = setInterval(frame, 10);

							function frame() {
								if (percentComplete >= 100) {
									clearInterval(id);
									i = 0;
								} else {
									percentComplete++;
								}
							}
						}
					}
				}, false);

				return xhr;
			}
		})
			.done(function (res) {
				proccessImportData(res);
			})
			.fail(function (error) {
				console.log(error);
			});
	}

	// handle file selected for import
	$('input[name="tool-import-file"]').on('change', function (e) {

		var files = e.originalEvent.target.files || e.originalEvent.dataTransfer.files;
		if (!files.length) {
			return;
		}
		var file = files[0];

		var data = new FormData;
		data.append('package', file);
		data.append('installtype', 'upload');
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=Import&task=Import&id=' + tempId;
		proccessImportAjax(data, url);
	})

	// modal import bundle 
	var $importModal = $('#t4-tool-import-modal');
	if (!$importModal.parent().is('.themeConfigModal')) $importModal.appendTo($('.themeConfigModal'));
	$('.btn-action[data-action="tool.importModal"]').click(function () {
		$importModal.show();
	});

	var proccessImportData = function (data) {
		if (!data.dir) {
			clearImport();
			alert(T4Admin.langs.toolImportDataFileError);
			return;
		}
		var setting = data.setting;
		// var setting = ['site','theme','layout'];
		var params = data.params;
		// find all group and check if group data available
		var count = 0;
		$('.tool-import-form [type="checkbox"]').each(function () {
			var $group = $(this),
				group = $group.val(),
				available = false;
			group_name = group.replace('typelist-', '');
			for (var a = 0; a < setting.length; a++) {
				var name = setting[a];
				if (name == group_name) {
					available = true;
					count++;
					break;
				}
			}

			if (available) {
				$group.prop('checked', true).prop('disabled', false).closest('li').removeClass('disabled');
			} else {
				$group.prop('checked', false).prop('disabled', true).closest('li').addClass('disabled');
			}
		})

		if (count) {
			$('.tool-import-form').show();

			// bind action event
			$('.t4-btn[data-action="tool.import"]').off('click').on('click', function () {
				doImport(data);
			})
		} else {
			alert(T4Admin.langs.toolImportDataFileEmptyWarning);
			$('.tool-import-form').hide();
			return;
		}
	}

	var doImport = function (data) {
		var groups = $('.tool-import-form [type="checkbox"]').filter(':enabled:checked').map(function () { return this.value });
		var groups_data = [];
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=import&task=importing&id=' + tempId;
		for (var a = 0; a < groups.length; a++) { groups_data.push(groups[a]) }
		console.log(groups_data);
		$.post(url, { data, groups: groups_data }).done(function (res) {
			console.log(res);
			if ((res.message && !res.success) || (res.error && res.error.length)) {
				if (res.error && res.error.length) res.message = res.error.toString();
				alert(res.message);
				return false;
			}
			var params = res.data.params;
			for (var i = 0; i < groups.length; i++) {
				var group = groups[i];
				for (var name in params) {
					if (name == group || name.startsWith(group + '_')) {
						updateValue(name, params[name]);
					}
				}
			}
			// clear form
			finishImport();
		});

	}

	var updateValue = function (name, val) {
		$('[name="jform[params][' + name + ']"]').val(val);
		if (name.startsWith('typelist-')) {
			$('[name="jform[params][' + name + ']"]').val(val).trigger('liszt:updated').trigger('change');
		}
	}

	var finishImport = function () {

		clearForm('import');
		alert(T4Admin.langs.toolImportDataDone);
		$('.t4-admin-save .btn-save').click();
	}

	var clearForm = function (task) {
		if (task == 'import') {
			$('input[name="tool-import-file"]').val('');
			$('.tool-import-form').hide();
		}
		if (task == 'export') {
			$('#tool-export-groups').val('').trigger('change');
		}
	}

	// Edit custom css
	var $cssmodal = $('.t4-css-editor-modal');
	if (!$cssmodal.parent().is('.themeConfigModal')) $cssmodal.appendTo($('.themeConfigModal'));
	$('.t4-btn[data-action="tool.css"]').click(async () => {
		const editor = await T4CodeEditor.get({
			name: 'cssEditor',
			container: '#t4_code_css',
			language: 'css',
		});

		$('body').addClass('t4-modal-open');
		$cssmodal.show();

		editor.updateOptions({ readOnly: true });
		editor.setValue('loading...');
		editor.layout();

		const url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=getcss&id=' + tempId;
		const res = await fetch(url);
		const css = await res.text();

		editor.setValue(css);
		editor.updateOptions({ readOnly: false });
	})

	$('body').on('click', '.t4-css-editor-apply', async () => {
		const editor = T4CodeEditor.instances.cssEditor;

		saveCss(editor.getValue());
	});
	var saveCss = function (css) {
		var url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=savecss&id=' + tempId;
		$.post(url, { css: css }).then(function () {
			// Save done, show message and reload preview
			$cssmodal.hide();
			$('body').removeClass('t4-modal-open');
			$(document).trigger('reload-preview');
			T4Admin.Messages(T4Admin.langs.customCssSaved, 'message');
		})
	}


	// SCSS TOOLS
	var $scssmodal = $('#t4-tool-scss-modal');
	$scssmodal.appendTo($('.themeConfigModal'));
	$('.t4-btn[data-action="tool.scss"]').click(async () => {
		const scssVariablesEditor = await T4CodeEditor.get({
			name: 'scssVariablesEditor',
			container: '#t4-scss-editor-variables',
			language: 'scss',
		});
		const scssEditor = await T4CodeEditor.get({
			name: 'scssEditor',
			container: '#t4-scss-editor-custom',
			language: 'css',
		});

		$scssmodal.show();

		scssVariablesEditor.updateOptions({ readOnly: true });
		scssVariablesEditor.setValue('loading...');
		scssVariablesEditor.layout();

		scssEditor.updateOptions({ readOnly: true });
		scssEditor.setValue('loading...');
		scssEditor.layout();

		const url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=load&id=' + tempId;
		const res = await fetch(url);
		const data = await res.json();
		const { variables, custom } = data;

		scssVariablesEditor.setValue(variables);
		scssVariablesEditor.updateOptions({ readOnly: false });

		scssEditor.setValue(custom);
		scssEditor.updateOptions({ readOnly: false });
	})

	$scssmodal.on('click', '.btn[data-action="apply"]', async event => {
		const $btn = $(event.currentTarget);
		const $btnText = $btn.find('.btn-text');

		$btn.attr('disabled', true);
		$btnText.text('Saving & Compiling ...');

		const { scssVariablesEditor, scssEditor } = T4CodeEditor.instances;
		const scssVar = scssVariablesEditor.getValue();
		const scssCustom = scssEditor.getValue();
		const url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=save&id=' + tempId;
		const submitData = new FormData();

		submitData.append('variables', scssVar);
		submitData.append('custom', scssCustom);

		try {
			const res = await fetch(url, {
				method: 'post',
				body: submitData,
			})

			const result = await res.json();

			if (result.success || result.ok) {
				T4Admin.Messages('Save & compile successfully!');
			} else {
				T4Admin.Messages(result.message, 'error');
			}
		} catch (error) {
			T4Admin.Messages('Compile error!', 'error');
		}

		$btn.attr('disabled', false);
		$btnText.text('Save & Compile');
	})
	$scssmodal.on('click', '.nav-tabs li', e => {
		if (jversion != 3) {
			e.preventDefault();

			//remove tab active
			$scssmodal.find('.nav-tabs li').removeClass('active');

			const $el = $(e.currentTarget);

			$el.addClass('active');
			var tabContent = $el.find('a').attr('href');

			if (tabContent) {
				$scssmodal.find('.tab-pane').removeClass('active');
				$scssmodal.find(tabContent).addClass('active');
			}
		}

		const { scssVariablesEditor, scssEditor } = T4CodeEditor.instances;

		scssVariablesEditor.layout();
		scssEditor.layout();
	});
	$scssmodal.on('click', '.btn[data-action="clean"]', async event => {
		const $btn = $(event.currentTarget);
		const $btnText = $btn.find('.btn-text');

		$btn.attr('disabled', true);
		$btnText.text('Removing...');

		const url = location.pathname + '?option=com_ajax&plugin=t4&format=json&t4do=scss&task=clean&id=' + tempId;

		try {
			const res = await fetch(url);
			const result = await res.json();

			if (result.error) {
				T4Admin.Messages(result.error, 'error');
			} else {
				T4Admin.Messages('Remove successfully!');
			}
		} catch (error) {
			T4Admin.Messages(error, 'error');
		}

		$btn.attr('disabled', false);
		$btnText.text('Remove Local CSS');
	})
})
