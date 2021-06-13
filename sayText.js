/** 
 * A JavaScript function to convert text to speech
 * Part of the "Read The Excerpt" WordPress plugin
 * copyright (c)2021, Larry Aronson
 * Version: 0.9.5; Last updated: 2021-01-14
 *
 * Parameters:  
 *		what ==> The text to be converted. Quote marks and other special charcters should be escaped.
 *		how  ==> An array of attributes: speed, pitch voice and lang.
 **/

function lup_sayText(what, how) {
	if (speechSynthesis.speaking) {
		speechSynthesis.cancel(); 							// stop any speaking in progress
	}
	else {
		var Voices;
		var saytext = new SpeechSynthesisUtterance(what);	// create utterance object
		if (typeof saytext === 'object') {					// did that work?
			if (typeof how === 'object') {					// check for attributes
				if ((typeof how['speed'] === 'number') && 	// rate of speech
					(how['speed'] >= 0.1) &&
					(how['speed'] < 10.0)) saytext.rate = how['speed'];
				else saytext.rate = 1.0;

				if ((typeof how['pitch'] === 'number') && 	// voice pitch
					(how['pitch'] > 0.0) &&
					(how['pitch'] <= 2.0)) saytext.pitch = how['pitch'];
				else saytext.pitch = 1.0;

				if (typeof how['lang'] === 'string') saytext.lang = how['lang'];
				else saytext.lang = null;

				if (typeof how['voice'] === 'string') {		// check for voice setting
					var Voices = localStorage.getItem('Voices');	// 
					if (!Voices) {
						Voices = speechSynthesis.getVoices(); 
						localStorage.setItem('Voices', Voices);
					}
					
					saytext.voice = null; 					// initial voice to default
					for (ix = 0; ix < Voices.length; ix++) {
						if (Voices[ix].name === how['voice']) {
							saytext.voice = Voices[ix]; 
							break;
						}
					}
				}
			}
			speechSynthesis.speak(saytext);						// All set, speak now or...
		}
 		else alert('Speech synthesis is not supported by your browser/OS.'); 
 	}
}