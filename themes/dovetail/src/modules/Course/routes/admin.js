export default [
  {
    path: '/admin/courses',
    name: 'admin.courses',
    redirect: {name: 'courses.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Courses',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/courses',
        name: 'courses.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Courses',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'create',
        props: true,
        name: 'courses.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add Course',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'trashed',
        name: 'courses.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Courses',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/edit',
        name: 'courses.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },

      // Content
      {
        path: ':id/contents',
        name: 'contents.index',
        component: () => import('../submodules/Content/Index.vue'),
        meta: {
          title: 'Manage Contents',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/contents/create',
        name: 'contents.create',
        component: () => import('../submodules/Content/Create.vue'),
        meta: {
          title: ':slug',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/contents/:content/edit',
        name: 'contents.edit',
        component: () => import('../submodules/Content/Edit.vue'),
        meta: {
          title: ':slug',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },

      // Category
      {
        path: 'categories',
        name: 'categories.index',
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
        name: 'categories.edit',
        component: () => import('../submodules/Category/Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },

      // Material
      {
        path: 'materials',
        name: 'materials.index',
        component: () => import('../submodules/Material/Index.vue'),
        meta: {
          title: 'Materials',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'materials/:id/edit',
        name: 'materials.edit',
        component: () => import('../submodules/Material/Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
    ],
  }
]
