/*
 Mantra Tourus LLC
 Copyright (c) 2020-2022
 */

// noinspection DuplicatedCode

import AppController from './app_controller';

const MARKED_CLASS = 'mm-object_marked';

/**
 * @extends Controller
 * @property {HTMLElement} fsObjectTarget
 * @property {HTMLElement[]} fsObjectTargets
 * @property {HTMLElement} dirTarget
 * @property {HTMLInputElement} dirInputTarget
 */
export default class MediaManagerController extends AppController {

  clickTimer;
  clickDouble = false;

  currentFile;
  currentPath = '';
  markedObject;

  static targets = [
    'fsObject',
    'dir',
    'dirInput',
  ];

  connect() {
    this.loadObjects();
  }

  loadObjects() {
    this.lockInterface();
    this.get('/media-api', {
      path: this.currentPath,
      tpl : 'page',
    }).then(response => {
      this.element.innerHTML = response.content;
      this.unlockInterface();
    }).catch(e => {
      this.unlockInterface();
      console.error(e);
      this.alert('Response error', e);
      // this.element.innerHTML = `
      //     <h4>${e.status} ${e.statusText}</h4>
      //     <h5>${e['responseJSON'].exception}</h5>
      //     <h6>${e['responseJSON'].message}</h6>
      //     <code>${e['responseJSON'].file} :: ${e['responseJSON'].line}</code>
      //   `;
    });
  }

  markObject(e) {
    if (e.target.closest('.mm-object__menu')) return;
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
      }
    }, 50);
  }

  selectObject(e) {
    this.clickDouble = true;
    clearTimeout(this.clickTimer);
    const el = e.target.closest('.mm-object');
    el.classList.add(MARKED_CLASS);
    this.markedObject = el;
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
    // if (this.markedObject.dataset.objectType === 'file') {
    //   const path = [
    //     '/storage/media',
    //     this.currentPath,
    //     this.markedObject.dataset.objectFilename,
    //   ].filter(s => !!s).join('/');
    // }
  }

  delete(e) {
    const el = e.target.closest('.mm-object');
    if (!confirm('Delete object [' + el.dataset.objectFilename + '] ?')) return;
    this.lockInterface();

    const formData = new FormData;
    formData.append('path', this.currentPath);
    formData.append('file', el.dataset.objectFilename);

    this.post('/media-api/delete', formData)
      .then(() => {
        this.unlockInterface();
        this.loadObjects();
      })
      .catch((error) => {
        this.unlockInterface();
        this.alert('Error', e, 'danger');
        console.warn(error);
      });
  }

  upload(event) {
    if (!event.target.files[0]) return;

    this.lockInterface();

    let reader = new FileReader();
    reader.readAsDataURL(event.target.files[0]);

    reader.onerror = () => { this.unlockInterface(); };
    reader.onabort = () => { this.unlockInterface(); };

    reader.onloadend = () => {
      const body = new FormData();

      body.append('file', event.target.files[0]);
      body.append('path', this.currentPath);

      this.post('/media-api/upload', body)
        .then(() => {
          this.loadObjects();
        })
        .catch((error) => {
          this.unlockInterface();
          this.alert('Validation error', 'File upload error', 'danger');
          console.warn(error);
        });
    };

  }

  dirToggle() {
    if (!this.dirTarget.hidden) {
      this.dirInputTarget.value = '';
    }
    this.dirTarget.hidden = !this.dirTarget.hidden;
  }

  createDir() {
    const value = this.dirInputTarget.value;
    if (!value.trim()) {
      this.dirInputTarget.classList.add('is-invalid');
      this.alert('Validation error', 'Please, enter the folder name', 'danger');
      return;
    }

    if (!/^[-_a-z0-9]+$/.test(value)) {
      this.dirInputTarget.classList.add('is-invalid');
      this.alert('Validation error', 'The folder name must contain<br>only "a"-"z", "0"-"9", "-", "_" characters.', 'danger');
      return;
    }

    this.lockInterface();
    let body = new FormData;
    body.append('path', this.currentPath);
    body.append('dir', value);

    this.post('/media-api/create', body)
      .then((response) => {
        if (response.status) {
          this.loadObjects();
        } else {
          this.unlockInterface();
          this.alert('Validation error', response.message);
        }
      })
      .catch((error) => {
        this.unlockInterface();
        this.alert('Create folder error', error, 'danger');
        console.warn(error);
      });
  }

  resetValidation() {
    this.dirInputTarget.classList.remove('is-invalid');
  }

  lockInterface() {
    this.element.classList.add('mm-busy');
  }

  unlockInterface() {
    this.element.classList.remove('mm-busy');
  }
}
