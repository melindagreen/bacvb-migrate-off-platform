import '../styles/admin.scss';
import { PLUGIN_PREFIX } from './inc/constants';
import { renderTickers, renderFlyins } from './inc/render-banners';

(function ($) {
	/**
	 * Add a subfield of a repeater or condition field
	 * @returns {null}
	 */
	function addSubfield() {
		// Get subfield type
		let type = $(this).data('type');

		// Find container
		const container = type === 'condition'
			? $(this).closest(`.${type}-container`)
			: $(this).siblings(`.${type}-container`);

		// Craft new replica field
		const newFieldOriginal = type === 'condition'
			? $(this).closest('.condition-and-group').find(`.${type}.new`)
			: $(this).siblings(`.${type}.new`);

		const newField = newFieldOriginal
			.clone(true)
			.removeClass('new');

		// Determine new ID and name attr from old ID and name
		// const newIndex = type === 'condition-and-group' ? 0 : container.find(`.${type}:not(.new)`).length;
		const newIndex = container.find(`.${type}:not(.new)`).length;
		if (newField.attr('id')) {
			const newId = newField.attr('id').replace('new', newIndex);
			newField.attr('id', newId);
			newField.find('.repeater-count').text(newIndex + 1);
		}

		// Update name attr of child fields
		newField.find(':input:not(button)').each((index, input) => {
			if ($(input).attr('name')) {
				$(input).attr(
					'name',
					$(input).attr('name').replace('new', newIndex)
				);

				if ($(input).attr('name').includes('id')) $(input).val($(input).val().replace('new', newIndex));
			}
		});

		// Enable field
		newField.find('> .madden-banners-field-group > :input, > .repeater-group-content > .madden-banners-field-group > :input, > .condition-field > .madden-banners-field-group > :input, > .condition > .condition-field > .madden-banners-field-group > :input, .toggle-repeater-group, button').attr('disabled', false);

		// Append new field to container
		if (type === 'condition') {
			$(this).closest('.condition').after(`<p id='madden-banners-madden-banners-conditions-condition-${newIndex}-joiner'>OR</p>`, newField);

		} else {
			if (type === 'condition-and-group' && container.find('.condition-and-group:not(.new)').length)
				container.append(`<p id="condtion-and-group-${newIndex}-joiner">AND</p>`);
			container.append(newField);
		}

		if (type === 'condition-and-group') {
			toggleConditionalFields.call(newField.find('.condition'));
		}

		// Toggle conditions
		// toggleAllConditionalFields();
		// newField.find(':input').each((index, el) => {})
		newField.find(':input').change(toggleConditionalFields);
	}

	/**
	 * Remove the given subfield
	 * @returns {null}
	 */
	function removeSubfield() {
		$('#madden-banners-loader').addClass('show');

		const type = $(this).data('type');
		const toRemove = $(this).closest(`.${type}`);
		const removeContainer = $(this).data('type') === 'condition' && toRemove.siblings(`.${type}`).length <= 1;

		if (removeContainer) {
			const container = $(this).closest(`.${type}-container`);
			const joiner = $(`#${container.attr('id')}-joiner`);

			container.remove();
			if (joiner) joiner.remove();
		} else {
			const joiner = $(`#${toRemove.attr('id')}-joiner`);
			toRemove.remove();
			if (joiner) joiner.remove();
		}

		$(`#submit`).click();
	}

	/**
	 * Apply toggleConditionalFields to all condition groups
	 * @returns {null}
	 */
	function toggleAllConditionalFields() {
		$('.conditions-and .condition').each((index, el) => {
			toggleConditionalFields.call(el);
		});
	}

	/**
	 * Show/hide conditional fields when the form updates
	 * @returns {null}
	 */
	function toggleConditionalFields() {
		// Find conditional fields and hide all by default
		const allConditionalFields = $(this)
			.closest('.condition')
			.find(`.${PLUGIN_PREFIX}-field-group[data-showif]`);

		allConditionalFields
			.closest('.condition-field, .repeater-field')
			.hide();

		// Loop through conditional fields
		allConditionalFields.each(function (index, el) {
			// Get pieces of condition
			const conditionArr = $(el).data('showif').split('|');
			const fieldId = `#${PLUGIN_PREFIX}-${conditionArr[0]}`; // Field to match
			const wantsMatch = conditionArr[1] === '=='; // Do we want a match?
			const value = conditionArr[2]; // Expected value

			// Find the field
			let field;
			if ($(el).closest('.condition, .repeater-group').length)
				field = $(el)
					.closest('.condition, .repeater-group')
					.find(fieldId);
			else field = $(fieldId);
			const currentVal = field.val();

			// Should we show it?
			const showField = (currentVal === value) === wantsMatch;

			// Show/hide and disable if hidden
			$(el).find(':input').attr('disabled', !showField);
			if (showField)
				$(el).closest('.condition-field, .repeater-field').show();
		});

		// Make sure all 'new' fields are still disabled
		$('.condition.new :input, .repeater-group.new :input').attr(
			'disabled',
			true
		);
	}

	/**
	 * 
	 */
	function showPreview() {
		const bannerGroup = $(this).closest('.repeater-group');
		const type = bannerGroup.attr('data-type').replace('-', '');
		const fields = {};
		bannerGroup.find(':input').serializeArray().forEach(field => {
			if (!field.name.includes('[conditions]')) {
				const matches = field.name.match(`plugin:madden_banners\\[${type}s\\]\\[all_${type}s\\]\\[\\d+\\]\\[(.*)\\]`);
				if (matches && matches[1]) fields[matches[1]] = field.value;
			}
		});

		switch (type) {
			case 'ticker':
				$('.madden-banners-ticker').remove();
				$('body').removeClass('has-top-ticker');
				renderTickers(fields, true);
				break;
			case 'flyin':
				$('.madden-banners-flyin').remove();
				renderFlyins(fields, true);
				break;
		}
	}

	/**
	 * Reset the settings form
	 * @param {Event} e             The triggering event
	 */
	function resetForm(e) {
		e.preventDefault();
		if (
			window.confirm(
				'Are you sure? This will overwrite your current settings.'
			)
		) {
			madden_banners_options_defaults.forEach((optionGroup) => {
				optionGroup.fields.forEach((field) => {
					const emptyInput = $(
						`<input type='hidden' name='plugin:madden_banners[${optionGroup.id
						}][${field.id.replace(
							PLUGIN_PREFIX + '_',
							''
						)}]' value='false' />`
					);
					switch (field.args.type) {
						case 'repeater':
						case 'conditional':
							$(`#${PLUGIN_PREFIX}-${field.id}-field-group`)
								.empty()
								.append(emptyInput);
							break;
						default:
							const fieldName = `plugin:${PLUGIN_PREFIX}options[${optionGroup.id}][${field.id}]`;
							$(`[name="${fieldName}"]`).val(
								field.args.default
							);
							break;
					}
				});
			});

			$(`#submit`).click();
		}
	}

	$(document).ready(function () {
		// Toggle conditional fields.
		toggleAllConditionalFields();
		$(`#${PLUGIN_PREFIX}-options-form :input`).change(
			toggleConditionalFields
		);

		// Event listener time!
		$('.repeater-groups-add, .conditions-add-or, .conditions-add-and').click(addSubfield);
		$('.repeater-groups-remove, .conditions-remove').click(removeSubfield);
		$('.toggle-repeater-group').click(function () {
			$(this).closest('.top-buttons').siblings('.repeater-group-content').toggleClass('open');
		});
		$('.banner-preview').click(showPreview);

		// Reset defaults
		$(`#${PLUGIN_PREFIX}-reset`).click(resetForm);
	});
})(jQuery);
