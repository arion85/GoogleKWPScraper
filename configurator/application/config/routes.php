<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['default_controller'] = 'user/index';
$route['404_override'] = '';

/*admin*/
$route['admin'] = 'user/index';
$route['admin/signup'] = 'user/signup';
$route['admin/create_member'] = 'user/create_member';
$route['admin/login'] = 'user/index';
$route['admin/logout'] = 'user/logout';
$route['admin/login/validate_credentials'] = 'user/validate_credentials';

$route['admin/main'] = 'admin_main/index';
$route['admin/main/add'] = 'admin_main/add';

$route['admin/accounts'] = 'admin_accounts/index';
$route['admin/accounts/add'] = 'admin_accounts/add';
$route['admin/accounts/update'] = 'admin_accounts/update';
$route['admin/accounts/update/(:any)'] = 'admin_accounts/update/$1';
$route['admin/accounts/delete/(:any)'] = 'admin_accounts/delete/$1';
$route['admin/accounts/(:any)'] = 'admin_accounts/index/$1'; //$1 = page number

$route['admin/countries'] = 'admin_countries/index';
$route['admin/countries/add'] = 'admin_countries/add';
$route['admin/countries/update'] = 'admin_countries/update';
$route['admin/countries/update/(:any)'] = 'admin_countries/update/$1';
$route['admin/countries/delete/(:any)'] = 'admin_countries/delete/$1';
$route['admin/countries/(:any)'] = 'admin_countries/index/$1'; //$1 = page number

$route['admin/proxys'] = 'admin_proxys/index';
$route['admin/proxys/add'] = 'admin_proxys/add';
$route['admin/proxys/reload'] = 'admin_proxys/reload';
$route['admin/proxys/update'] = 'admin_proxys/update';
$route['admin/proxys/update/(:any)'] = 'admin_proxys/update/$1';
$route['admin/proxys/delete/(:any)'] = 'admin_proxys/delete/$1';
$route['admin/proxys/(:any)'] = 'admin_proxys/index/$1'; //$1 = page number

$route['admin/keys'] = 'admin_keys/index';
$route['admin/keys/add'] = 'admin_keys/add';
$route['admin/keys/update'] = 'admin_keys/update';
$route['admin/keys/update/(:any)'] = 'admin_keys/update/$1';
$route['admin/keys/keywords/(:any)'] = 'admin_keys/keywords/$1';
$route['admin/keys/keywords/(:any)/(:any)'] = 'admin_keys/keywords/$1/$2';
$route['admin/keys/delete/(:any)'] = 'admin_keys/delete/$1';
$route['admin/keys/(:any)'] = 'admin_keys/index/$1'; //$1 = page number


/* End of file routes.php */
/* Location: ./application/config/routes.php */