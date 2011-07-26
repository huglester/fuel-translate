**NOT READY FOR USE** -> **CURRENTLY IN DEVELOPMENT**

Fuel-Translate
==============

This is a fuel (http://fuelphp.com) task that can be ran with the ```oil refine``` utility that will generate translations of any properly formatted lang files to and from any of the supported languages.

Once ran the translated Lang files are written in the proper location to be utilized immediately, and will overwrite any existing files of the target language.

This Task fully supports the fuel lang structure, including multidimensional arrays and retains original keywords (:keyword).

We are Currently utilizing the Bing API Translation Services.

Usage
=====

syntax:

```oil refine translate to from path```

 * **to** - (required) - target language (see list of available language codes below)
 * **from** - (default = 'en') - original language to use
 * **path** - (default = 'APPPATH/lang') - alternate directory of lang root 

Examples:

 * ```oil refine translate es``` - Translates all lang files from English to Spanish
 * ```oil refine translate sk es``` - Translates all lang files from Spanish to Slovak


Languages Currently Supported
=============================

 * ar - Arabic
 * bg - Bulgarian
 * ca - Catalan
 * ?? - Chinese Simiplified
 * zh - Chinese Traditional
 * cs - Czech
 * da - Danish
 * nl - Dutch
 * en - English
 * et - Estonian
 * fi - Finnish
 * fr - French
 * de - German
 * el - Greek
 * ht - Haitian Creole
 * he - Hebrew
 * hu - Hungarian
 * id - Indonesian
 * it - Italian
 * ja - Japanese
 * ko - Korean
 * lv - Latvian
 * lt - Lithuanian
 * no - Norwegian
 * po - Polish
 * pt - Portuguese
 * ro - Romanian
 * ru - Russian
 * sk - Slovak
 * sl - Slovenian
 * es - Spanish
 * sv - Swedish
 * th - Thai
 * tr - Turkish
 * uk - Ukrainian
 * vi - Vietnamese