export default [
  {
    path: '/admin/discussions',
    name: 'admin.threads',
    redirect: {name: 'threads.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Discussions',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/discussions',
        name: 'threads.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Discussions',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'trashed',
        name: 'threads.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Discussions',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ]
  }
]
