export default [
  {
    path: '/pages',
    component: () => import('@/components/Layouts/Public.vue'),
    meta: {
      title: 'pages',
      sort: 1,
    },
    children: [
      // pages Show
      {
        path: ':id',
        name: 'pages.single',
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
