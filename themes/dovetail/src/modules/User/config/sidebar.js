export default [
  {
    code: 'users',
    name: 'users',
    meta: {
      title: 'Users',
      icon: 'mdi-account-multiple-outline',
      authenticatable: true,
      description: 'Manage users',
      sort: 5,
      permission: 'users.index',
      children: ['users.index', 'users.create', 'users.edit', 'users.show', 'users.trashed', 'permissions.index', 'roles.index', 'roles.create', 'roles.edit', 'roles.show', 'roles.trashed'],
    },
    children: [
      {
        code: 'users.index',
        name: 'users.index',
        meta: {
          title: 'All Users',
          authenticatable: true,
          description: 'View the list of all users',
          sort: 5,
          permission: 'users.index',
          children: ['users.index', 'users.edit', 'users.show'],
        },
      },
      {
        code: 'users.create',
        name: 'users.create',
        meta: {
          title: 'Add User',
          authenticatable: true,
          description: 'Add a new user',
          sort: 6,
          permission: 'users.create',
        },
      },
      {
        code: 'users.trashed',
        name: 'users.trashed',
        meta: {
          title: 'Deactivated Users',
          authenticatable: true,
          description: 'View the list of all users that has been moved to trash',
          sort: 6,
          permission: 'users.trashed',
          children: ['users.trashed'],
        },
      },
      // Role
      {
        code: 'roles.index',
        name: 'roles.index',
        meta: {
          title: 'Roles',
          authenticatable: true,
          description: 'Manage list of all user roles',
          icon: 'mdi-shield-account-outline',
          sort: 6,
          permission: 'roles.index',
          children: ['roles.index', 'roles.create', 'roles.edit', 'roles.show', 'roles.trashed'],
        }
      },
      // Permissions
      {
        code: 'permissions.index',
        name: 'permissions.index',
        meta: {
          title: 'Permissions',
          authenticatable: true,
          description: 'Display list of all role permissions',
          icon: 'mdi-shield-lock',
          sort: 6,
          permission: 'permissions.index',
          children: ['permissions.index'],
        }
      },
    ],
  }
]
