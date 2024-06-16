<template>
  <div ref="editor-container" id="grapes-editor_container">
    <div class="panel__top">
      <div class="panel__devices"></div>
      <div class="panel__basic-actions"></div>
      <div class="panel__switcher"></div>
    </div>
    <div class="editor-row">
      <div class="editor-canvas">
        <div ref="gjs" id="gjs"></div>
      </div>
      <div class="panel__right">
        <div class="layers-container"></div>
        <div class="styles-container"></div>
        <div class="traits-container"></div>
        <div class="blocks-container"></div>
      </div>
    </div>
  </div>
</template>

<script>
import grapesjs from 'grapesjs';

export default {
  props: ['value'],

  data: () => ({
    editor: null,
    init: true,
  }),

  methods: {
    updateHtml (html) {
      this.$emit('input', html)
    },

    toggleFullScreen () {
      this.$emit('toggleFullScreen')
    },

    initEditor() {
      const { value } = this

      // # Declare custom component types
      const myNewComponentTypes = editor => {
        editor.DomComponents.addType('section', {
          isComponent: el => el.tagName === 'SECTION',

          model: {
            defaults: {
              tagName: 'section',
              draggable: '*',
              attributes: {
                class: 'grapesjs-section'
              },
              traits: [
                'id',
              ],
            }
          }
        });
        editor.DomComponents.addType('heading', {
          isComponent: el => el.tagName === 'h1',

          model: {
            defaults: {
              tagName: 'div',
              attributes: {
                class: 'grapesjs-heading'
              },
              traits: [
                'id',
              ],
            }
          }
        });
        editor.DomComponents.addType('form', {
          isComponent: el => el.tagName === 'FORM',

          model: {
            defaults: {
              tagName: 'form',
              attributes: {
                name: '',
                class: 'grapesjs-form'
              },
              traits: [
                'id',
                'action',
                'methods'
              ],
            }
          }
        });
        editor.DomComponents.addType('input', {
          // Make the editor understand when to bind `my-input-type`
          isComponent: el => el.tagName === 'INPUT',

          // Model definition
          model: {
            // Default properties
            defaults: {
              tagName: 'input',
              // draggable: 'form, form *', // Can be dropped only inside `form` elements
              droppable: false, // Can't drop other elements inside
              attributes: { // Default attributes
                type: 'text',
                name: '',
                placeholder: 'Insert text here',
                class: 'grapesjs-input'
              },
              traits: [
                'id',
                'name',
                'placeholder',
                {
                  name: 'type',
                  type: 'select',
                  options: [
                    {value:'text',name:'text'},
                    {value:'password',name:'password'},
                    {value:'email',name:'email'},
                    {value:'number',name:'number'},
                  ]
                 },
                { type: 'checkbox', name: 'required' },
              ],
            }
          }
        });
        editor.DomComponents.addType('textarea', {
          isComponent: el => el.tagName === 'TEXTAREA',

          model: {
            defaults: {
              tagName: 'textarea',
              droppable: false,
              attributes: {
                name: 'default-name',
                placeholder: 'Insert text here',
                class: 'grapesjs-textarea'
              },
              traits: [
                'id',
                'name',
                'placeholder',
                { type: 'checkbox', name: 'required' },
              ],
            }
          }
        });
        editor.DomComponents.addType('radio', {
          isComponent: el => el.tagName === 'INPUT',

          model: {
            defaults: {
              tagName: 'input',
              droppable: false,
              attributes: {
                name: 'default-name',
                type: 'radio',
                class: 'grapesjs-radio'
              },
              traits: [
                'id',
                'name',
                { type: 'checkbox', name: 'required' },
              ],
            }
          }
        });
        editor.DomComponents.addType('checkbox', {
          isComponent: el => el.tagName === 'INPUT',

          model: {
            defaults: {
              tagName: 'input',
              droppable: false,
              attributes: {
                name: 'default-name',
                type: 'checkbox',
                class: 'grapesjs-checkbox'
              },
              traits: [
                'id',
                'name',
                { type: 'checkbox', name: 'required' },
              ],
            }
          }
        });
        editor.DomComponents.addType('select', {
          isComponent: el => el.tagName === 'SELECT',

          model: {
            defaults: {
              tagName: 'select',
              droppable: 'option',
              attributes: {
                name: '',
                class: 'grapesjs-select',
              },
              traits: [
                'id',
                'name',
                { type: 'checkbox', name: 'required' },

              ],
            },
          }
        });
        editor.DomComponents.addType('option', {
          isComponent: el => el.tagName === 'OPTION',

          model: {
            defaults: {
              tagName: 'option',
              draggable: 'select',
              droppable: false,
              attributes: {
                name: '',
                class: 'grapesjs-option',
              },
              traits: [
                'id',
                'value',
                'label'
              ],
            }
          }
        });
        editor.DomComponents.addType('button', {
          isComponent: el => el.tagName === 'BUTTON',

          model: {
            defaults: {
              tagName: 'button',
              droppable: '*',
              attributes: {
                class: 'grapesjs-button v-btn v-btn--contained v-btn--router v-size--large primary-bg white-text',
                type: 'button',
              },
              traits: [
                'id',
                'name',
                {
                  name: 'type',
                  type: 'select',
                  options: [
                    {value:'button',name:'button'},
                    {value:'submit',name:'submit'},
                    {value:'reset',name:'reset'},
                  ]
                 },
              ],
            }
          }
        });
      };
      // # Declare custom component types

      // # Init editor
      this.editor = grapesjs.init({
        canvas: {
          styles: [
            'https://cdn.jsdelivr.net/npm/vuetify@2.x/dist/vuetify.min.css',
            window.location.origin + '/theme/dist/css/app.css',
          ]
        },
        // Indicate where to init the editor. You can also pass an HTMLElement
        container: this.$refs.gjs,
        // Add the defined types
        plugins: [ myNewComponentTypes ],
        // Get the content for the canvas directly from the element
        // As an alternative we could use: `components: '<h1>Hello World Component!</h1>'`,
        fromElement: false,
        // components: value,
        // Size of the editor
        height: '100%',
        width: 'auto',
        // Disable the storage manager for the moment
        storageManager: false,
        // Avoid any default panel
        // panels: { defaults: [] },

        // Setup blocks
        blockManager: {
          appendTo: '.blocks-container',
          blocks: [
            {
              id: 'section', // id is mandatory
              label: `
                <div>
                  <i class="mdi mdi-text-subject block-icon" title="Section" aria-hidden="true"></i><br />
                  <span>Section</span>
                </div>
              `,
              select: true,
              category: 'Basic',
              content: `
                <section data-gjs-type="section">
                  <h1 data-gjs-type="heading">This is a simple title</h1>
                  <div data-gjs-type="text">This is just a Lorem text: Lorem ipsum dolor sit amet</div>
                </section>`
            },
            {
              id: 'text',
              label: `
                <div>
                  <i class="mdi mdi-text-short block-icon" title="Text" aria-hidden="true"></i><br />
                  <span>Text</span>
                </div>`,
              select: true,
              category: 'Basic',
              content: '<div data-gjs-type="text">Insert your text here</div>',
            },
            {
              id: 'link',
              label: `
                <div>
                  <i class="mdi mdi-link block-icon" title="Link" aria-hidden="true"></i><br />
                  <span>Link</span>
                </div>
              `,
              select: true,
              category: 'Basic',
              content: `<a href="#" data-gjs-type="link">Link</a>`,
            },
            {
              id: 'link-block',
              label: `
                <div>
                  <i class="mdi mdi-link block-icon" title="Link block" aria-hidden="true"></i><br />
                  <span>Link Block</span>
                </div>
              `,
              select: true,
              category: 'Basic',
              content: `
                <a href="#" data-gjs-type="link" class="link-block" data-gjs-droppable="*" data-gjs-custom-name="Link block"></a>
                <style>
                  .link-block {
                    min-height: 50px;
                    min-width: 50px;
                    display: block;
                  }
                </style>`,
            },
            {
              id: 'image',
              label: `
                <div>
                  <i class="mdi mdi-image block-icon" title="Image" aria-hidden="true"></i><br />
                  <span>Image</span>
                </div>
              `,
              // Select the component once it's dropped
              select: true,
              category: 'Basic',
              // You can pass components as a JSON instead of a simple HTML string,
              // in this case we also use a defined component type `image`
              content: { type: 'image' },
              // This triggers `active` event on dropped components and the `image`
              // reacts by opening the AssetManager
              activate: true,
            },
            {
              id: 'video',
              label: `
                <div>
                  <i class="mdi mdi-video block-icon" title="Video" aria-hidden="true"></i><br />
                  <span>Video</span>
                </div>
              `,
              select: true,
              category: 'Basic',
              content: { type: 'video' },
              activate: true,
            },
            {
              id: '2-columns',
              label: `
                <div>
                  <i class="mdi mdi-view-column block-icon" title="2 column" aria-hidden="true"></i><br />
                  <span>2 columns</span>
                </div>
              `,
              select: true,
              category: 'Basic',
              content: `
                <div class="row" data-gjs-draggable="*" data-gjs-droppable=".col" data-gjs-custom-name="Row">
                  <div class="col col-12 col-md" data-gjs-draggable=".row" data-gjs-droppable="*" data-gjs-custom-name="Column"></div>
                  <div class="col col-12 col-md" data-gjs-draggable=".row" data-gjs-droppable="*" data-gjs-custom-name="Column"></div>
                </div>
              `,
            },
            {
              id: 'card',
              label: `
                <div>
                  <i class="mdi mdi-card block-icon" title="Card" aria-hidden="true"></i><br />
                  <span>Card</span>
                </div>
              `,
              select: true,
              category: 'Basic',
              content: `
                <div class="v-card v-sheet" data-gjs-draggable="*" data-gjs-droppable=".v-card__title .v-card__text .v-card__actions" data-gjs-custom-name="Card">
                  <div class="v-card__title" data-gjs-draggable=".v-card" data-gjs-droppable="*"><div data-gjs-type="text">Card title</div></div>
                  <div class="v-card__text" data-gjs-draggable=".v-card" data-gjs-droppable="*">
                    <div data-gjs-type="text" >Youre text here</div>
                  </div>
                  <div class="v-card__actions" data-gjs-draggable=".v-card" data-gjs-droppable="*">
                    <button data-gjs-type="button" type="button" data-gjs-custom-name="Button">Button</button>
                  </div>
                </div>
              `,
            },
            {
              id: 'form',
              label: `
                <div>
                  <i class="mdi mdi-alpha-f block-icon" title="Form" aria-hidden="true"></i><br />
                  <span>Form</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <form data-gjs-type="form">
                  <div class="row">
                    <div class="col col-12">
                      <div data-gjs-type="text">Name</div>
                      <input data-gjs-type="input" type="text" placeholder="Type your name here" />
                    </div>
                    <div class="col col-12">
                      <div data-gjs-type="text">E-mail</div>
                      <input data-gjs-type="input" type="email" placeholder="Type your email here" />
                    </div>
                    <div class="col col-12">
                      <div data-gjs-type="text">Gender</div>
                      <input data-gjs-type="radio" /> M
                      <input data-gjs-type="radio" /> G
                    </div>
                    <div class="col col-12">
                      <button data-gjs-type="button" type="button" data-gjs-custom-name="Button"><div data-gjs-type="text">Button</div></button>
                    </div>
                  </div>
                </form>
              `,
            },
            {
              id: 'input-text',
              label: `
                <div>
                  <i class="mdi mdi-text-short block-icon" title="Input" aria-hidden="true"></i><br />
                  <span>Input</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <input data-gjs-type="input" type="text" placeholder="Type your text here" data-gjs-custom-name="Input" />
              `,
            },
            {
              id: 'textarea',
              label: `
                <div>
                  <i class="mdi mdi-card-text block-icon" title="Textarea" aria-hidden="true"></i><br />
                  <span>Textarea</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <textarea data-gjs-type="textarea"></textarea>
              `,
            },
            {
              id: 'input-checkbox',
              label: `
                <div>
                  <i class="mdi mdi-checkbox-marked block-icon" title="Checkbox" aria-hidden="true"></i><br />
                  <span>Checkbox</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <input data-gjs-type="checkbox" type="checkbox" data-gjs-custom-name="Checkbox" />
              `,
            },
            {
              id: 'input-radio',
              label: `
                <div>
                  <i class="mdi mdi-radiobox-marked block-icon" title="Radio" aria-hidden="true"></i><br />
                  <span>Radio</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <input data-gjs-type="radio" type="radio" data-gjs-custom-name="Radio" />
              `,
            },
            {
              id: 'select',
              label: `
                <div>
                  <i class="mdi mdi-form-select block-icon" title="Select" aria-hidden="true"></i><br />
                  <span>Select</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <select data-gjs-type="select">
                  <option value="">Option 1</option>
                </select>
              `,
            },
            {
              id: 'option',
              label: `
                <div>
                  <i class="mdi mdi-format-list-bulleted block-icon" title="Select" aria-hidden="true"></i><br />
                  <span>Option</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <option  data-gjs-type="option" value="">Option</option>
              `,
            },
            {
              id: 'button',
              label: `
                <div>
                  <i class="mdi mdi-gesture-tap-button block-icon" title="Button" aria-hidden="true"></i><br />
                  <span>Button</span>
                </div>
              `,
              select: true,
              category: 'Forms',
              content: `
                <button data-gjs-type="button" type="button" data-gjs-custom-name="Button"><div data-gjs-type="text">Button</div></button>
              `,
            },
          ]
        },

        layerManager: {
          appendTo: '.layers-container'
        },

        // We define a default panel as a sidebar to contain layers
        panels: {
          defaults: [
            {
              id: 'panel-top',
              el: '.panel__top',
            },
            {
              id: 'layers',
              el: '.panel__right',
              // Make the panel resizable
              resizable: {
                // maxDim: 350,
                minDim: 200,
                tc: 0, // Top handler
                cl: 1, // Left handler
                cr: 0, // Right handler
                bc: 0, // Bottom handler
                // Being a flex child we need to change `flex-basis` property
                // instead of the `width` (default)
                keyWidth: 'flex-basis',
              },
            },
            {
              id: 'panel-switcher',
              el: '.panel__switcher',
              buttons: [
                {
                  id: 'show-layers',
                  active: true,
                  label: '<i class="mdi mdi-menu" title="Layers" aria-hidden="true"></i>',
                  command: 'show-layers',
                  // Once activated disable the possibility to turn it off
                  togglable: false,
                },
                {
                  id: 'show-style',
                  active: true,
                  label: '<i class="mdi mdi-brush" title="Styles" aria-hidden="true"></i>',
                  command: 'show-styles',
                  togglable: false,
                },
                {
                  id: 'show-traits',
                  active: true,
                  label: '<i class="mdi mdi-cog" title="Settings" aria-hidden="true"></i>',
                  command: 'show-traits',
                  togglable: false,
                },
                {
                  id: 'show-blocks',
                  active: true,
                  label: '<i class="mdi mdi-view-grid-plus" title="Blocks" aria-hidden="true"></i>',
                  command: 'show-blocks',
                  togglable: false,
                }
              ],
            },
            {
              id: 'panel-devices',
              el: '.panel__devices',
              buttons: [
                {
                  id: 'device-desktop',
                  label: '<i class="mdi mdi-monitor" title="Desktop view" aria-hidden="true"></i>',
                  command: 'set-device-desktop',
                  active: true,
                  togglable: false,
                },
                {
                  id: 'device-tablet',
                  label: '<i class="mdi mdi-tablet" title="Tablet view" aria-hidden="true"></i>',
                  command: 'set-device-tablet',
                  active: true,
                  togglable: false,
                },
                {
                  id: 'device-mobile',
                  label: '<i class="mdi mdi-cellphone" title="Phone view" aria-hidden="true"></i>',
                  command: 'set-device-mobile',
                  active: true,
                  togglable: false,
                }
              ],
            },
            {
              id: 'basic-actions',
              el: '.panel__basic-actions',
              buttons: [
                {
                  id: 'visibility',
                  className: 'btn-toggle-borders',
                  label: '<i class="mdi mdi-checkbox-blank-outline" title="Outline" aria-hidden="true"></i>',
                  command: 'sw-visibility', // Built-in command
                  togglable: true
                },
                {
                  id: 'view',
                  className: 'btn-open-view',
                  label: '<i class="mdi mdi-eye" title="Preview" aria-hidden="true"></i>',
                  command: 'preview',
                },
                {
                  id: 'export',
                  className: 'btn-open-export',
                  label: '<i class="mdi mdi-code-tags" title="Code" aria-hidden="true"></i>',
                  command: 'export-template',
                  context: 'export-template', // For grouping context of buttons from the same panel
                },
                {
                  id: 'show-json',
                  className: 'btn-show-json',
                  label: '<i class="mdi mdi-code-json" title="JSON" aria-hidden="true"></i>',
                  context: 'show-json',
                  command(editor) {
                    editor.Modal.setTitle('Components JSON')
                      .setContent(`<textarea style="width:100%; height: 250px;">
                        ${JSON.stringify(editor.getComponents())}
                      </textarea>`)
                      .open();
                  },
                },
                {
                  id: 'undo',
                  className: 'btn-undo',
                  label: '<i class="mdi mdi-undo" title="Undo" aria-hidden="true"></i>',
                  command: 'core:undo',
                },
                {
                  id: 'redo',
                  className: 'btn-redo',
                  label: '<i class="mdi mdi-redo" title="Redo" aria-hidden="true"></i>',
                  command: 'core:redo',
                },
                {
                  id: 'clear',
                  className: 'btn-clear',
                  label: '<i class="mdi mdi-delete" title="Clear canvas" aria-hidden="true"></i>',
                  command(editor) {
                    if(confirm(`Are you sure to clear the canvas?`)) editor.runCommand('core:canvas-clear')
                  },
                },
                {
                  id: 'fullScreen',
                  className: 'btn-fullscreen',
                  label: '<i class="mdi mdi-pencil-off" title="Exit editor" aria-hidden="true"></i>',
                  command: 'fullScreenEditor',
                },
              ],
            }
          ]
        },
        // The Selector Manager allows to assign classes and
        // different states (eg. :hover) on components.
        // Generally, it's used in conjunction with Style Manager
        // but it's not mandatory
        selectorManager: {
          appendTo: '.styles-container'
        },
        styleManager: {
          appendTo: '.styles-container',
          sectors: [
            {
              name: 'General',
              open: false,
              buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
            },
            {
              name: 'Dimension',
              open: false,
              // Use built-in properties
              buildProps: ['width', 'height', 'min-width', 'min-height', 'max-width', 'max-height', 'padding', 'margin'],
            },
            {
              name: 'Typography',
              open: false,
              buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
              properties: [
                {
                  id: 'text-decoration',
                  name: 'Text decoration',
                  property: 'text-decoration',
                  type: 'select',
                  defaults: 'none',
                  // List of options, available only for 'select' and 'radio'  types
                  options: [
                    { value: 'none', name: 'None' },
                    { value: 'Underline', name: 'Underline' },
                    { value: '32px', name: 'Big' },
                  ],
                }
              ]
            },
            {
              name: 'Decoration',
              open: false,
              buildProps: ['background-color', 'background', 'border', 'border-radius','opacity'],
              properties: [
                {
                  name    : 'Box shadow',
                  property  : 'box-shadow',
                  type    : 'stack',
                  preview   : true,
                  // List of nested properties, available only for 'stack' and 'composite'  types
                  properties  :
                  [
                    {
                      name:     'Shadow type',
                      // Nested properties with stack/composite type don't require proper 'property' name
                      // as all of them will be merged to parent property, eg. box-shadow: X Y ...;
                      property:   'shadow-type',
                      type:     'select',
                      defaults:   '',
                      list:   [ { value : '', name : 'Outside', },
                                  { value : 'inset', name : 'Inside', }],
                    },{
                      name:     'X position',
                      property:   'shadow-x',
                      type:     'integer',
                      units:    ['px','%'],
                      defaults :  0,
                    },{
                      name:     'Y position',
                      property:   'shadow-y',
                      type:     'integer',
                      units:    ['px','%'],
                      defaults :  0,
                    },{
                      name:     'Blur',
                      property: 'shadow-blur',
                      type:     'integer',
                      units:    ['px'],
                      defaults :  0,
                      min:    0,
                    },{
                      name:     'Spread',
                      property:   'shadow-spread',
                      type:     'integer',
                      units:    ['px'],
                      defaults :  0,
                    },{
                      name:     'Color',
                      property:   'shadow-color',
                      type:     'color',
                      defaults:   'black',
                    },
                  ],
                }
              ]
            },
            {
              name: 'Flex',
              open: false,
              buildProps: ['flex', 'flex-basis', 'flex-direction', 'justify-content', 'align-items', 'flex-grow', 'flex-shrink', 'align-self'],
            },
            {
              name: 'Extra',
              open: false,
              buildProps: ['transition', 'perspective', 'transform'],
            }
          ]
        },

        traitManager: {
          appendTo: '.traits-container',
        },

        deviceManager: {
          devices: [
            {
              name: 'Desktop',
              width: '', // default size
            },
            {
              name: 'Tablet',
              width: '700px', // this value will be used on canvas width
              widthMedia: '960px', // this value will be used in CSS @media
            },
            {
              name: 'Mobile',
              width: '320px', // this value will be used on canvas width
              widthMedia: '480px', // this value will be used in CSS @media
            }
          ]
        },
      });
      // # Init editor

      // # Define commands
      this.editor.Commands.add('show-layers', {
        getRowEl(editor) { return editor.getContainer().closest('.editor-row'); },
        getLayersEl(row) { return row.querySelector('.layers-container') },

        run(editor) {
          const lmEl = this.getLayersEl(this.getRowEl(editor));
          lmEl.style.display = '';
        },
        stop(editor) {
          const lmEl = this.getLayersEl(this.getRowEl(editor));
          lmEl.style.display = 'none';
        },
      });

      this.editor.Commands.add('show-styles', {
        getRowEl(editor) { return editor.getContainer().closest('.editor-row'); },
        getStyleEl(row) { return row.querySelector('.styles-container') },

        run(editor) {
          const smEl = this.getStyleEl(this.getRowEl(editor));
          smEl.style.display = '';
        },
        stop(editor) {
          const smEl = this.getStyleEl(this.getRowEl(editor));
          smEl.style.display = 'none';
        },
      });

      this.editor.Commands.add('show-traits', {
        getTraitsEl(editor) {
          const row = editor.getContainer().closest('.editor-row');
          return row.querySelector('.traits-container');
        },
        run(editor) {
          this.getTraitsEl(editor).style.display = '';
        },
        stop(editor) {
          this.getTraitsEl(editor).style.display = 'none';
        },
      });

      this.editor.Commands.add('show-blocks', {
        getTraitsEl(editor) {
          const row = editor.getContainer().closest('.editor-row');
          return row.querySelector('.blocks-container');
        },
        run(editor) {
          this.getTraitsEl(editor).style.display = '';
        },
        stop(editor) {
          this.getTraitsEl(editor).style.display = 'none';
        },
      });

      this.editor.Commands.add('set-device-desktop', {
        run: editor => editor.setDevice('Desktop')
      });

      this.editor.Commands.add('set-device-tablet', {
        run: editor => editor.setDevice('Tablet')
      });

      this.editor.Commands.add('set-device-mobile', {
        run: editor => editor.setDevice('Mobile')
      });

      const toggleFullScreen = this.toggleFullScreen

      this.editor.Commands.add('fullScreenEditor', function () {
          toggleFullScreen()
      });
      // # Define commands

      // # On canvas update
      const updateHtml = this.updateHtml
      this.editor.on('update canvas:drop', () => {
        if(this.init) {
          this.init = false
        }
        else {
          // # Return canvas components
          let content = null
          if(this.editor.getHtml() !== '')
            content = JSON.stringify({ html: this.editor.getHtml(), css: this.editor.getCss() })
          updateHtml(content)
          // # Return canvas components
        }
      }, this);
      // # On canvas update

      // # Set canvas components
      if(value) {
        const { html, css } = JSON.parse(value)
        this.editor.setComponents(html)
        this.editor.setStyle(css)
      }
      // # Set canvas components
    }
  },

  mounted() {
    this.initEditor()
  },
}
</script>
