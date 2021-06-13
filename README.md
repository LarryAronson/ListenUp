# ListenUp

A plugin for speaking segments of post content using a browser's built-in, JavaScript SpeechSynthesis API.

_ListenUp_ provides shortcodes for adding voice (text to speech) to elements of a post or page.

Reference: https://wicg.github.io/speech-api/

Last updated: 2021-06-13


**Installation**

Install the plugin using the link at the very top of the Add New Plugin Dashboard panel and selecting the zip file, or upload the unzipped folder to your plugins directory using your favorite FTP program. Once uploaded, Activate the plugin to make it ready for use.


**Description**

Four shortcodes are available: 

_ListenUp_, will speak all the text content between its opening and closing tags using your browser's default voice. For example:

  [ListenUp]We hold these truths to be self-evident, 
  that all men are created equal.[/ListenUp]
  
_ListenUpExcerpt_, will fetch the post excerpt and speak it. If the post excerpt is empty, the beginning of the content will be read up to "more" tag. If no "more" tag is present, the first 55 words of the content will be read. The enclosed content provides the link to initiate speaking. When used without a closing tag, i.e.: [ListenUpExcerpt], it will provide a default link: _Hear the excerpt_. Example:

  [ListenUpExcerpt]Hear a Brief Summary[/ListenUpExcerpt]

_ListenUpImage_, will fetch and speak the description of the first image to be found in the content between its openning and closing tags.

  [ListenUpImage]&lt;img src="https://example.com/wp-content/uploads/example.jpg" /&gt;.[/ListenUpimage]
  
_ListenUpCustom_, will fetch the contents of a custom field and speak it. Like ListenUpExcerpt it may be used as a single standalone tag or as a container. For example:

  [ListenUpCustom name="bob"]Hear what Robert says.[/ListenUpCustom]
  
For all uses, while speaking is in progress, a subsequent click on the linked text will cancel the reading and reset the VoiceSynthesis API. In all cases if there's nothing left to speak after filtering the content for control characters and html elements, the plugin will exit without altering the post content.


**Attributes**

A number attributes can be added to each shortcode to control its operation and style the speaking voice. The following attributes are recognized by the voice synthesis engine and are valid for both shortcodes.

**Voice Attributes:**
_speed_	    The speaking rate. The value should be a floating point number between 0.1 and 10.0 indicating a multiple of the normal rate = 1.0. Additionally, the speed can be specified as either fast (equal to 1.67) or slow (0.67).

_pitch_	    The vocal pitch. The value should be a floating point number greater than 0.0 and less than or equal to 2.0, with 1.0 being the default. Additionally the pitch can be specified as high (1.75) or low (0.25).

_voice_	    A voice name that's supported by a browser (see notes). Additionally the value can be specified as male or female. Do not enclose this attribute value in quote marks as this can run afoul of WordPress's character substitution filters.

_lang_	    A 2-character code for a language that's supported by a browser (see notes). Do not enclose the value in quote marks.


**Usage Attributes:**
_id_	      (ListenUpExcerpt and ListenUpCustom) 
By default, the current post's excerpt is read. If this parameter is set to an integer, the excerpt of a post with that id will be read instead.
  
_link_	    (ListenUpExcerpt and ListenUpCustom) The text for the link when the shortcode is used without a closing tag. Default is: "Hear the excerpt" for ListenUpExcerpt and the custom field name for ListenUpCustom.

_name_	    (ListenUpCustom only) The key for the custom field attached to the current post or the post referenced by the id attribute. If there are multiple values to the custom field, only the first instance will be fetched and spoken.

_class_	    Specifies the CSS class for styling the content. for ListenUp, the default class is ListenUp-content. For ListenUpExcerpt it's ListenUp-excerpt and for ListenUpCustom it's ListenUp-custom. An alternate class, ListenUp-button can be invoked by coding: class="button". This styles the excerpt or custom field content link to look like a button. Setting the class attribute to a non-existent class, e.g. "null", will allow the theme's styles to dominate.

_intro_	    Text to be read before other content separated from it by a brief pause. This provides a means for saying something when there's no visible content to speak. For example, to provide a spoken description when the shortcode encloses an image tag.

_title_	Tooltip text to appear when the cursor is over the link or content (hover state.) The default is "Click to Listen. Click again to Cancel."


**Examples:**

_ListenUp_
    [ListenUp intro="Introduction"]some post content[/ListenUp]
    [ListenUp title="Hear what she said."]some post content[/ListenUp]
    [ListenUp voice=female class="my-vocal-class"]some post content[/ListenUp]
    [ListenUp voice=Amalie lang=fr]Parlez vous Français?[/ListenUp]
    
_ListenUpExcerpt_
    [ListenUpExcerpt speed=fast]
    [ListenUpExcerpt voice=female pitch="0.50"]
    [ListenUpExcerpt class="button"]Hear Summary[/ListenUpExcerpt]
    [ListenUpExcerpt class="button" link="Hear Summary"] (same as above)

_ListenUpImage_
    [ListenUpImage title="Hear Image Description"]<img src="..." />[/ListenUpImage]
_
ListenUpCustom_
    [ListenUpCustom name="contributors" link="Acknowledgments"]
    [ListenUpCustom name="lincoln-quote"]Gettysburg Address[/ListenUpCustom]


**Usage Notes:**

**Files:**

This plugin distribution consists of this README.html file plus three other files:

  ListenUp.php -- The main plugin file.
  ListenUp.css -- The stylesheet containing the default classes.
  sayText.js -- A JavaScript function for accessing the speechSynthesis API.

The files are rather short—less than 300 lines of code not counting this README file.

ListenUp is safe to use. No changes are made to your WordPress database and no options are saved. The plugin's functions are not loaded on Dashboard panels.

The styles in ListenUp.css will be loaded into frontend pages and posts. Additional style classes may be added to this file in order to keep them separate from your theme's stylesheet.

sayText.js contains a single JavaScript function: lup_sayText(what, how). When the plugin is active, this function is loaded into the footer of all frontend pages and posts.

lup_sayText may be invoked anywhere you want to convert text to speech. The first argument to the function is the text to be spoken. Quote marks in the text should be properly escaped and control characters should be removed. The second argument is an array of voice attributes. Here's an example of how you might use it:

  &lt;p onmouseover="lup_sayText('1, 2, 3', {'voice':'Alice','lang':'it'});"&gt;Italian: "1, 2, 3"&lt;p&gt;

**Caveats**

This is experimental software. Support for speech synthesis is not universal. Browsers differ in their implementation of the JavaScript APIs used for speech recognition and synthesis. I've tested this using recent versions of browsers on MacOS, with Chrome on an Android smartphone and Safari on an iPad. It works more or less on all these platforms. If speech synthesis is not available on a user's browser, the plugin will issue an alert message when the shortcode content is clicked.

Speech synthesis relies on an external speech conversion service that's built into users' browsers so an internet connection must be available in order for this to work. JavaScript must be enabled in the user's browser but, then, WordPress won't work without it.

Avoid using the plugin with a very long content segment. I believe the API limit is 32k characters.

The plugin works by enclosing the content or link text in a span (<span>) element with an onclick handler. The default style classes set the display property of this span element to "inline-block." You should take this into account when specifying your own class attributes.

You can use these shortcodes multiple times within a post. However, you should having standalone tags appearing before containers of the same tag type since WordPress makes the assumption that a shortcode is a container until it fails to find a closing tag.

Note also that the cancel action is global. That is, when speaking is in progress, clicking on any ListenUp or ListenUpExcerpt element will cancel speaking, not just the element previously clicked on. The user needs to click once again to start speaking another ListenUp element.

Links within the content enclosed by ListenUp tags will function normally when clicked, however the plugin will also start speaking the content containing the link and will continue speaking on the target and subsequent web pages until finished. Users will need to return to a page using the plugin to cancel the speaking or else wait it out.

ListenUpImage depends on WordPress supplying the attachment id as a class name on the image tag, e.g: "wp-image-9999". If your version of WordPress does not do this, you can supply the attachment id via the shortcode's "id" attribute. If the attachment record's description field is empty, the content of the record's "alt" filed is use. This may not be the same content as appears in the image tag's "alt" attribute as that can be edited when the image in inserted in the post.

Language (lang) and voice are a bit tricky. Chrome and Opera only recognize lang and setting this will force a matching voice for the more common languages. Safari and Firefox, otoh, only recognize voice and changing that will force a matching language setting. The voice synthesis API uses an AI algorithm to determine how to pronounce common abbreviations, numbers and other text items that do not have a direct phonetic rendering in the target language. For this reason, you should spell out dates instead of using a numeric format, i.e.: 1/8/2021.
To make matters worse, iOS/Safari supports both voice and language but has a different set of voices than desktop browsers.

Given these constraints, it was necessary to fudge the settings in order to force a woman's voice when the attribute is set to female. I chose the "Samantha" voice and set the language attribute to "en". Samantha has a slight Irish accent but she pronounces "Z" as "Zee" and not "Zed". in some browsers, the voice and language settings won't hold and clicking a ListUp link a second time may not produce the same result.

If you wish to set the voice or language to something other than the default or the generic "male" or "female" settings, you should set both in order for it to take effect on the most browsers. Use my voice synthesis playground at https://larryaronson.com/voice.html to test out various combinations on different platforms.

Larry Aronson
<larry@aronson.nyc>
