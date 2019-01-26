(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
    $(window).load(function () {
        let allCB = document.getElementsByClassName("jzs-settings-checkbox");
        for (let i = 0; i < allCB.length; i++) {
            let cb = allCB[i];
            cb.addEventListener("change", () => {
                if (cb.checked) {
                    $(cb).closest(".postbox").find(".inside").removeClass("hidden");
                } else {
                    $(cb).closest(".postbox").find(".inside").addClass("hidden");
                }
            });
        }
        
        let allSelectDOM = document.getElementsByTagName("select");
        for (let i = 0; i < allSelectDOM.length; i++) {
            let slct = allSelectDOM[i];
            slct.addEventListener("change", () => {
                document.getElementById(slct.getAttribute("name") + "preview").setAttribute("src", slct.value);
            });
        }
    });
})( jQuery );
