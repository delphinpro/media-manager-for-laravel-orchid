/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

import AppController from './app_controller';

const MARKED_CLASS = 'mm-object_marked';

/**
 * @extends AppController
 * @property {HTMLElement} fsObjectTarget
 * @property {HTMLElement[]} fsObjectTargets
 * @property {HTMLButtonElement} btnApplyTarget
 */
export default class MmModalController extends AppController {

  static targets = [
    'fsObject',
    'btnApply',
  ];

  clickTimer;
  clickDouble = false;

  currentFile;
  currentPath = '';
  markedObject;

  get $element() { return $(this.element); }

  /** @returns {HTMLElement} */
  get modalBody() { return this.element.querySelector('.modal-body'); }

  connect() {
    this.currentPath = localStorage.getItem('mm.currentPath') ?? '';
    this.$element.on('shown.bs.modal', () => {
      this.loadObjects();
    });
    this.$element.on('hide.bs.modal', () => {
      if (!this['element'].classList.contains('fade')) {
        this['element'].classList.add('fade', 'in');
      }
    });
  }

  loadObjects() {
    localStorage.setItem('mm.currentPath', this.currentPath);
    this.modalBody.classList.add('mm-busy');
    this.get('/media-api', {
      path: this.currentPath,
      tpl : 'modal',
    }).then(response => {
      this.modalBody.innerHTML = response.content;
      this.modalBody.classList.remove('mm-busy');
    }).catch((e) => {
      this.modalBody.classList.remove('mm-busy');
      console.error(e);
      this.alert('Response error', e);
      // this.modalBody.innerHTML = `
      //     <h4>${e.status} ${e.statusText}</h4>
      //     <h5>${e['responseJSON'].exception}</h5>
      //     <h6>${e['responseJSON'].message}</h6>
      //     <code>${e['responseJSON'].file} :: ${e['responseJSON'].line}</code>
      //   `;
    });
  }

  open(params) {
    this.currentFile = this.getFileNameFromUrl(params?.currentFile);
    this.inputController = params?.inputController;
    this.modalBody.classList.add('mm-busy');
    this.$element.modal('toggle');
  }

  close() {
    this.$element.modal('hide');
  }

  getFileNameFromUrl(url) {
    return decodeURI(url.split('/').pop().toString());
  }

  markObject(e) {
    this.clickDouble = false;
    this.clickTimer = setTimeout(() => {
      if (!this.clickDouble) {
        const el = e.target.closest('.mm-object');
        if (!el.classList.contains(MARKED_CLASS)) {
          this.fsObjectTargets.forEach(obj => {
            if (obj !== el) obj.classList.remove(MARKED_CLASS);
          });
          this.markedObject = el;
        } else {
          this.markedObject = null;
        }
        el.classList.toggle(MARKED_CLASS);
        this.btnApplyTarget.disabled = !(this.markedObject && this.markedObject.dataset.objectType === 'file');
      }
    }, 50);
  }

  selectObject(e) {
    this.clickDouble = true;
    clearTimeout(this.clickTimer);
    const el = e.target.closest('.mm-object');
    el.classList.add(MARKED_CLASS);
    this.markedObject = el;
    this.btnApplyTarget.disabled = !(this.markedObject && this.markedObject.dataset.objectType === 'file');
    this.applySelect();
  }

  goto(e) {
    e.preventDefault();
    const el = e.target.closest('[data-path-to]');
    this.currentPath = el.dataset.pathTo;
    this.loadObjects();
  }

  applySelect() {
    if (!this.markedObject) return;
    if (this.markedObject.dataset.objectType === 'dir') {
      this.currentPath = [
        this.currentPath,
        this.markedObject.dataset.objectName,
      ].filter(s => !!s).join('/');
      this.loadObjects();
    }
    if (this.markedObject.dataset.objectType === 'file') {
      const path = [
        '/storage/media',
        this.currentPath,
        this.markedObject.dataset.objectFilename,
      ].filter(s => !!s).join('/');
      this.inputController.update(path);
      this.close();
    }
  }
}
