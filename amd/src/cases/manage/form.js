import $ from 'jquery';
import Templates from 'core/templates';
import Notification from 'core/notification';
import {
    sectionTextUpsert,
    sectionTextDelete,
    ourcaseEdit
} from 'local_dta/repositories/ourcases_repository';
import ModalFactory from 'core/modal_factory';
import {get_string} from 'core/str';
import {setupForElementId} from 'editor_tiny/editor';
import setEventListeners from './listeners';

let sectionTextModal;
let tinyConfig;
let urlView = null;

/**
 * Add a new text section to the page.
 * @return {void}
 */
function addTextSection() {
    Templates.render('local_dta/cases/section-text-edit', {
        id: new Date().getTime(),
        description: null,
        exist: true
    }).then((html) => {
        $('#sections-body').append(html);
        createTinyMCE($('.card-body:has(textarea)').last().find('textarea').attr('id'));
        window.scrollTo(0, document.body.scrollHeight);
        return;
    }).fail(Notification.exception);
}


/**
 * Change the section to edit mode.
 * @param {boolean} toView - Whether to change to view mode.
 * @param {number} id - The id of the section to change.
 * @return {void}
 */
function changeSectionToEdit(toView = false, id) {
    const description = toView ? $(`#content_${id}`).html() : $(`#content_${id}`).val();
    const template = toView ? 'local_dta/cases/section-text-edit' : 'local_dta/cases/section-text-view';
    Templates.render(template,
     {id, description}).then((html) => {
        return $(`#section_${id}`).replaceWith(html);
    }).fail(Notification.exception);
}


/**
 * Change the section to edit mode.
 * @param {number} id - The id of the section to change.
 * @param {number} toId - The id of the section to change to.
 * @return {void}
 */
function changeSectionToNewId(id, toId) {
    const description = $(`#content_${id}`).val();
    Templates.render("local_dta/cases/section-text-view",
        {id: toId, description, exist: true}).then((html) => {
           return $(`#section_${id}`).replaceWith(html);
    }).fail(Notification.exception);
}



/**
 * Set event listeners for the module.
 * @param {number} id - The id of the section to remove.
 * @return {void}
 *
 */
function upsertSection(id) {
    const sectionid = $(`#not_exist_${id}`).val() ? null : $(`#sectionid_${id}`).val();
    const ourcaseid = $('#ourcases-id').val();
    const title = null;
    const text = $(`#content_${id}`).val();
    const sequence = -1;

    return sectionTextUpsert({ourcaseid, sectionid, title, text, sequence})
        .then((data) => {
            if (data && data.result) {
                changeSectionToNewId(id, data.sectionid);
                return data;
            } else {
                throw new Error('No se pudo realizar la actualización de la sección.');
            }
        })
        .fail(() => {
            throw new Error('Hubo un error al realizar la solicitud: ');
        });
}


/**
 * Remove a section from the page.
 * @param {number} sectionid - The id of the section to remove.
 * @return {void}
 */
function removeSection(sectionid) {
    if ($(`#not_exist_${sectionid}`).val()) {
        if (sectionid) {
            $(`#section_${sectionid}`).remove();
        }
    } else {
        changeSectionToEdit(false, sectionid);
    }
}

/**
 * Show the delete section modal
 * @param {number} sectionid - The id of the section to remove.
 * @return {void}
 */
async function showDeleteSectionModal(sectionid) {
    sectionTextModal = await ModalFactory.create({
        title: get_string("ourcases_section_text_delete_modal_title", "local_dta"),
        body: Templates.render('local_dta/cases/section-text-modal', {modalDeleteId: sectionid}),
    });
    $("#modal_delete_id").val();
    sectionTextModal.show();
}

/**
 * Show save case modal
 * @return {void}
 */
async function showSaveCase() {
    const button = $('#header-edit-button')[0];
    const button2 = $('#section-edit-button')[0];
    const saveModal = await ModalFactory.create({
        title: get_string("ourcases_modal_save_title", "local_dta"),
        body: Templates.render('local_dta/cases/manage-save-modal', {
            havePendingChanges: (button || button2) ? true : false,
        }),
    });
    saveModal.show();
}


/**
 * Delete text section
 * @return {void}
 */
function deleteSection() {
    const sectionid = $("#modal_delete_id").val();
    const ourcaseid = $('#ourcases-id').val();

    sectionTextDelete({ourcaseid, sectionid}).then((data) => {
        if (data.result) {
            sectionTextModal.hide();
            sectionTextModal = null;
            $(`#section_${sectionid}`).remove();
        }
        return;
    }).fail(Notification.exception);
}

/**
 * Edit a case.
 * @return {void}
 */
function changeStatusToComplete() {
    const ourcaseid = $('#ourcases-id').val();
    const status = 1;
    const args = {ourcaseid, status};

    ourcaseEdit(args).then((data) => {
        if (data.result) {
            window.location.href = urlView;
        }
        return;
    }).fail(Notification.exception);
}

/**
 * Remove tinyMCE from an area.
 * @param {string} area - The id of the area to remove tinyMCE from.
 * @return {void}
 */
function removeTinyMCEFromArea(area) {
    window.tinymce.get(area).remove();
}

/**
 * Remove tinyMCE from an area.
 * @param {string} area - The id of the area to remove tinyMCE from.
 * @returns {string} The content of the tinyMCE area.
 */
function getTinyMCEContent(area) {
    return window.tinyMCE.get(area).getContent();
}

/**
 * Create tinyMCE in an area.
 * @param {string} area - The id of the area to create tinyMCE in.
 * @return {void}
 */
function createTinyMCE(area) {
    setTimeout(() => {
        setupForElementId({
            elementId: `${area}`,
            options: tinyConfig,
        });
      }, 200);
}



/**
 * Initialize the module.
 * @param {string} dataUrlView - The url to redirect to after completing the case.
 * @return {void}
 */
export const init = async(dataUrlView) => {
    setEventListeners();
    urlView = dataUrlView;
    tinyConfig = window.dta_tiny_config;
    createTinyMCE('section-header-description');
};