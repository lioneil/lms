export default [
  {
    name: 'dashboard',
    meta: {
      title: 'Dashboard',
      sort: 0,
      authenticatable: true,
      description: 'View app overview and summary',
      icon: 'mdi-view-dashboard-outline',
      permission: false,
    },
  },

  {
    name: 'divider:dashboard/index',
    meta: {
      title: 'Content',
      sort: 1,
      subheader: true,
      height: 2,
    },
  },
];
