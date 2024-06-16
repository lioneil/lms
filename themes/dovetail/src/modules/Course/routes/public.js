export default [
  {
    path: '/courses',
    component: () => import('@/components/Layouts/Public.vue'),
    meta: {
      title: 'Courses',
      sort: 6,
      authenticatable: true,
      icon: 'mdi-book-multiple-variant',
    },
    children: [
      {
        path: '',
        name: 'courses.all',
        component: () => import('../All.vue'),
        meta: {
          title: 'All Courses',
          sort: 9,
          authenticatable: true,
        }
      },
      {
        path: ':courseslug/lessons/:contentslug',
        name: 'courses.lesson',
        component: () => import('../Single.vue'),
        meta: {
          title: ':slug',
          sort: 9,
          authenticatable: true,
        }
      },
      {
        path: ':courseslug',
        name: 'courses.overview',
        component: () => import('../Overview.vue'),
        meta: {
          title: ':slug',
          sort: 10,
          authenticatable: true,
        },
      },
    ],
  }
]
