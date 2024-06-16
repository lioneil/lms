export default [
  {
    path: '/admin/widgets',
    name: 'admin.widgets',
    redirect: {name: 'widgets.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Settings',
      sort: 600,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/widgets',
        name: 'widgets.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'Widgets',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/edit',
        name: 'widgets.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ]
  }
]
