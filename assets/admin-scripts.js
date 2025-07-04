(function ($) {
    $(function() {

		// Copy the shortcode code on click of the <code> el
		var zts_code_els = document.querySelectorAll('.z-ts-faq code');

		zts_code_els.forEach((code_el) => code_el.addEventListener('click', () => {
			let code_shortcode = code_el.innerHTML;
			console.log(code_shortcode);
			zts_setClipboard(code_shortcode).then( zts_showCopiedMsg( code_shortcode, code_el ) );
		}));

		async function zts_setClipboard(text) {
            // htmlDecode | see: https://stackoverflow.com/questions/1912501/unescape-html-entities-in-javascript?page=1&tab=scoredesc#tab-top
            const temp_doc = new DOMParser().parseFromString(text, "text/html");
            const decodedText = temp_doc.documentElement.textContent;

			const type = "text/plain";
			const clipboardItemData = {
				[type]: decodedText,
			};
			const clipboardItem = new ClipboardItem(clipboardItemData);
			await navigator.clipboard.write([clipboardItem]);
		}
		async function zts_showCopiedMsg( text, el ) {
			console.log( text );
			console.log( el );

			const msgDiv = document.createElement( 'div' );
			msgDiv.classList.add('zts-msg');
			msgDiv.innerHTML = z_theme_switcher_admin.copiedText;
			el.after(msgDiv);
			setTimeout( function() {
				msgDiv.classList.add('fadeOut');
				setTimeout( function() {
					msgDiv.remove();
				}, 1500 );
			}, 3000 );
		}

    });
})(jQuery);