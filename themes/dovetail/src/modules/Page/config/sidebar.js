export default [
  {
    code: 'pages',
    name: 'pages',
    meta: {
      title: 'Pages',
      icon: 'mdi-file-document-outline',
      authenticatable: true,
      description: 'Manage pages',
      sort: 5,
      permission: 'pages.index',
      children: [
        'pages.index',
        'pages.show',
        'pages.create',
        'pages.edit',
        'pages.trashed',
        'pages.categories.edit',
        'pages.categories.index',
      ],
    },
    children: [
      {
        code: 'pages.index',
        name: 'pages.index',
        meta: {
          title: 'All Pages',
          authenticatable: true,
          description: 'Manage list of all pages',
          sort: 5,
          permission: 'pages.index',
          children: ['pages.index', 'pages.edit', 'pages.show',],
        },
      },
      {
        code: 'pages.create',
        name: 'pages.create',
        meta: {
          title: 'Add Page',
          authenticatable: true,
          description: 'Add a new page',
          sort: 5,
          permission: 'pages.create',
          children: ['pages.create'],
        },
      },
      {
        code: 'pages.trashed',
        name: 'pages.trashed',
        meta: {
          title: 'Trashed Pages',
          authenticatable: true,
          description: 'View list of pages that has been moved to trash',
          sort: 5,
          permission: 'pages.trashed',
          children: ['pages.trashed'],
        },
      },
      // Category
      {
        code: 'categories.index',
        name: 'pages.categories.index',
        meta: {
          title: 'Categories',
          icon: 'mdi-tag-outline',
          authenticatable: true,
          description: 'Manage list of all pages categories',
          sort: 5,
          permission: 'categories.index',
          children: ['pages.categories.index', 'pages.categories.edit'],
        },
      },
    ],
  }
]
