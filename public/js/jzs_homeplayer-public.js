(function ($) {
    'use strict';
    $(window).load(function () {
        console.log("jzs-plugin.js loaded");

        let updateHeaderProductStatus = (videoEl) => {
            if (document.getElementById("jzs-product-status") && document.getElementsByClassName("jzs-video jzs-playing").length > 0) {
                let status = videoEl.getAttribute("data-stockStatus") == "offline" ? "false" : "true";
                document.getElementById("jzs-product-status").setAttribute("data-islive", status);
            } else {
                console.error("Il manque l'entête et le code du player");
            }
        }

        let transitionEndEventName = () => {
            let i, el = document.createElement('div'),
                transitions = {
                    'transition': 'transitionend',
                    'OTransition': 'otransitionend', // oTransitionEnd in very old Opera
                    'MozTransition': 'transitionend',
                    'WebkitTransition': 'webkitTransitionEnd'
                };

            for (i in transitions) {
                if (transitions.hasOwnProperty(i) && el.style[i] !== undefined) {
                    return transitions[i];
                }
            }

            throw 'TransitionEnd event is not supported in this browser';
        }

        if (document.getElementById("main-header")) {
            let tmpOutput = "<div class='jzs-header'><a class='header-logo' href='/'>Sillage™</a>";
            if (document.getElementById("jzs-homeplayer")) {
                tmpOutput += "<div class='header-product-infos'>Satus: <span id='jzs-product-status'></span><br>Current edition: \"<span id='jzs-product-edition'>" + document.getElementById("jzs-homeplayer").getAttribute("data-collection") + "</span>\"</div>";
            }
            tmpOutput += "</div>";

            console.log(tmpOutput);
            
            let jzs_tmp = document.getElementById("main-header").getElementsByClassName("et_menu_container")[0];
            jzs_tmp.innerHTML = tmpOutput + jzs_tmp.innerHTML;
            
            if (document.getElementById("jzs-homeplayer")) {
                updateHeaderProductStatus(document.getElementById("jzs-homeplayer").getElementsByClassName("jzs-video jzs-playing")[0]);
            }
        }

        // handle rainbow product click
        if (document.getElementById("jzs-homeplayer")) {
            let rainbowBtns = document.getElementsByClassName("jzs-select-product")[0].getElementsByClassName("rainbow-btn");
            for (let i = 0; i < rainbowBtns.length; i++) {
                rainbowBtns[i].addEventListener("click", evt => {
                    evt.preventDefault();
                    document.getElementById("jzs-homeplayer").getElementsByClassName("player focused")[0].classList.remove("focused");

                    let jzsPlayer = document.getElementById("jzs-homeplayer").getElementsByClassName("player")[i];
                    jzsPlayer.classList.add("focused");
                    updateHeaderProductStatus(jzsPlayer.getElementsByClassName("jzs-video jzs-playing")[0]);
                });
            }

            let containers = document.getElementById("jzs-homeplayer").getElementsByClassName("player");
            for (let j = 0; j < containers.length; j++) {
                const container = containers[j];

                let btns = container.getElementsByClassName("jzs-video-btn");
                for (let i = 0; i < btns.length; i++) {
                    const btn = btns[i];
                    btn.addEventListener("click", evt => {

                        let newVideo = container.getElementsByClassName("jzs-video")[i];
                        if (!newVideo.classList.contains("jzs-playing")) {
                            let currentVideo = container.getElementsByClassName("jzs-video jzs-playing")[0];
                            newVideo.classList.add("jzs-playing");
                            currentVideo.classList.add("jzs-fade-out");
                            updateHeaderProductStatus(newVideo);

                            let onTransitionOver = transitionEndEventName();
                            let transitionListener = () => {
                                // remove transition listener
                                newVideo.removeEventListener(onTransitionOver, transitionListener);
                                currentVideo.classList.remove("jzs-fade-out", "jzs-playing");
                                currentVideo = newVideo;
                            }
                            // on transition over
                            newVideo.addEventListener(onTransitionOver, transitionListener);
                        }
                    });
                }
            }
        }

        // add custom CSS
        document.body.innerHTML += "<link rel='stylesheet' href='/wp-content/plugins/jzs_homeplayer/public/css/jzs-product-public.css' />";
    });
})(jQuery);