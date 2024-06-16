export default [
  {
    path: '/admin/users/roles',
    name: 'admin.roles',
    redirect: {name: 'roles.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Roles',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/users/roles',
        name: 'roles.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Roles',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'create',
        props: true,
        name: 'roles.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add Role',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'trashed',
        name: 'roles.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Trashed Roles',
          sort: 8,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'edit/:id',
        name: 'roles.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
      {
        path: ':id',
        name: 'roles.show',
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
