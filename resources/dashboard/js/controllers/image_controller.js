/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

/**
 * @extends Controller
 * @property {HTMLImageElement} previewTarget
 * @property {HTMLInputElement} filenameTarget
 * @property {HTMLAnchorElement} imageLinkTarget
 */
export default class ImageController extends window.Controller {

  static get targets() {
    return [
      'preview',
      'filename',
      'imageLink',
    ];
  }

  currentFile;

  connect() {
    this.updateCurrentFile();
  }

  updateCurrentFile() {
    const url = new URL(this.previewTarget.src);
    this.currentFile = url.pathname;
  }

  browse() {
    this['application'].getControllerForElementAndIdentifier(this.modal, 'mm-modal')
      .open({
        currentFile    : this.currentFile,
        inputController: this,
      });
  }

  update(filename) {
    this.previewTarget.setAttribute('src', filename);
    this.filenameTarget.value = filename;
    this.imageLinkTarget.href = filename;
    this.updateCurrentFile();
  }

  clear() {
    this.filenameTarget.value = '';
    this.previewTarget.src = '/icons/no-image.png';
  }

  /** @returns {HTMLElement} */
  get modal() { return document.getElementById(`screen-modal-media-manager`); }
}
