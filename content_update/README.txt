CONTENT UPDATE MODULE
---------------------
This is an example module which performs CRUD on a Drupal content type 'course'.
It listens for '403's and redirects to the home page. 
It also dispatches the 'content_update.courses_updated' event.
It has been tested with Drupal 8.4.


INSTALLATION
------------
Unzip the folder to the modules folder then go to admin/modules to install in the usual way.
It adds the 'course' content type with 4 fields.


UNINSTALLING
------------
Uninstall as normal via admin/modules/uninstall BUT you will have to manually delete any
 'course' content first.

USAGE
-----
Go the the path 'courses/update' to create some 'course' nodes. 


PROBLEMS OR COMMENTS?
---------------------
Contact me at mikantchap@hotmail.com