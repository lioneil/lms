export default [
  {
    code: 'threads',
    name: 'threads',
    meta: {
      title: 'Discussions',
      icon: 'mdi-chat-outline',
      authenticatable: true,
      description: 'Manage discussions',
      sort: 5,
      permission: 'threads.index',
      children: [
        'threads.index',
        'threads.create',
        'threads.edit',
        'threads.trashed',
      ],
    },
    children: [
      {
        code: 'threads.index',
        name: 'threads.index',
        meta: {
          title: 'All Discussions',
          authenticatable: true,
          description: 'View the list of all discussions',
          sort: 5,
          permission: 'threads.index',
          children: [
            'threads.edit',
            'threads.index'
          ],
        },
      },
      {
        code: 'threads.create',
        name: 'threads.create',
        meta: {
          title: 'Add Discussion',
          authenticatable: true,
          description: 'Add a new discussion',
          sort: 6,
          permission: 'threads.create',
        },
      },
      {
        code: 'threads.trashed',
        name: 'threads.trashed',
        meta: {
          title: 'Trashed Discussions',
          authenticatable: true,
          description: 'View list of all discussions that has been moved to trash',
          sort: 5,
          permission: 'threads.trashed',
          children: ['threads.trashed'],
        },
      },
    ],
  }
]
