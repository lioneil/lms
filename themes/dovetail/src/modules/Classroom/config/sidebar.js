export default [
  {
    code: 'classrooms',
    name: 'classrooms',
    meta: {
      title: 'Classrooms',
      icon: 'mdi-google-classroom',
      authenticatable: true,
      description: 'Manage classrooms',
      sort: 5,
      permission: 'classrooms.index',
      children: [
        'classrooms.create',
        'classrooms.edit',
        'classrooms.show',
        'classrooms.index',
        'classrooms.trashed',
      ],
    },
    children: [
      {
        code: 'classrooms.index',
        name: 'classrooms.index',
        meta: {
          title: 'All Classrooms',
          authenticatable: true,
          description: 'View the list of all classrooms',
          sort: 5,
          permission: 'classrooms.index',
          children: [
            'classrooms.edit',
            'classrooms.show',
            'classrooms.index'
          ],
        },
      },
      {
        code: 'classrooms.create',
        name: 'classrooms.create',
        meta: {
          title: 'Add Classroom',
          authenticatable: true,
          description: 'Add a new classroom',
          sort: 6,
          permission: 'classrooms.create',
        },
      },
      {
        code: 'classrooms.trashed',
        name: 'classrooms.trashed',
        meta: {
          title: 'Trashed Classrooms',
          authenticatable: true,
          description: 'View list of all classrooms that has been moved to trash',
          sort: 5,
          permission: 'classrooms.trashed',
          children: ['classrooms.trashed'],
        },
      },
    ],
  }
]
