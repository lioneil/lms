export default [
  {
    path: '/admin/permissions',
    name: 'admin.permissions',
    redirect: {name: 'permissions.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Permissions',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/users/permissions',
        name: 'permissions.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Permissions',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
    ],
  }
]
