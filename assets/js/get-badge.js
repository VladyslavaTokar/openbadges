/* =========================
    jQuery
   ========================= */
jQuery(function (event) {
    var clicked = false;

    /**
     * @description Get the param from the url.
     *
     * @param strinf name, the name of the param that we want to take.
     */
    var urlParam = function (name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results[1] || 0;
    }

    /**
     * @description Here's wrap the code that permit to simplify an ajax call.
     *
     * @param {array} data, that will send with the ajax call.
     * @param {function} func, that will be execute after the success of the ajax call,
     */
    var ajaxCall = function (data, func) {
        jQuery.post(
            globalUrl.ajax,
            data)
            .done(
                function (response) {
                    func(response);
                    clicked = false;
                }
            )
            .fail(
                function (xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                    console.log(textStatus);
                    console.log(errorThrown);
                }
            );
    }

    /**
     * @description Build the html structure to show the loading gif.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @return string html format.
     */
    var loadingPage = function () {
        return ("<div class='cover-container'><header class='masthead clearfix'>" +
            "</header><main role='main' class='inner cover'>" +
            "<img src='" + globalUrl.loader + "' width='80px' height='auto'/>" +
            "</main>" +
            "<footer class='mastfoot'></footer></div>");
    }

    /**
     * @description Permit to check if inside an <input> exist a value, if
     *              exist add the class "is-valid", if not add "is-invalid".
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {jQuery} input <input> tag with a 'value'.
     *
     * @return void
     */
    var checkValue = function (input) {
        if (input.val() == "") {
            input.addClass("is-invalid");
            input.on("input", function (event) {
                if (input.val() != "") {
                    input.removeClass("is-invalid");
                    input.addClass("is-valid");
                } else {
                    input.removeClass("is-valid");
                    input.addClass("is-invalid");
                }
            });
        }
    }

    /**
     * @description Show the Mozilla Open Badge step inside the #gb-wrap <div>.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @return void
     */
    var showGetMOBOpenBadges = function () {
        jQuery("#gb-wrap").fadeOut(400, function (event) {
            jQuery("#gb-wrap").html(loadingPage(event));

            // If the user have an account in Open Badge BackPack,
            // have also the permission to get the badge.
            var data = {
                'action': 'ajaxGbShowMozillaOpenBadges',
                'idBadge': urlParam('v'),
            };

            var func = function (response) {
                jQuery("#gb-wrap").html(response);
            }

            ajaxCall(data, func);

        }).delay(400).fadeIn(400);
    }

    /**
     * @description Get definitely the badge and after show the conclusion
     *              step inside the #gb-wrap <div>
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {boolean} mozOpenBadge
     * @return void
     */
    var showConclusion = function (mozOpenBadge = 0) {
        jQuery("#gb-wrap").fadeOut(400, function (event) {
            jQuery("#gb-wrap").html(loadingPage(event));
            var data = {
                'action': 'ajaxGbShowConclusion',
                'isMozilla': mozOpenBadge,
                'idBadge': urlParam('v'),
            };

            var func = function (response) {
                jQuery("#gb-wrap").html(response);
            }

            ajaxCall(data, func);

        }).delay(400).fadeIn(400);
    }

    /**
     * @description Click event of the button continue of the first step.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {array} event of the click
     * @return vodi
     */
    var btnContinue = "#gb-continue";
    jQuery(document).on("click", btnContinue, function (event) {
        jQuery(btnContinue).prop('disabled', true);

        jQuery("#gb-wrap").fadeOut(400, function (event) {
            jQuery("#gb-wrap").html(loadingPage());

            var data = {
                'action': 'ajaxGbShowLogin',
                'idBadge': urlParam('v'),
            };

            var func = function (response) {
                jQuery("#gb-wrap").html(response);
                jQuery(btnContinue).prop('disabled', false);
            }

            ajaxCall(data, func);

        }).delay(400).fadeIn(400);
    });

    /**
     * @description Click event of the button login.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {array} event of the click
     *
     * @return void
     */
    var btnLogin = "#gb-form-login";
    jQuery(document).on("submit", btnLogin, function (event) {
        jQuery(btnLogin).prop('disabled', true);
        event.preventDefault();

        var email = jQuery("#staticEmail").val();
        var password = jQuery("#inputPassword").val();
        var remember = jQuery("#inputRemember").is(':checked');
        var data = {
            'action': 'ajaxGbLogin',
            'idBadge': urlParam('v'),
            'userEmail': email,
            'userPassword': password,
            'remember': remember,

        };

        var func = function (response) {
            if (response == true) {
                showGetMOBOpenBadges();
            } else {
                jQuery("#gb-resp-login").html(response);
            }
            jQuery(btnLogin).prop('disabled', false);
        }

        ajaxCall(data, func);
    });

    /**
     * @description Click event of the button registration.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {array} event of the click.
     *
     * @return void
     */
    var formRegister = "#gb-form-registration";
    var responseRegister = "#gb-resp-register";
    var btnRegister = "#submit-form";
    var lblRegister = "#lbl-submit-form";
    jQuery(document).on("submit", formRegister, function (event) {
        jQuery(btnRegister).prop('disabled', true);
        jQuery(lblRegister).addClass("disabled");

        event.preventDefault();
        jQuery(responseRegister).html("");

        var inputFields = [
            jQuery(this).find("#reg-email"),
            jQuery(this).find("#reg-user-name"),
            jQuery(this).find("#reg-first-name"),
            jQuery(this).find("#reg-last-name"),
            jQuery(this).find("#reg-pass"),
            jQuery(this).find("#reg-repeat-pass"),
        ];

        if (this.checkValidity() === false) {
            event.stopPropagation();
            inputFields.forEach(function (field) {
                checkValue(field);
            });
            jQuery(btnRegister).prop('disabled', false);
            jQuery(lblRegister).removeClass("disabled");
        } else {

            var data = {
                'action': 'ajaxGbRegistration',
                'idBadge': urlParam('v'),
                'userEmail': inputFields[0].val(),
                'userName': inputFields[1].val(),
                'firstName': inputFields[2].val(),
                'lastName': inputFields[3].val(),
                'userPassword': inputFields[4].val(),
                'userRepPass': inputFields[5].val(),
            };

            var func = function (response) {
                if (response == 0) {
                    showGetMOBOpenBadges();
                } else if (response) {
                    jQuery(responseRegister).html(response);
                }
                jQuery(btnRegister).prop('disabled', false);
                jQuery(lblRegister).removeClass("disabled");

            }
            ajaxCall(data, func);
        }
        this.classList.add('was-validated');
    });

    /**
     * @description Click event of get badge button in Mozilla Open badge step.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {array} event of the click
     *
     * @return void
     */
    var btnGetBadgeMob = "#gb-ob-get-badge"
    jQuery(document).on("click", btnGetBadgeMob, function (event) {
        jQuery(btnGetBadgeMob).prop('disabled', true);

        var data = {
            'action': 'ajaxGbGetJsonUrl',
            'idBadge': urlParam('v'),
        };

        var func = function (response) {
            OpenBadges.issue([response], function (errors, successes) {
                if (successes.length) {
                    showConclusion(1);
                } else if (errors.length) {
                    jQuery("#gb-ob-response").html("Badge not sent!")
                }
                jQuery(btnGetBadgeMob).prop('disabled', false);

            });
        }
        ajaxCall(data, func);

    });

    /**
     * @description Click event of skip get Mozilla Open Badge.
     *
     * @author Alessandro RICCARDI
     * @since  x.x.x
     *
     * @param {array} event  of the click
     *
     * @return void
     */
    var btnGetBadgeStandard = "#gb-get-standard";
    jQuery(document).on("click", btnGetBadgeStandard, function (event) {
        jQuery(btnGetBadgeStandard).prop('disabled', false);
        showConclusion(0);
        jQuery(btnGetBadgeStandard).prop('disabled', true);
    });
});
