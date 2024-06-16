const routes = [];
const requireSidebar = require.context(
  // The relative path of the routes folder
  '@/modules',
  // Whether or not to look in subfolders
  true,
  // The regular expression used to match base route filenames
  /config\/sidebar\.js$/,
);

requireSidebar.keys().forEach(route => {
  const routeConfig = requireSidebar(route);
  routeConfig.default.forEach(route => {
    routes.push(route);
  });
});

export default routes.sort((a, b) => a.meta.sort - b.meta.sort);
