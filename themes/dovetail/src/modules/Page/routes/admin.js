export default [
  {
    path: '/admin/pages',
    name: 'admin.pages',
    redirect: {name: 'pages.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Pages',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/pages',
        name: 'pages.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Pages',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'create',
        name: 'pages.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Create Page',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'trashed',
        name: 'pages.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Pages',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/edit',
        name: 'pages.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },

      // Category
      {
        path: 'categories',
        name: 'pages.categories.index',
        component: () => import('../submodules/Category/Index.vue'),
        meta: {
          title: 'Categories',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'categories/:id/edit',
        name: 'pages.categories.edit',
        component: () => import('../submodules/Category/Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
      {
        path: ':id',
        name: 'pages.show',
        component: () => import('../Show.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ],
  }
]
