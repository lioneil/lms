<template>
  <div class="dt-editor">
    <ckeditor :editor="editor" v-model="editorData" :config="editorConfig"></ckeditor>
    <input type="hidden" :name="name" v-model="editorData">
  </div>
</template>

<script>
import ClassicEditor from '@ckeditor/ckeditor5-editor-classic/src/classiceditor';

import Essentials from '@ckeditor/ckeditor5-essentials/src/essentials';
import UploadAdapter from '@ckeditor/ckeditor5-adapter-ckfinder/src/uploadadapter';
import Autoformat from '@ckeditor/ckeditor5-autoformat/src/autoformat';
import Bold from '@ckeditor/ckeditor5-basic-styles/src/bold';
import Italic from '@ckeditor/ckeditor5-basic-styles/src/italic';
import Code from '@ckeditor/ckeditor5-basic-styles/src/code';
import BlockQuote from '@ckeditor/ckeditor5-block-quote/src/blockquote';
import CKFinder from '@ckeditor/ckeditor5-ckfinder/src/ckfinder';
import EasyImage from '@ckeditor/ckeditor5-easy-image/src/easyimage';
import Heading from '@ckeditor/ckeditor5-heading/src/heading';
import Image from '@ckeditor/ckeditor5-image/src/image';
import ImageCaption from '@ckeditor/ckeditor5-image/src/imagecaption';
import ImageStyle from '@ckeditor/ckeditor5-image/src/imagestyle';
import ImageToolbar from '@ckeditor/ckeditor5-image/src/imagetoolbar';
import ImageUpload from '@ckeditor/ckeditor5-image/src/imageupload';
import ImageResize from '@ckeditor/ckeditor5-image/src/imageresize';
import Indent from '@ckeditor/ckeditor5-indent/src/indent';
import Link from '@ckeditor/ckeditor5-link/src/link';
import List from '@ckeditor/ckeditor5-list/src/list';
import MediaEmbed from '@ckeditor/ckeditor5-media-embed/src/mediaembed';
import Paragraph from '@ckeditor/ckeditor5-paragraph/src/paragraph';
import PasteFromOffice from '@ckeditor/ckeditor5-paste-from-office/src/pastefromoffice';
import Table from '@ckeditor/ckeditor5-table/src/table';
import TableToolbar from '@ckeditor/ckeditor5-table/src/tabletoolbar';
import TextTransformation from '@ckeditor/ckeditor5-typing/src/texttransformation';
import RemoveFormat from '@ckeditor/ckeditor5-remove-format/src/removeformat';
import Alignment from '@ckeditor/ckeditor5-alignment/src/alignment';
import Font from '@ckeditor/ckeditor5-font/src/font';
import FontFamily from '@ckeditor/ckeditor5-font/src/fontfamily';
import FontSize from '@ckeditor/ckeditor5-font/src/fontsize';
import FontColor from '@ckeditor/ckeditor5-font/src/fontcolor';
import FontBackgroundColor from '@ckeditor/ckeditor5-font/src/fontbackgroundcolor';


import DovetailUploadAdapter from './Adapter/UploadAdapter';

export default {
  props: ['url', 'name', 'value'],

  computed: {
    editorData: {
      get () {
        return this.value
      },
      set (val) {
        this.$emit('input', val)
      },
    },
  },

  data: (vm) => ({
    editor: ClassicEditor,
    editorConfig: {
      plugins: [
        Font,
        FontSize,
        FontColor,
        FontBackgroundColor,
        FontFamily,
        Code,
        Alignment,
        Essentials,
        UploadAdapter,
        Autoformat,
        Bold,
        Italic,
        BlockQuote,
        CKFinder,
        EasyImage,
        Heading,
        Image,
        ImageCaption,
        ImageStyle,
        ImageToolbar,
        ImageUpload,
        ImageResize,
        Indent,
        Link,
        List,
        MediaEmbed,
        Paragraph,
        PasteFromOffice,
        Table,
        TableToolbar,
        TextTransformation,
        RemoveFormat,
      ],
      toolbar: {
        items: [
          'fontFamily',
          'fontSize',
          'fontColor',
          'fontBackgroundColor',
          '|',
          'alignment',
          'bold',
          'italic',
          'code',
          'link',
          'bulletedList',
          'numberedList',
          '|',
          'indent',
          'outdent',
          '|',
          'imageUpload',
          'blockQuote',
          'insertTable',
          'mediaEmbed',
          'removeFormat',
        ]
      },
      fontFamily: {
        options: [
          'default',
          'Ubuntu Mono, Courier New, Courier, monospace',
        ]
      },
      fontSize: {
        options: [
          {
            title: 'Heading 1',
            model: '32px',
          },
          {
            title: 'Heading 2',
            model: '24px',
          },
          {
            title: 'Heading 3',
            model: '18.72px',
          },
          {
            title: 'Heading 4',
            model: '16px',
          },
          {
            title: 'Heading 5',
            model: '13.28px',
          },
          {
            title: 'Heading 6',
            model: '10.72px',
          },
        ]
      },
      fontColor: {
        columns: 6,
        colors: [
          {
            color: 'var(--primary)',
            label: 'Primary'
          },
          {
            color: 'var(--secondary)',
            label: 'Secondary'
          },
          {
            color: 'var(--accent)',
            label: 'Accent'
          },
          {
            color: 'var(--success)',
            label: 'Success'
          },
          {
            color: 'var(--info)',
            label: 'Info'
          },
          {
            color: 'var(--warning)',
            label: 'Warning'
          },
          {
            color: 'var(--error)',
            label: 'Error'
          },
          {
            color: 'var(--light)',
            label: 'Light',
            hasBorder: true
          },
          {
            color: 'var(--dark)',
            label: 'Dark'
          },
          {
            color: 'var(--text)',
            label: 'Text'
          },
          {
            color: 'var(--text-muted)',
            label: 'Muted'
          },
          {
            color: 'var(--link)',
            label: 'Grey'
          },
        ],
      },
      fontBackgroundColor: {
        columns: 6,
        colors: [
          {
            color: 'var(--primary)',
            label: 'Primary'
          },
          {
            color: 'var(--secondary)',
            label: 'Secondary'
          },
          {
            color: 'var(--accent)',
            label: 'Accent'
          },
          {
            color: 'var(--success)',
            label: 'Success'
          },
          {
            color: 'var(--info)',
            label: 'Info'
          },
          {
            color: 'var(--warning)',
            label: 'Warning'
          },
          {
            color: 'var(--error)',
            label: 'Error'
          },
          {
            color: 'var(--light)',
            label: 'Light',
            hasBorder: true
          },
          {
            color: 'var(--dark)',
            label: 'Dark'
          },
          {
            color: 'var(--text)',
            label: 'Text'
          },
          {
            color: 'var(--text-muted)',
            label: 'Muted'
          },
          {
            color: 'var(--link)',
            label: 'Grey'
          },
        ],
      },
      image: {
        styles: [
          'alignLeft', 'alignCenter', 'alignRight', 'full', 'side',
        ],
        resizeOptions: [
          {
            name: 'imageResize:original',
            value: null,
            icon: 'original'
          },
          {
            name: 'imageResize:25',
            value: '25',
            icon: 'small'
          },
          {
            name: 'imageResize:50',
            value: '50',
            icon: 'medium'
          },
          {
            name: 'imageResize:75',
            value: '75',
            icon: 'large'
          },
        ],
        toolbar: [
          'imageStyle:alignLeft', 'imageStyle:alignCenter', 'imageStyle:alignRight',
          '|',
          'imageStyle:full', 'imageStyle:side',
          '|', 'imageResize', '|',
          'imageTextAlternative'
        ],
      },
      table: {
        contentToolbar: [
          'tableColumn',
          'tableRow',
          'mergeTableCells'
        ]
      },

      extraPlugins: [ vm.adapter ],
      uploadUrl: vm.url,
    },
  }),

  methods: {
    adapter (editor) {
      editor.plugins.get('FileRepository').createUploadAdapter = function (loader) {
        return new DovetailUploadAdapter(loader, editor.config._config.uploadUrl);
      };
    }
  },
}
</script>
