export default [
  {
    path: '/discussions',
    component: () => import('@/components/Layouts/Public.vue'),
    meta: {
      title: 'Discussions',
      sort: 1,
    },
    children: [
      {
        path: '/discussions',
        name: 'threads.all',
        component: () => import('../All.vue'),
        meta: {
          title: 'Discussions',
        },
      },
      {
        path: 'create',
        name: 'threads.create',
        component: () => import('../Create.vue'),
        meta: {
          title: 'Add Discussion',
          sort: 7,
          authenticatable: true,
          icon: 'mdi-book-multiple-variant',
        },
      },
      {
        path: ':id/edit',
        name: 'threads.edit',
        component: () => import('../Edit.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
      // Discussions Show
      {
        path: ':id',
        name: 'threads.show',
        component: () => import('../Single.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        },
      },
    ],
  }
]
