import {setupForElementId} from "editor_tiny/editor";
import {clean} from "./cleaner";

/**
 * Create tinyMCE in an elementId.
 * @param {string} elementId - The id of the area to create tinyMCE in.
 * @return {void}
 */
export const createTinyMCE = (elementId) => {
  const options = window.dta_tiny_config;

  if (!options) {
    return;
  }
  setupForElementId({elementId, options});
  clean();
};

/**
 * Remove tinyMCE from an area.
 * @param {string} area - The id of the area to remove tinyMCE from.
 * @return {void}
 */
export const removeTinyMCEFromArea = (area) => {
  window.tinymce.get(area).remove();
};

/**
 * Get the content of a tinyMCE area.
 * @param {string} area - The id of the area to get the content from.
 * @returns {string} The content of the tinyMCE area.
 */
export const getTinyMCEContent = (area) => {
  return window.tinyMCE.get(area).getContent();
};