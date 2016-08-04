/*
    Contains all global script settings, constants, variables and common methods
*/

export var settings = {
  SESSION_KEY: 'sdjfhk_session',

  debug: 0,

  voidLink: 'javascript:void(0);',
  voidHref: 'href=\'javascript:void(0)\'',

  imagesPath: '/img/',
  servicesPath: '/services/',
  userPhotosPath: '/img/photos/',
  avatarsPath: '/img/avatars/',
  skinsPreviewPath: '/img/journals/',
  openIdPath: '/img/openid/',

  adminRights: 75,
  keeperRights: 20,
  topicRights: 10,

  loadingIndicator: '<div class=\'LoadingIndicator\'></div>',

  severityCss: ['Warning', 'Error'],

  replaceTagsExpr: new RegExp('\<[\/a-z][^\>]*\>', 'gim'),

  forumAccess: {
    NO_ACCESS: 0,
    READ_ONLY_ACCESS: 1,
    FRIENDLY_ACCESS: 2,
    READ_ADD_ACCESS: 3,
    FULL_ACCESS: 4
  },

  forumAccessName: [
    'доступ закрыт', 
    'только чтение', 
    'дружественный доступ', 
    'чтение/запись', 
    'полный доступ'
  ]
};
