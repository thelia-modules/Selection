#Selection module changelog

###3.1.3
- Back office: the product and content lists of a selection, and the products of a category or the contents of a folder, no longer answer a 500 when the administrator has not visited the shop in the session (edition language instead of the front language)
- Back office: product search field on the selection edit screen, on the title in the edition language or the reference, leaving out the products already in the selection; products without a title in that language come last. Titles of these lists and of the search are read in the edition language of the back office: an administrator whose language has no product translation sees empty titles
- Back office: the read actions of these lists and of the search require the VIEW access on the module
- Back office: long titles wrap in the product and content tables of a selection, which no longer scroll sideways
- Back office: the screens are translated again (catalogue moved to `I18n/backOffice/default-twig`, domain `selection.bo.default-twig`, since the Smarty templates are gone)
- The `Tests` directory is not loaded as services

###3.0.3
- Send the CSRF token in the request body

###1.1.9
- Fixed title and language issues when editing a selection

###1.1.8
- Invisible products are not displayed anymore when editing a selection

###1.1.7
- Fixed rewritten urls not marked as obsolete on deletion

###1.1.6
 - Fix products position update

###1.1.4
 - #13 added missing update folder

###1.1.3
- #12 main loop wasn't returning meta infos

###1.1.2
 - Fixed a duplication bug when displaying the selections in the back office

###1.1.1
 - Added a 'SELECTION_URL' variable returned by the selection_list loop

##1.1

- Cleaned a bit the code and added a few comments
- The 'title' parameter in the selection_list loop now actually works
- (#2) Added an exclude parameter to the selection_list loop
- (#3) Explained how to get the correct rewritten URL in the README
- (#4 & #7) The selection description can now be correctly called and displayed from the selection_list loop
- (#4) The selection chapo can now be correctly called and displayed from the selection_list loop
- (#4) The selection postscriptum can now be correctly called and displayed from the selection_list loop
- (#6) Added arrow buttons surrounding the products when editing a selection to change their position
- (#6) You can now change the position of a product in the list by clicking on it then choosing a new position.
- (#9) Rewrote the Readme with a better description and in a better english

##1.0
- Initial release



##Contributors

- Maxime Bruchet  <mbruchet@openstudio.fr>
- Luc Normandon   <lucnormandon@openstudio.fr>
