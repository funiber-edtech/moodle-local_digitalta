import $ from 'jquery';
import {setupForElementId} from 'editor_tiny/editor';
import {sectionTextUpsert} from 'local_dta/repositories/reflection_repository';
import Notification from 'core/notification';

let tinyConfig;

/**
 * Create tinyMCE in an area.
 * @param {string} area - The id of the area to create tinyMCE in.
 * @return {void}
 */
function createTinyMCE(area) {
    setupForElementId({
        elementId: `${area}`,
        options: tinyConfig,
    });
}

/**
 * Set event listeners for the module.
 * @return {void}
 * */
function setDefaultTinyMCE() {
  $('.editor').each(function() {
    createTinyMCE(this.id);
  });
}

/**
 * Set the tinyMCE config.
 * @return {void}
 */
function setTinyConfig() {
  tinyConfig = window.dta_tiny_config;
}

/**
 * Save the text section.
 * @param {object} btn - The data to save.
 * @return {void}
 */
function saveTextSection(btn) {
  const data = btn.data();
  const {target, group} = data;
  const reflectionid = $('#reflectionid').val();
  const content = window.tinyMCE.get(target).getContent();
  sectionTextUpsert({reflectionid, group, content}).then(() => {

    // TODO: Hector, add the sectionid to the section so we can update it later.
    // TODO: happy holidays :D be safe and have fun with your family
    Notification.addNotification({
        message: 'Section saved successfully.',
        type: 'success'
    });
    return;
  }).fail(Notification.exception);
}


/**
 * Set event listeners for the module.
 * @return {void}
 * */
function setEventListeners() {
    // Save section
    $(document).on('click', '.submit', function() {
      saveTextSection($(this));
    });

    // Collapse Sections
    $(document).on('click', '.header', function() {
      const section = $(this).closest('.section');
      const content = section.find('.questions');
      const collapseIcon = $(this).find('i');
      if (section.hasClass('collapsed')) {
        section.removeClass('collapsed');
        content.css('display', 'flex');
        collapseIcon.removeClass('fa-chevron-right').addClass('fa-chevron-down');
      } else {
        section.addClass('collapsed');
        content.hide();
        collapseIcon.removeClass('fa-chevron-down').addClass('fa-chevron-right');
      }
    });

    // Add-Section-Menu Collapse
    $(document).on('click', '#add_button', function() {
      const importerParent = $(this).closest('#importer');
      const importerDiv = importerParent.find('#import_div');
      const addIcon = $(this).find('i');
      // importerDiv.css('display', importerDiv.css('display') == 'flex' ? 'none' : 'flex');
      if (importerParent.hasClass('collapsed')) {
        importerParent.removeClass('collapsed');
        importerDiv.css('display', 'flex');
        addIcon.removeClass('fa fa-plus-circle').addClass('fa fa-minus-circle');
      } else {
        importerParent.addClass('collapsed');
        importerDiv.hide();
        addIcon.removeClass('fa fa-minus-circle').addClass('fa fa-plus-circle');
      }
    });

    // Import Buttons
    $(document).on('click', '.import_button', function() {
      const buttonId = $(this).attr('id');

      switch (buttonId) {
        case 'tiny':
          //eslint-disable-next-line no-console
          console.log('Tiny');
          break;
        case 'tiny_record':
          //eslint-disable-next-line no-console
          console.log('Tiny Record');
          break;
        case 'import_cases':
          break;
        case 'import_experiences':
          //eslint-disable-next-line no-console
          console.log('Import Experiences');
          break;
        case 'import_tutor_conc':
          //eslint-disable-next-line no-console
          console.log('Import Tutor Conc');
          break;
        case 'import_resources':
          //eslint-disable-next-line no-console
          console.log('Import Resources');
          break;
      }
    });
}

export const init = () => {
    setEventListeners();
    setTinyConfig();
    setDefaultTinyMCE();
};