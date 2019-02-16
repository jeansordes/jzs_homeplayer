document.addEventListener("DOMContentLoaded", () => {
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

    // handle product page
    if (document.getElementById("jzs-product")) {
        console.log("jzs product page detected");
        let container = document.getElementById("jzs-product");
        let currentColorBtn = document.getElementsByClassName("swatch-" + document.getElementById("jzs-color-btns").getElementsByClassName("btn hover")[0].getAttribute("data-target"))[0];

        let getCurrentPrice = () => document.getElementsByClassName("woocommerce-Price-amount")[0].innerText;

        // connect jzs-buy-btn with real-buy-btn
        container.getElementsByClassName("buy-btn")[0].addEventListener("click", () => document.getElementsByClassName("single_add_to_cart_button")[0].click());

        let deHoverAllBtns = (btns) => {
            for (let i = 0; i < btns.length; i++)
                btns[i].classList.remove("hover");
        }

        // connect jzs-color-btns with real-color-btns
        let jzsColorBtns = document.getElementById("jzs-color-btns").getElementsByClassName("btn");
        for (let index = 0; index < jzsColorBtns.length; index++) {
            let btn = jzsColorBtns[index];
            btn.addEventListener("click", () => {
                deHoverAllBtns(jzsColorBtns);
                btn.classList.add("hover");
                currentColorBtn = document.getElementsByClassName("swatch-" + btn.getAttribute("data-target"))[0];
                currentColorBtn.click();

                for (let i = 0; i < jzs_product_data.product.variations.length; i++) {
                    let colorData = jzs_product_data.product.variations[i];
                    if (colorData.colorSlug == btn.getAttribute("data-target")) {
                        document.getElementById("jzs-model").innerText = colorData.model.toUpperCase();
                        colorData.product_thumbnails.forEach((thmbnl, th_i) => {
                            container.getElementsByClassName("thumbnails")[0].getElementsByClassName("btn")[th_i].src = thmbnl;
                            container.getElementsByClassName("product-section")[0].getElementsByTagName("img")[th_i].src = thmbnl;
                        });

                        i = jzs_product_data.product.variations.length;
                    }
                }
            });
        }

        // connect jzs-size-btns with real-size-select-option
        let jzsSizeBtns = document.getElementById("jzs-size-btns").getElementsByClassName("btn");
        for (let index = 0; index < jzsSizeBtns.length; index++) {
            let btn = jzsSizeBtns[index];
            btn.addEventListener("click", () => {
                deHoverAllBtns(jzsSizeBtns);
                btn.classList.add("hover");
                let select = document.getElementById("pa_height");

                /* BIG FAT HACK AHEAD : la raison c'est que si on se content de faire select.value = "xs", le JS qui met à jour le prix ne détecte pas le changement. Donc on contourne */
                // on remet tout à zéro
                document.getElementsByClassName("reset_variations")[0].click();
                // on selectionne la valeur qu'on veut
                select.value = btn.getAttribute("data-target");
                // puis on reclick sur la couleur
                currentColorBtn.click();

                for (let j = 0; j < jzs_product_data.sizes.length; j++) {
                    let sizeData = jzs_product_data.sizes[j];
                    if (sizeData.slug == btn.getAttribute("data-target")) {
                        document.getElementById("jzs-height").innerText = sizeData.description;
                        document.getElementById("jzs-wearing").innerText = sizeData.name.toUpperCase();

                        j = jzs_product_data.sizes.length;
                    }
                }

            });
        }

        // TODO: Rendre le tout vraiment intéractif
        let thumbnails = container.getElementsByClassName("thumbnails")[0].getElementsByClassName("btn");
        for (let i = 0; i < thumbnails.length; i++) {
            let imgBtn = thumbnails[i];
            imgBtn.addEventListener("click", () => {
                let allBigImgs = container.getElementsByClassName("product-section")[0].getElementsByTagName("img");
                for (let j = 0; j < allBigImgs.length; j++) {
                    allBigImgs[j].classList.remove("focused");
                }
                allBigImgs[i].classList.add("focused");
                imgBtn.classList.add("focused");
            });
        }
        console.log(jzs_product_data);
    }

    // handle checkout / cart page
    if (document.getElementById("jzs-checkout")) {
        console.log("jzs checkout page detected");
        let container = document.getElementById("jzs-checkout");
        let products = document.getElementById("jzs-cart-products");
        let addProduct2Cart = p => {
            products.innerHTML += '<div class="product-infos"><div class="header"><span class="badge">' + p.slug.toUpperCase() + '</span></div><div class="details"><div class="thumbnail"><img src="' + p.imgSrc + '" alt="product thumbnail"></div><div class="text-part"><label class="rm-btn btn"><strong>&times;</strong></label><div class="product-name">' + p.name + '</div><div class="specs"><div><span class="label">COLOR</span><div class="value jzs-title-framed jzs-title-font">' + p.color.toUpperCase() + '</div></div><div><span class="label">SIZE</span><div class="value jzs-title-framed jzs-title-font">' + p.size.toUpperCase() + '</div></div><div><span class="label">QUANTITY</span><input type="number" step="1" min="0" value="' + p.amount + '" class="value jzs-title-framed jzs-title-font" /></div></div><strong class="price">' + p.price + '</strong></div></div></div>';
        }

        let initProducts = () => {
            let realProducts = document.getElementsByClassName("woocommerce-cart-form__cart-item cart_item");
            products.innerHTML = '';
            for (let i = 0; i < realProducts.length; i++) {
                let realProduct = realProducts[i];
                let realRmBtn = realProduct.getElementsByClassName("remove")[0];
                let productNameDOM = realProduct.getElementsByClassName("product-name")[0].children[0];
                addProduct2Cart({
                    slug: productNameDOM.getAttribute("href").split('/')[4],
                    imgSrc: realProduct.getElementsByClassName("attachment-woocommerce_thumbnail size-woocommerce_thumbnail")[0].getAttribute("src"),
                    name: productNameDOM.innerText.split(" - ")[0],
                    color: productNameDOM.innerText.split(" - ")[1].split(", ")[1],
                    size: productNameDOM.innerText.split(" - ")[1].split(", ")[0],
                    amount: realProduct.getElementsByClassName("input-text qty text")[0].value,
                    price: realProduct.getElementsByClassName("woocommerce-Price-amount amount")[1].innerText
                });
                products.getElementsByTagName("input")[i].addEventListener("change", evt => {
                    realProduct.getElementsByClassName("input-text qty text")[0].value = evt.target.value;
                    realProduct.getElementsByClassName("input-text qty text")[0].dispatchEvent(new Event('change', { 'bubbles': true }));
                    document.getElementsByClassName("actions")[0].getElementsByClassName("button")[1].click();
                    let currentProductPrice = realProduct.getElementsByClassName("woocommerce-Price-amount amount")[1].innerText;
                    let interval = setInterval(() => {
                        if (currentProductPrice != document.getElementsByClassName("woocommerce-cart-form__cart-item cart_item")[i].getElementsByClassName("woocommerce-Price-amount amount")[1].innerText) {
                            clearInterval(interval);
                            initProducts();
                        }
                    }, 500);
                });
                products.getElementsByClassName("rm-btn")[i].addEventListener("click", () => realRmBtn.click());
            }

            if (realProducts.length == 0) {
                products.innerHTML = "<em>No products</em>";
            }

            document.getElementById("jzs-checkout-subtotal").innerText = document.getElementsByClassName("cart-subtotal")[0].getElementsByClassName("woocommerce-Price-amount amount")[0].innerText;

            document.getElementById("jzs-checkout-delivery").innerText = document.getElementById("shipping_method").getElementsByClassName("woocommerce-Price-amount amount")[0].innerText;
        }

        initProducts();

        container.getElementsByClassName("order-btn btn")[0].addEventListener("click", () => document.getElementsByClassName("checkout-button")[0].click());
    }
    console.log("jzs script loaded");
});