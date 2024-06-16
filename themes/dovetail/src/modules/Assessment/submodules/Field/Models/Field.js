class Field {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      title: '',
      code: '',
      type: '',
      sort: '',
      created: '',
      form_id: '',
      file: '',
      metadata: {

      },
    }

    this.fieldTypes = [
      {
        icon: 'mdi-radiobox-marked',
        name: ' Multiple Choice',
        code: 'MultipleChoiceField'
      },
      {
        icon: 'mdi-checkbox-marked',
        name: ' Checkbox',
        code: 'CheckboxField'
      },
      {
        icon: 'mdi-text-short',
        name: ' Short Answer',
        code: 'ShortAnswerField'
      },
      {
        icon: 'mdi-text',
        name: ' Essay',
        code: 'EssayField'
      },
      {
        icon: 'mdi-arrow-down-drop-circle',
        name: ' Drop-down',
        code: 'DropDownField'
      },
      {
        icon: 'mdi-dots-horizontal',
        name: ' Linear Scale',
        code: 'LinearScaleField'
      },
    ]
  }
}

export default Field
