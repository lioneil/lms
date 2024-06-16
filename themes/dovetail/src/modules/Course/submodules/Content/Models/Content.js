class Content {
  constructor () {
    this.loading = false
    this.isPrestine = true
    this.isValid = true
    this.errors = []

    this.data = {
      course: {
        meta: {
          lessons: {}
        }
      },
      title: '',
      subtitle: '',
      slug: '',
      description: '',
      content: null,
      sort: '',
      created: '',
      type: '',
      file: '',
      metadata: {
        type: '',
        parent: null,
      },
    }

    this.contentTypes = [
      {
        icon: 'mdi-format-quote-open-outline',
        name: 'Section',
        section: true,
      },
      {
        icon: 'mdi-format-textbox',
        name: ' Text Content',
        code: 'TextContent'
      },
      {
        icon: 'mdi-video-outline',
        name: 'Video',
        code: 'VideoContent',
      },
      {
        icon: 'mdi-presentation',
        name: 'Slide Presentation',
        code: 'PresentationContent'
      },
      {
        icon: 'mdi-file-pdf-outline',
        name: 'PDF',
        code: 'PDFContent'
      },
      {
        icon: 'mdi-book-play-outline',
        name: 'SCORM  |  xAPI',
        code: 'ScormContent'
      },
      {
        icon: 'mdi-xml',
        name: 'Embed Code | iFrame',
        divider: true,
        code: 'EmbedContent'
      },
      {
        icon: 'mdi-clipboard-arrow-up-outline',
        name: ' AssignmentContent',
        code: 'AssignmentContent'
      },
      {
        icon: 'mdi-ballot-outline',
        name: 'Exam',
        code: 'ExamContent'
      },
    ]
  }
}

export default Content
