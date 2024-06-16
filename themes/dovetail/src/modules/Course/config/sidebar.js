export default [
  {
    code: 'courses',
    name: 'courses',
    meta: {
      title: 'Courses',
      icon: 'mdi-book-open-outline',
      authenticatable: true,
      description: 'Manage courses',
      sort: 5,
      permission: 'courses.index',
      children: [
        'categories.edit',
        'categories.index',
        'contents.create',
        'contents.edit',
        'contents.index',
        'courses.create',
        'courses.edit',
        'courses.index',
        'courses.trashed',
        'materials.edit',
        'materials.index'
      ],
    },
    children: [
      {
        code: 'courses.index',
        name: 'courses.index',
        meta: {
          title: 'All Courses',
          authenticatable: true,
          description: 'Manage list of all courses',
          sort: 5,
          permission: 'courses.index',
          children: [
            'contents.create',
            'contents.edit',
            'contents.index',
            'courses.edit',
            'courses.index'
          ],
        },
      },
      {
        code: 'courses.create',
        name: 'courses.create',
        meta: {
          title: 'Add Course',
          authenticatable: true,
          description: 'Add a new course',
          sort: 6,
          permission: 'courses.create',
        },
      },
      {
        code: 'courses.trashed',
        name: 'courses.trashed',
        meta: {
          title: 'Trashed Courses',
          authenticatable: true,
          description: 'View list of courses that has been moved to trash',
          sort: 5,
          permission: 'courses.trashed',
          children: ['courses.trashed'],
        },
      },

      // Category
      {
        code: 'categories.index',
        name: 'categories.index',
        meta: {
          title: 'Categories',
          icon: 'mdi-tag-outline',
          authenticatable: true,
          description: 'Manage list of all course categories',
          sort: 5,
          permission: 'courses.index',
          children: ['categories.index', 'categories.edit'],
        },
      },

      // Material
      {
        code: 'materials.index',
        name: 'materials.index',
        meta: {
          title: 'Materials',
          icon: 'mdi-folder-table-outline',
          authenticatable: true,
          description: 'Manage list of all course materials',
          sort: 5,
          permission: 'courses.index',
          children: ['materials.index', 'materials.edit'],
        },
      },
    ],
  }
]
