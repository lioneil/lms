export default [
  {
    code: 'assessments',
    name: 'assessments',
    meta: {
      title: 'Assessments',
      icon: 'mdi-ballot-outline',
      authenticatable: true,
      description: 'Manage assessments',
      sort: 5,
      permission: 'assessments.index',
      children: [
        'assessments.create',
        'assessments.edit',
        'assessments.show',
        'assessments.index',
        'assessments.trashed',
        'fields.index',
        'fields.create',
        'fields.edit',
      ],
    },
    children: [
      {
        code: 'assessments.index',
        name: 'assessments.index',
        meta: {
          title: 'All Assessments',
          authenticatable: true,
          description: 'View the list of all assessments',
          sort: 5,
          permission: 'assessments.index',
          children: [
            'assessments.edit',
            'assessments.show',
            'assessments.index',
            'fields.index',
            'fields.create',
            'fields.edit',
          ],
        },
      },
      {
        code: 'assessments.create',
        name: 'assessments.create',
        meta: {
          title: 'Add Assessment',
          authenticatable: true,
          description: 'Add a new assessment',
          sort: 6,
          permission: 'assessments.create',
        },
      },
      {
        code: 'assessments.trashed',
        name: 'assessments.trashed',
        meta: {
          title: 'Trashed Assessments',
          authenticatable: true,
          description: 'View list of all assessments that has been moved to trash',
          sort: 5,
          permission: 'assessments.trashed',
          children: ['assessments.trashed'],
        },
      },
    ],
  }
]
