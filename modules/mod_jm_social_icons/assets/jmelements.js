(function($){
	"use strict";

	$(document).ready(function() {
		$('#jform_params_view').on('change', function() {
			var icon_view = $('#jform_params_view').val();
			var button_save_visible = $('#jform_params_items_btn_save').is(':visible');
			if (button_save_visible) {
				$('#jform_params_icon').removeClass('invalid');
				$('#jform_params_icon').removeClass('required');
				$('#jform_params_icon-lbl').removeClass('invalid');
				$('#jform_params_icon-lbl').removeClass('required');
				$('#jform_params_icon-lbl span.form-control-feedback').remove();
				$('#jform_params_icon-lbl span.star').remove();
				$('#jform_params_image_file').removeClass('invalid');
				$('#jform_params_image_file').removeClass('required');
				$('#jform_params_image_file-lbl').removeClass('invalid');
				$('#jform_params_image_file-lbl').removeClass('required');
				$('#jform_params_image_file-lbl span.form-control-feedback').remove();
				$('#jform_params_image_file-lbl span.star').remove();
				$('#jform_params_name').removeClass('invalid');
				$('#jform_params_name').removeClass('required');
				$('#jform_params_name-lbl').removeClass('invalid');
				$('#jform_params_name-lbl').removeClass('required');
				$('#jform_params_name-lbl span.form-control-feedback').remove();
				$('#jform_params_name-lbl span.star').remove();
				$('#jform_params_url').removeClass('invalid');
				$('#jform_params_url').removeClass('required');
				$('#jform_params_url-lbl').removeClass('invalid');
				$('#jform_params_url-lbl').removeClass('required');
				$('#jform_params_url-lbl span.form-control-feedback').remove();
				$('#jform_params_url-lbl span.star').remove();
				if (icon_view == 1) {
					$('#jform_params_image_file').addClass('required');
					$('#jform_params_image_file-lbl').addClass('required');
					$('#jform_params_image_file-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
					$('#jform_params_icon').addClass('required');
					$('#jform_params_icon-lbl').addClass('required');
					$('#jform_params_icon-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				} else if (icon_view == 2) {
					$('#jform_params_icon').addClass('required');
					$('#jform_params_icon-lbl').addClass('required');
					$('#jform_params_icon-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
					$('#jform_params_image_file').addClass('required');
					$('#jform_params_image_file-lbl').addClass('required');
					$('#jform_params_image_file-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
					$('#jform_params_name').addClass('required');
					$('#jform_params_name-lbl').addClass('required');
					$('#jform_params_name-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				} else if (icon_view == 3) {
					$('#jform_params_name').addClass('required');
					$('#jform_params_name-lbl').addClass('required');
					$('#jform_params_name-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				}
				$('#jform_params_url').addClass('required');
				$('#jform_params_url-lbl').addClass('required');
				$('#jform_params_url-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
			}
		})
	});
	
	var JMElements = function(id, fields, lang, element_field, version){
		var self = this;

		if (typeof id == 'undefined' || !id || typeof fields == 'undefined' || !fields) {
			return false;
		}

		this.id = id;
		this.data_holder = $('#' + id);
		if (this.data_holder.length != 1) {
			return false;
		}

		this.lang = lang;
		this.element_field = element_field;

		this.fields = $(fields);
		if (this.fields.length > 0) {
			this.fields.each(function(){
				var tag_name = $(this).prop('tagName');
				var input_type = $(this).attr('type');
				var multiple = $(this).attr('multiple');

				var entry = {
					name: $(this).attr('name'),
					value: $(this).val(),
					tag_name: tag_name,
					input_type: input_type,
					multiple: multiple
				};

				if ((input_type && (input_type == 'checkbox' || input_type == 'radio')) || (tag_name == 'SELECT' && multiple)) {
					if ($(this).is(':selected') || $(this).is(':checked')) {
						self.fields_defaults.push(entry);
					}
				} else {
					self.fields_defaults.push(entry);
				}
			});
		}

		this.data = this.data_holder.val();
		if (this.data != '') {
			this.pushElements(this.data);
		}

		if (version == 2 || version == 3) {
			this.sortable_list = $('#' + self.id + '_items').sortable({
				axis:'y',
				cursor: 'move',
				items: 'tr',
				handle: '.jm-sort-handle',
				cancel: 'a,.btn,i',
				update: function( event, ui ) {
					self.stringify();
				}
			});
		}else{
			this.sortable_list = dragula([document.querySelector('#' + self.id + '_items')])
			.on('drop', el => {
				self.stringify();
			});
		}

		this.save_btn = $('#' + id + '_btn_save');
		this.add_btn = $('#' + id + '_btn_add');
		this.cancel_btn = $('#' + id + '_btn_cancel');

		var last_field = this.fields.last().parents('div.control-group');
		this.cancel_btn.insertAfter(last_field);
		this.save_btn.insertAfter(last_field);

		$("#toolbar-apply .btn").mousedown(function(e) {
			if( self.save_btn.hasClass('active') ) {
				self.save_btn.trigger('click');
			}
		});

		$("#toolbar-save .btn").mousedown(function(e) {
			if( self.save_btn.hasClass('active') ) {
				self.save_btn.trigger('click');
			}
		});

		$("#toolbar-save-new .btn").mousedown(function(e) {
			if( self.save_btn.hasClass('active') ) {
				self.save_btn.trigger('click');
			}
		});

		$("#toolbar-save-copy .btn").mousedown(function(e) {
			if( self.save_btn.hasClass('active') ) {
				self.save_btn.trigger('click');
			}
		});

		this.save_btn.click(function(e){
			e.preventDefault();
			if (self.save()) {
				self.toggleFormFields(0);
				$('#' + self.id + '_items').find('.jm-elements-edit-icon').removeClass('active');
				return false;
			}
		});

		this.cancel_btn.click(function(e){
			e.preventDefault();
			self.clearForm();
			self.toggleFormFields(0);
			$('#' + self.id + '_items').find('.jm-elements-edit-icon').removeClass('active');
			return false;
		});

		this.add_btn.click(function(e){
			e.preventDefault();
			self.clearForm();
			$('joomla-field-media').trigger('updatePreview');
			self.toggleFormFields(1);
			$('#' + self.id + '_items').find('.jm-elements-edit-icon').removeClass('active');
			return false;
		});

		this.toggleFormFields(0);

	};

	JMElements.prototype = {
		constructor: JMElements,
		id: null,
		data: '',
		elements: [],
		fields: null,
		fields_defaults: [],
		data_holder: null,
		current_index: -1,
		lang: {
			element_name: 'Item',
			elements_heading: 'Items',
			element_empty_required_message: 'Error'
		},
		element_field: '',
		sortable_list: '',
		save_btn: null,
		cancel_btn: null,
		add_btn: null,
		pushElements: function(data) {
			var elements = $.parseJSON(data);
			if (elements.length > 0) {
				this.elements = elements;

				for (var i in this.elements) {
					if (!this.elements.hasOwnProperty(i)) {
						continue;
					}
						var element_name = '';
						if (this.element_field) {
							for (var j in this.elements[i]) {
								if (!this.elements[i].hasOwnProperty(j)) {
									continue;
								}
								if (this.elements[i][j].name == this.element_field) {
									element_name = this.elements[i][j].value;
								}
							}
						}

						this.pushElement(i, true, element_name);
				}
			}
		},

		pushElement: function(index, is_new, element_name) {
			var element = this.elements[index];
			if (!element) {
				return false;
			}
			var self = this;
			var wrapper = $('#' + self.id + '_item-' + index);

			is_new = (is_new || !wrapper.length) ? true : false;

			if (is_new) {
				wrapper = $('<tr />', {
					id: self.id + '_item-' + index,
					'class': 'jm-element-item',
					'data-index': index
				});
			}

			if (!element_name) {
				element_name = this.lang.element_name + ' #' + (index+1);
			}

			if (is_new) {
				wrapper.html('<td><span class="icon-menu jm-sort-handle" style="cursor: move;"></span></td><td class="jm-elements-slide-title"><strong>' + element_name + '</strong></td><td><span class="btn btn-mini jm-elements-edit-icon"><i class="icon icon-edit" /></span></td><td><span class="btn btn-mini jm-elements-delete-icon"><i class="icon icon-remove" /></span></td>');

				wrapper.find('.jm-elements-delete-icon').click(function(){
					self.removeElement(index);
				});
				wrapper.find('.jm-elements-edit-icon').click(function(){
					$('#' + self.id + '_items').find('.jm-elements-edit-icon').removeClass('active');
					$(this).addClass('active');
					self.editElement(index);
				});

				$('#' + self.id + '_items').append(wrapper);
			} else {
				wrapper.find('strong').text(element_name);
			}
		},

		getElement: function(index, json) {
			if (typeof this.elements[index] != undefined) {
				this.current_index = index;
				if (json) {
					return JSON.stringify(this.elements[index]);
				}
				return this.elements[index];
			}
			return false;
		},

		editElement: function(index) {
			this.clearForm();
			var element = this.getElement(index, false);
			if (!element) {
				return false;
			}

			this.current_index = index;

			var self = this;

			this.fields.each(function(){
				var tag_name = $(this).prop('tagName');
				var input_type = $(this).attr('type');
				var multiple = $(this).attr('multiple');

				// first clear everything - just in case
				if (tag_name == 'SELECT') {
					$(this).find('option').removeAttr('selected');
				} else {
					if (input_type == 'checkbox' || input_type == 'radio') {
						$(this).removeAttr('checked');
						if( $(this).parent().hasClass('btn-group') ) {
							$(this).next().removeClass('active btn-success btn-danger');
						}
					} else {
						$(this).val('');
					}
				}

				if (element.length > 0) {
					for (var i in element) {
						if (!element.hasOwnProperty(i)) {
							continue;
						}
						if (element[i].name != $(this).attr('name')) {
							continue;
						}
						if (tag_name == 'SELECT') {
							$(this).find('option[value="'+element[i].value+'"]').attr('selected', 'selected');
						} else if (input_type == 'checkbox' || input_type == 'radio') {
							if ($(this).val() == element[i].value) {
								$(this).attr('checked', 'checked');
								if( $(this).parent().hasClass('btn-group') ) {
									if( $(this).val() == 1 ) {
										$(this).next().addClass('active btn-success');
									} else {
										$(this).next().addClass('active btn-danger');
									}
								}
							}
						} else {
							$(this).val(element[i].value);
							if( $(this).hasClass('minicolors') ) { //color field
								$(this).next().find('.minicolors-swatch-color').css('background-color', element[i].value);
							}
						}
					}
				}
				
				$('joomla-field-media').each(function() {
					$(this).trigger('updatePreview');
				})
				
				$(this).trigger('change');
				$(this).trigger('liszt:updated');
			});

			this.toggleFormFields(1);
		},

		removeElement: function(index) {
			if (typeof this.elements[index] != undefined) {
				delete this.elements[index];

				this.clearForm();
				$('#' + this.id + '_item-' + index).remove();

				this.stringify();
				return true;
			}

			this.clearForm();
			return false;
		},

		save: function() {
				var vals = this.fields.serializeArray();
				var is_new = this.current_index == -1;
				var index = is_new ? this.elements.length : this.current_index;
				var icon_view = $('#jform_params_view').val();
				var error = false;
				this.elements[index] = vals;

				var element_name = '';
				if (this.element_field) {
					element_name = $('[name^="' + this.element_field + '"]').first().val();
				}

				var image_file = $('#jform_params_image_file').val().trim();
				var icon = $('#jform_params_icon').val().trim();
				var url = $('#jform_params_url').val().trim();
				var name = $('#jform_params_name').val().trim();
				
				if (icon_view == 1) {
					if ((image_file.length == 0) && (icon.length == 0)) {
						$('#jform_params_image_file').addClass('invalid');
						$('#jform_params_image_file-lbl').addClass('invalid');
						$('joomla-field-media .field-media-preview').addClass('invalid');
						$('#jform_params_icon').addClass('invalid');
						$('#jform_params_icon-lbl').addClass('invalid');
						if ($('#jform_params_image_file-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_image_file-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						if ($('#jform_params_icon-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_icon-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					}else if ((image_file.length != 0 || icon.length != 0)) {
						$('#jform_params_image_file').removeClass('invalid');
						$('#jform_params_image_file-lbl').removeClass('invalid');
						$('.field-media-preview').removeClass('invalid');
						$('#jform_params_icon').removeClass('invalid');
						$('#jform_params_icon-lbl').removeClass('invalid');
						$('#jform_params_image_file-lbl span.form-control-feedback').remove();
						$('#jform_params_icon-lbl span.form-control-feedback').remove();
					}
					
					if (url.length == 0) {
						$('#jform_params_url').addClass('invalid');
						$('#jform_params_url-lbl').addClass('invalid');
						if ($('#jform_params_url-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_url-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					} else if (url.length != 0) {	
						$('#jform_params_url').removeClass('invalid');
						$('#jform_params_url-lbl').removeClass('invalid');
						$('#jform_params_url-lbl span.form-control-feedback').remove();
					}
					
					if ((image_file.length != 0 || icon.length != 0) && (url.length != 0)) {
						error = false;
					}
					
				} else if (icon_view == 2) {
					
					if ((image_file.length == 0) && (icon.length == 0)) {
						$('#jform_params_image_file').addClass('invalid');
						$('#jform_params_image_file-lbl').addClass('invalid');
						$('joomla-field-media .field-media-preview').addClass('invalid');
						$('#jform_params_icon').addClass('invalid');
						$('#jform_params_icon-lbl').addClass('invalid');
						if ($('#jform_params_image_file-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_image_file-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						if ($('#jform_params_icon-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_icon-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					}else if ((image_file.length != 0 || icon.length != 0)) {
						$('#jform_params_image_file').removeClass('invalid');
						$('#jform_params_image_file-lbl').removeClass('invalid');
						$('.field-media-preview').removeClass('invalid');
						//$('#jform_params_icon').removeClass('invalid');
						$('#jform_params_icon-lbl').removeClass('invalid');
						$('#jform_params_image_file-lbl span.form-control-feedback').remove();
						$('#jform_params_icon-lbl span.form-control-feedback').remove();
					}
					
					if (url.length == 0) {
						$('#jform_params_url').addClass('invalid');
						$('#jform_params_url-lbl').addClass('invalid');
						if ($('#jform_params_url-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_url-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					}else{
						$('#jform_params_url').removeClass('invalid');
						$('#jform_params_url-lbl').removeClass('invalid');
						$('#jform_params_url-lbl span.form-control-feedback').remove();
					}
					
					if (name.length == 0) {
						$('#jform_params_name').addClass('invalid');
						$('#jform_params_name-lbl').addClass('invalid');
						if ($('#jform_params_name-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_name-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					}else{
						$('#jform_params_name').removeClass('invalid');
						$('#jform_params_name-lbl').removeClass('invalid');
						$('#jform_params_name-lbl span.form-control-feedback').remove();
					}
					
					if ((image_file.length != 0 || icon.length != 0) && (url.length != 0) && (name.length != 0)) {
						error = false;
					}
					
				} else if (icon_view == 3) {
					if (name.length == 0) {
						$('#jform_params_name').addClass('invalid');
						$('#jform_params_name-lbl').addClass('invalid');
						if ($('#jform_params_name-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_name-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					}else{
						$('#jform_params_name').removeClass('invalid');
						$('#jform_params_name-lbl').removeClass('invalid');
						$('#jform_params_name-lbl span.form-control-feedback').remove();
					}
					
					if (url.length == 0) {
						$('#jform_params_url').addClass('invalid');
						$('#jform_params_url-lbl').addClass('invalid');
						if ($('#jform_params_url-lbl span.form-control-feedback').length == 0) {
							$('#jform_params_url-lbl').append('<span class="form-control-feedback">'+ this.lang.element_empty_required_message +'</span>');
						}
						error = true;
					}else{
						$('#jform_params_url').removeClass('invalid');
						$('#jform_params_url-lbl').removeClass('invalid');
						$('#jform_params_url-lbl span.form-control-feedback').remove();
					}
					
					if ((name.length != 0) && (url.length != 0)) {	
						error = false;
					}
				}

				if (!error) {
					this.clearForm();
					this.pushElement(index, is_new, element_name);
					this.stringify();
				}
				
				return !error;
		},

		stringify: function() {
			var json = null;
			var elements = this.elements;
			var rows = $('#' + this.id + '_items').find('tr');
			var data = [];

			rows.each(function(j){
				var row = $(this);
				var row_index = row.attr('data-index');
				for (var i in elements) {
					if (elements.hasOwnProperty(i) && elements[i] != null && i == row_index) {
						data[data.length] = elements[i];
					}
				}
			});

			this.data_holder.val(JSON.stringify(data));
		},
		
		cleanRequiredFormFields: function() {
			$('#jform_params_icon').removeClass('invalid');
			$('#jform_params_icon').removeClass('required');
			$('#jform_params_icon-lbl').removeClass('invalid');
			$('#jform_params_icon-lbl').removeClass('required');
			$('#jform_params_icon-lbl span.form-control-feedback').remove();
			$('#jform_params_icon-lbl span.star').remove();
			$('#jform_params_image_file').removeClass('invalid');
			$('#jform_params_image_file').removeClass('required');
			$('#jform_params_image_file-lbl').removeClass('invalid');
			$('#jform_params_image_file-lbl').removeClass('required');
			$('#jform_params_image_file-lbl span.form-control-feedback').remove();
			$('#jform_params_image_file-lbl span.star').remove();
			$('#jform_params_name').removeClass('invalid');
			$('#jform_params_name').removeClass('required');
			$('#jform_params_name-lbl').removeClass('invalid');
			$('#jform_params_name-lbl').removeClass('required');
			$('#jform_params_name-lbl span.form-control-feedback').remove();
			$('#jform_params_name-lbl span.star').remove();
			$('#jform_params_url').removeClass('invalid');
			$('#jform_params_url').removeClass('required');
			$('#jform_params_url-lbl').removeClass('invalid');
			$('#jform_params_url-lbl').removeClass('required');
			$('#jform_params_url-lbl span.form-control-feedback').remove();
			$('#jform_params_url-lbl span.star').remove();
		},

		clearForm: function() {
			this.current_index = -1;

			var self = this;

			this.fields.each(function(){
				var tag_name = $(this).prop('tagName');
				var input_type = $(this).attr('type');
				var multiple = $(this).attr('multiple');

				// first clear everything - just in case

				if (tag_name == 'SELECT') {
					$(this).find('option').removeAttr('selected');
				} else {
					if (input_type == 'checkbox' || input_type == 'radio') {
						$(this).removeAttr('checked');
						if( $(this).parent().hasClass('btn-group') ) {
							$(this).next().removeClass('active btn-success btn-danger');
						}
					} else {
						$(this).val('');
					}
				}

				if (self.fields_defaults.length > 0) {
					for (var i in self.fields_defaults) {
						if (!self.fields_defaults.hasOwnProperty(i)) {
							continue;
						}
						if (self.fields_defaults[i].name != $(this).attr('name')) {
							continue;
						}
						if (tag_name == 'SELECT') {
							$(this).find('option[value="'+self.fields_defaults[i].value+'"]').attr('selected', 'selected');
						} else if (input_type == 'checkbox' || input_type == 'radio') {
							if ($(this).val() == self.fields_defaults[i].value) {
								$(this).attr('checked', 'checked');
								if( $(this).parent().hasClass('btn-group') ) {
									if( $(this).val() == 1 ) {
										$(this).next().addClass('active btn-success');
									} else {
										$(this).next().addClass('active btn-danger');
									}
								}
							}
						} else {
							$(this).val(self.fields_defaults[i].value);
							if( $(this).hasClass('minicolors') ) { //color field
								$(this).next().find('.minicolors-swatch-color').css('background-color', self.fields_defaults[i].value);
							}
						}

					}
				}
				self.cleanRequiredFormFields();
				$(this).trigger('change');
				$(this).trigger('liszt:updated');
			});
		},

		clearFormSubmit: function(callback) {
			this.clearForm();

			if($.isFunction(callback)){
				callback();
			}
		},

		toggleFormFields: function(state) {
			var icon_view = $('#jform_params_view').val();
			if (state == 1) {
				if (icon_view == 1) {
					$('#jform_params_image_file').addClass('required');
					$('#jform_params_image_file-lbl').addClass('required');
					$('#jform_params_image_file-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
					$('#jform_params_icon').addClass('required');
					$('#jform_params_icon-lbl').addClass('required');
					$('#jform_params_icon-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				} else if (icon_view == 2) {
					$('#jform_params_icon').addClass('required');
					$('#jform_params_icon-lbl').addClass('required');
					$('#jform_params_icon-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
					$('#jform_params_image_file').addClass('required');
					$('#jform_params_image_file-lbl').addClass('required');
					$('#jform_params_image_file-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
					$('#jform_params_name').addClass('required');
					$('#jform_params_name-lbl').addClass('required');
					$('#jform_params_name-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				} else if (icon_view == 3) {
					$('#jform_params_name').addClass('required');
					$('#jform_params_name-lbl').addClass('required');
					$('#jform_params_name-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				}
				$('#jform_params_url').addClass('required');
				$('#jform_params_url-lbl').addClass('required');
				$('#jform_params_url-lbl').append('<span class="star" aria-hidden="true">&nbsp;*</span>');
				this.save_btn.addClass('active');
				this.fields.parents('div.control-group').css('display', '');
				this.save_btn.css('display', '');
				this.cancel_btn.css('display', '');
				this.add_btn.css('display', 'none');
			} else {
				this.cleanRequiredFormFields();
				this.save_btn.removeClass('active');
				this.fields.parents('div.control-group').css('display', 'none');
				this.save_btn.css('display', 'none');
				this.cancel_btn.css('display', 'none');
				this.add_btn.css('display', '');
			}
		}
	};

	window.JMElements = JMElements;

})(jQuery);
