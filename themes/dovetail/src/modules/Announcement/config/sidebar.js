export default [
  {
    code: 'announcements',
    name: 'announcements',
    meta: {
      title: 'Announcements',
      icon: 'mdi-bullhorn-outline',
      authenticatable: true,
      description: 'Manage announcements',
      sort: 5,
      permission: 'announcements.index',
      children: [
        'announcements.index',
        'announcements.create',
        'announcements.show',
        'announcements.edit',
        'announcements.trashed',
        'announcements.categories.edit',
        'announcements.categories.index'
      ],
    },
    children: [
      {
        code: 'announcements.index',
        name: 'announcements.index',
        meta: {
          title: 'All Announcements',
          authenticatable: true,
          description: 'Manage list of all announcements',
          sort: 5,
          permission: 'announcements.index',
          children: ['announcements.index', 'announcements.edit', 'announcements.show'],
        },
      },
      {
        code: 'announcements.create',
        name: 'announcements.create',
        meta: {
          title: 'Add Announcement',
          authenticatable: true,
          description: 'Add a new announcement',
          sort: 6,
          permission: 'announcements.create',
        },
      },
      {
        code: 'announcements.trashed',
        name: 'announcements.trashed',
        meta: {
          title: 'Trashed Courses',
          authenticatable: true,
          description: 'View list of announcements that has been moved to trash',
          sort: 5,
          permission: 'announcements.trashed',
          children: ['announcements.trashed'],
        },
      },

      // Category
      {
        code: 'categories.index',
        name: 'announcements.categories.index',
        meta: {
          title: 'Categories',
          icon: 'mdi-tag-outline',
          authenticatable: true,
          description: 'Manage list of all announcement categories',
          sort: 5,
          permission: 'announcements.index',
          children: ['announcements.categories.index', 'announcements.categories.edit'],
        },
      },
    ],
  }
]
