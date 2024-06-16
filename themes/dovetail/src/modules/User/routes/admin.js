export default [
  {
    path: '/admin/users',
    name: 'admin.users',
    redirect: {name: 'users.index'},
    component: () => import('@/App.vue'),
    meta: {
      title: 'Users',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '/admin/users',
        name: 'users.index',
        component: () => import('../Index.vue'),
        meta: {
          title: 'All Users',
          sort: 6,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'create',
        props: true,
        name: 'users.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add User',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: 'trashed',
        name: 'users.trashed',
        component: () => import('../Trashed.vue'),
        meta: {
          title: 'Deactivated Users',
          sort: 8,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/edit',
        name: 'users.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
      {
        path: ':id',
        name: 'users.show',
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
