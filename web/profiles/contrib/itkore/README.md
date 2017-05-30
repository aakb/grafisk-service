#Setup guidelines

##About
ITKore represents a drupal 8 platform from which new drupal 8 sites may be built. This way some of the trivial tasks are bypassed to start the project a little further ahead.

ITKore profile is a drupal install profile which holds custom modules and depends on some contrib modules. The profile also alters some default core configuration.

##Modules

###ITKore
ITKore contains the following modules

* ITKore admin
   * Provides an admin interface for site specific config settings
   * Provides two submodules for adding subpages to config settings page
      1. Itkore footer (Footer settings)
      2. Itkore frontpage header (Frontpage header settings)
* ITKore content types
   * Provides four content types
      1. News
      2. Page
      3. Overview page
* ITKore fields
   * Holds field definitions
* ITKore user roles
   * Sets up user roles and default permissions
* ITKore user theme
   * Sets up login form to use admin theme

###Other
To support the build som basic drupal contrib and custom ITK modules are added.

* ITK cookie message
   * Provides cookie warning
* ITK paragraph
   * Provides default paragraph types commonly used by ITK
* ITK admin links
   * Provides admin links as a block
* ITK Hamburger menu
   * Provides a default hamburger menu

* Ctools
* Entity reference revisions
* Field group
* Imce
* Metatag
* Paragraphs
* Pathauto
* Paragraphs
* Redirect
* Role delegation
* Token
* Toolbar visibility
* Youtube field
