/*******************************************************************************
 *
 *  avast! Online Security plugin
 *  (c) 2013 Avast Corp.
 *
 *  @author: Lucian Corlaciu
 *
 *  Injected Core - cross browser
 *
 ******************************************************************************/

(function($, EventEmitter) {

  if (typeof AvastSP == 'undefined') { AvastSP = {}; }//AVAST Online Security

  AvastSP.ial = AvastSP.ial || {
    /**
     * Background script instance - browser specific
     * @type {Object}
     */
    bs: null,
    /**
     * DNT settings used to determine if a page needs to be refreshed or not
     * @type {Object}
     */
    _CHANGED_FIELDS: {},
    /**
     * Initialization
     * @param  {Object} _bs instance of browser specifics
     * @return {Object} AvastSP.ial instance - browser agnostic
     */
    init: function(_bs){
      this.bs = _bs;
      //this.initPage();
      this.attachHandlers();
      this.bs.messageHandler('initMe');

      return this;
    },
    /**
     * EventEmitter instance to hangle injected layer events.
     * @type {Object}
     */
    _ee: new EventEmitter(),

    _isOldGui : true,
    /**
     * Register events with instance of EventEmitter.
     * @param  {Object} callback to register with instance of eventEmitter
     * @return {void}
     */
    registerEvents: function(registerCallback, thisArg) {
      if (typeof registerCallback === 'function') {
        registerCallback.call(thisArg, this._ee);
      }
    },
    /**
     * Initializes the page where this script is injected
     * @return {void}
     */
    initPage: function() {
      if($('head').length === 0) {
        $('html').prepend("<head></head>");
      }
      AvastSP.ial.injectFonts();
    },
    /**
     * Injects custom fonts
     * @return {void}
     */
    injectFonts: function() {
      if($('#avast_os_ext_custom_font').length === 0) {
        $('head')
          .append("<link id='avast_os_ext_custom_font' href='//fonts.googleapis.com/css?family=Source+Sans+Pro' rel='stylesheet' type='text/css'>")
          .append("<link href='//fonts.googleapis.com/css?family=Lato:400,900' rel='stylesheet' type='text/css'>")
          .append("<link href='//fonts.googleapis.com/css?family=Open+Sans:300,400,700&subset=latin,latin-ext' rel='stylesheet' type='text/css'>")
          .append("<link href='//fonts.googleapis.com/css?family=Gloria+Hallelujah' rel='stylesheet' type='text/css'>");
        }
    },
    /**
     * Message hub - handles all the messages from the background script
     * @param  {String} message
     * @param  {Object} data
     * @param  {Function} reply
     * @return {void}
     */
    messageHub: function(message, data, reply) {
      // emit messages in specific namespace
      this._ee.emit('message.' + message, data, reply);
    },
    /**
     * Reinitialize the page. Handle 'reInit' message from background.
     */
    reInitPage: function (data) {
      AvastSP.ial.initPage();
      AvastSP.ial.attachHandlers();
    },
    /**
     * Attaches DOM handlers
     * @return {void}
     */
    attachHandlers: function() {
      typeof $ !== 'undefined' && $(document).ready(function() {
        window.onunload = AvastSP.ial.onUnload;
      });
    },
    /**
     * Notifies the background script
     * @return {void}
     */
    onUnload: function() {
      AvastSP.ial.bs.messageHandler('unload');
    },
    /**
     * Hides the message box, if present, and restores the page to its initial state
     * @return {void}
     */
    clearBoxes: function() {
      $("body").removeClass("avast-overlay-on").removeClass("avast-bar-on").removeClass("avast-install-on");
    },
    /**
     * Retrive the top element of the page.
     * See: http://stackoverflow.com/questions/10100540/chrome-extension-inject-sidebar-into-page
     * @return retrieved top element to inject ext. HTML into
     */
    getTopHtmlElement: function () {
      var docElement = document.documentElement;
      if (docElement) {
        return $(docElement); //just drop $ wrapper if no jQuery
      } else {
        docElement = document.getElementsByTagName('html');
        if (docElement && docElement[0]) {
          return $(docElement[0]);
        } else {
          docElement = $('html');
          if (docElement.length > -1) {//drop this branch if no jQuery
            return docElement;
          } else {
            throw new Error('Cannot insert the bar.');
          }
        }
      }
    },
    /**
     * Creates the UI for a message box (SiteCorrect, Phishing, Malware, CreditCard Warning)
     * @param  {Object} data
     * @return {[type]}
     */
    createMessageBox: function(data) {
      AvastSP.ial._isOldGui = data.isOldGui;
      AvastSP.ial.initPage();
      if($('#avast-inpage').length <= 0) {
        AvastSP.ial._messageBoxData = data;
        AvastSP.ial.getTopHtmlElement()
          .prepend(Mustache.render(data.isOldGui ? AvastSP.Templates.messageBoxOld : AvastSP.Templates.messageBox, data));

        var ignore = function(){
          AvastSP.ial.clearBoxes();
          $('input').unbind('blur', AvastSP.ial.onBlurHandler);
          $('#avast-inpage').remove();
          AvastSP.ial.bs.messageHandler('messageBoxFeedback',{
            type: AvastSP.ial._messageBoxData.type,
            ok: false,
            auto: false,
            url_from: AvastSP.ial._messageBoxData.url_from
          });
          AvastSP.ial._messageBoxData = null;
        };

        var proceed = function(){
          var $checkBox = $('#avast-extra-checkbox');

          AvastSP.ial.bs.messageHandler('messageBoxFeedback',{
            type: AvastSP.ial._messageBoxData.type,
            ok: true,
            auto: $checkBox.prop ? $checkBox.prop('checked') : $checkBox.attr('checked') ,
            redirect_id: AvastSP.ial._messageBoxData.redirect_id,
            url_from: AvastSP.ial._messageBoxData.url_from,
            url_to: AvastSP.ial._messageBoxData.url,
            brand: AvastSP.ial._messageBoxData.brand
          });
        };

        $('#avast-inpage')
          .find('#avast-action-ignore')
            .click(ignore);


        $('#avast-inpage')
          .find('#avast-action-proceed')
            .click(proceed);

        if(data.warning !== false) {
          AvastSP.ial.showMessageBox();
        } else {
          AvastSP.ial.monitorSensitiveFields();
        }
      }
      AvastSP.ial.bs.messageHandler('messageBoxOpened', {type: data.type});
    },

    /**
     * Displays an prior injected message box
     * @return {[type]}
     */
    showMessageBox: function() {
      AvastSP.ial.getTopHtmlElement().addClass("avast-overlay-on");
    },

    /**
     * Monitors sensitive fields for Credit Card warning functionality
     * @return {[type]}
     */
    monitorSensitiveFields: function() {
      // start monitoring all input elements
      $("input").blur(AvastSP.ial.onBlurHandler);
    },
    /**
     * DOM handlers for specific fields
     * @param  {DOMElement} el
     * @return {Boolean}
     */
    checkInput: function(el) {
      switch(el.attr("type")) {
        case "password":
          // User is trying to insert a password
          return el.val().length > 0;
        case "text":
          var val = el.val();

          var is4DigitCC = function(val){
            return (val.length == 4 && val.search(new RegExp('[0-9]{4}')) == 0);
          }
          // user has entered 4 numbers into the input field
          if(is4DigitCC(val)) {
            // get all previous input fields
            var prevs = el.prevAll("input[type=text]"),
              total = prevs.length;
              // check 3 previous input field for 4 digit numbes
              if (total >= 3) {
                for (var i = 0; i < 3; i++) {
                  if (!is4DigitCC(prevs.eq(i).val())) {
                    return false;
                  }
                }
                // looks like a credit card number split input 4 fields
                return true;
              } else {
                return false;
              }
          }
          // check credit card numbers in the field
          if(val.length == 19) {
            var regExp = new RegExp('[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}');
            return (val.search(regExp) == 0);
          } else if(val.length == 16) {
            var regExp = new RegExp('[0-9]{4}[0-9]{4}[0-9]{4}[0-9]{4}');
            return (val.search(regExp) == 0);
          }
          return false;
      }
    },
    onBlurHandler: function() {
      var el = $(this);
      // validate input
      if (AvastSP.ial.checkInput(el)) {
        // insert card / password message

        $("#avast-title").remove();
        $(AvastSP.ial._isOldGui ? "#avast-body P" : "#avast-body").text(AvastSP.ial._messageBoxData.strings.inputwarning);

        // display the dialog
        AvastSP.ial.showMessageBox();
      }
    },
    /**
     * Check anchor tags in given page and add events to those opening avast:// URLs.
     */
    fixAosUrls: function(data) {
      $('a')
        .filter(function() {
          return (/^avast\:\/\//).test(this.href);
        })
        .click(function(e){
          var matches = this.href.match(/^avast\:\/\/(.+)/);
          if (matches) {
            e.preventDefault();
            var action = data.actions[matches[1]];
            if (action) {
              AvastSP.ial.bs.messageHandler(action.message, action.data);
            }
          }
        });
    },

    /**
     * Create a top bar instance
     * @param {String} bar template HTML to be injected
     * @param {String} selector of the injected bar template
     * @param {String} bar height styling ('50px')
     * @return {Object} a wrapper for the bar
     */
    topBar: function (barHtml, barElementSelector, barHeight) {
      var _oldHtmlTopMargin = null;
      var _oldFixed = []
      AvastSP.ial.getTopHtmlElement().prepend(barHtml);

      return {
        /**
         * Display the bar.
         */
        show: function () {
          $(barElementSelector).css({top: '0px', left: '0px'});
          // slide page down
          AvastSP.ial.getTopHtmlElement().css('margin-top',
            function (index, value) {
              _oldHtmlTopMargin =  value;
              return barHeight;
            }
          );

          // fix for elements with position fixed
          $("*").each(function(){
            var $node = $(this);
            if($node.css("position") == "fixed") {
              var top = parseInt($node.css("top"));
              if(typeof(top) == "number") {
                $node.css("top", top+barHeight);
                _oldFixed.push({$node : $node, top: top})
              }
            }
          });
        },
        /**
         * Remove/close the top bar and reset relevant CSS.
         */
        remove: function() {
          $(barElementSelector).remove();
          // restore page position
          if (_oldHtmlTopMargin)
            AvastSP.ial.getTopHtmlElement().css('margin-top', _oldHtmlTopMargin);

          // revert altered fixed positions.
          if(_oldFixed.length > 0){
            for(var i=0, j=_oldFixed.length; i<j; i++){
                _oldFixed[i].$node.css("top",_oldFixed[i].top);
            }
          }
        }
      };
    }
  }; // AvastSP.ial

  AvastSP.ial.registerEvents(function(ee) {
    ee.on('message.reInit',          AvastSP.ial.reInitPage);
    ee.on('message.showMessageBox',  AvastSP.ial.createMessageBox);
    ee.on('message.fixAosUrls',      AvastSP.ial.fixAosUrls);
  });

}).call(this, $, EventEmitter2);

/*******************************************************************************
 *
 *  avast! Online Security plugin
 *  (c) 2014 Avast Corp.
 *
 *  @author:
 *
 *  Injected Layer - SafePrice - cross browser
 *
 ******************************************************************************/

(function($) {

  var SAFESHOP_REFRESH_INTERVAL = 45 * 60 * 1000;

  var safeShopRefreshIID = null;

  if (typeof AvastSP === 'undefined' || typeof AvastSP.ial === 'undefined') {
    console.error('AvastSP.ial instance not initialised to add SafePrice component');
    return;
  } else if (typeof AvastSP.ial.sp !== 'undefined') {
    return;
  }

  AvastSP.ial.sp = {

    /**
     * Check the current page using the received selector.
     * @param {Object} page related data
     */
    checkSafeShop: function (data) {
      var safeShopData = $.extend({ scan: null }, data);
      // product scan - to retrieve page data
      AvastSP.ial.productScan(data.csl, function(response) {
        safeShopData.scan = response;
        safeShopData.coupons = data.coupons;
        safeShopData.referrer = document.referrer;
        AvastSP.ial.bs.messageHandler('safeShopOffersFound', safeShopData);
      });
    },

    createSafeShopBarCoupon : function(data) {
      this.createSafeShopBar(data);
    },

    /**
     * Creates UI for the Top Bar (SafeZone)
     * @param  {Object} data
     * @return {[type]}
     */
    createSafeShopBar: function(data) {
      var pop_elements = ['#avast-sas-offers-drop','#avast-sas-coupons-drop',
        '#avast-sas-help-drop','#avast-sas-settings-drop'];
      function toggleBlockShow (id) { $(id).toggleClass('avast-sas-drop-show'); }
      function showBlock (id) { $(id).addClass('avast-sas-drop-show'); }
      function hideBlock (id) { $(id).removeClass('avast-sas-drop-show'); }
      function feedback (data) { AvastSP.ial.bs.messageHandler('safeShopFeedback', data); }
      function showBlockExcl (id) {
        toggleBlockShow(id);
        for (var i=0; i < pop_elements.length; i++) {
          if (id !== pop_elements[i]) {
            hideBlock(pop_elements[i]);
          }
        }
      }
      function alignPosition(elementSel, alignToSel, toRight, offset) {
        var $elm = $(elementSel);
        var $aTo = $(alignToSel);
        if ($elm.length && $aTo.length) {
          var pos = $aTo.position().left;
          if (toRight) {
            pos += $aTo.width();
          }
          $elm.css('left', pos + offset + 'px');
        }
      }


      function helpShow (event) {
        if (event) event.stopPropagation();
        showBlockExcl('#avast-sas-help-drop');

        alignPosition('#avast-sas-hint-best-offer', '#avast-sas-best-offer-link>div', false, 0);
        alignPosition('#avast-sas-hint-compare-prices', '#avast-sas-offers', false, 0);
        alignPosition('#avast-sas-hint-coupons', '#avast-sas-coupons', true, -70);

        $("html").bind("click", helpConfirm );
      }

      function helpAction (event, optin) {
        event.stopPropagation();
        $("html").unbind("click", helpConfirm );
        feedback({type: 'safeShopOptin', optin: optin});
        hideBlock('#avast-sas-help-drop');
      }

      function helpConfirm (event) {
        helpAction(event, true);
      }

      function helpCancel (event) {
        helpAction(event, false);
        bar.remove();
      }

      function openSettings (event) {
        event.stopPropagation();
        $("html").unbind("click", helpConfirm );
        AvastSP.ial.bs.messageHandler('openSettings', {});
        hideBlock('#avast-sas-help-drop');
      }

      function dropControl (buttonId, dropId) {
        var active = false;
        var timeoutId = null;

        function hideOnTimeout () {
          hideBlock(dropId);
          timeoutId = null;
          active = false;
        }

        function hoverIn (e) {
          if (timeoutId) {
            clearTimeout(timeoutId);
            timeoutId = null;
          } else {
            showBlockExcl(dropId);
            $(dropId).css('left', $(buttonId).position().left+'px');
            active = true;
          }
        }

        function hoverOut (e) {
          if (!timeoutId && active) {
            timeoutId = setTimeout(hideOnTimeout, 500);
          }
        }

        $(buttonId).hover(hoverIn, hoverOut);
        $(buttonId).click( function(e) {
          if (active) {
            hideBlock(dropId); active = false;
          } else {
            showBlockExcl(dropId);
            $(dropId).css('left', $(buttonId).position().left+'px');
          }
        });
        $(dropId).hover(hoverIn, hoverOut);
      }

      var bar;
      var domain = data.domain;

      AvastSP.ial.initPage();

      if($('.avast-extension-safeshop-bar').length > 0) {
        return;
      }

      var height = (data.coupon.code) ? 100 : 40;

      bar = AvastSP.ial.topBar(
        Mustache.render(AvastSP.Templates.safeShopBar, data),
        '.avast-extension-safeshop-bar',
        height+'px'
      );

      dropControl('#avast-sas-offers', '#avast-sas-offers-drop');
      dropControl('#avast-sas-coupons', '#avast-sas-coupons-drop');

      $('#avast-sas-help').click(helpShow);

      $('#avast-sas-promo-action').click(helpConfirm);

      $('#avast-sas-promo-settings').click(openSettings);

      $('#avast-sas-settings').click(function(e) {
        showBlockExcl('#avast-sas-settings-drop');
      });

      $('#avast-sas-close').click(function() {
        bar.remove();
      });

      $('.avast-sas').mouseenter(function(event){
          $(event.currentTarget).removeClass("avast-sas-discreet");
      });

      setTimeout(function(){
          $('.avast-sas').removeClass("avast-sas-discreet-animated");
      },5000);

      $('.avast-sas-settings-list').click(function(e) {
        hideBlock('#avast-sas-settings-drop');
        var action = e.currentTarget.id;
        feedback ({type: 'safeShopSettings', action: action, domain: domain});
        bar.remove();
      });

      $('.avast-sas-offer-link').click(function(e) {
        e.preventDefault();
        var url = e.currentTarget.href,
          offers = data.safeShopOffers,
          offer = null;
        for (var i=0; i<offers.length; i++) {
          if (offers[i].url === url) {
            offer = offers[i];
            break;
          }
        }
        if (url) {
          feedback({type: 'offerRedirect',
            url: url,
            originalRequest: data.originalQuery,
            offer: offer
          });
        }
      });

      $('.avast-sas-coupon-link').click(function(e) {
        var url = e.currentTarget.href,
          coupons = data.safeShopCoupons,
          coupon = null;
        for (var i=0; i<coupons.length; i++) {
          if (coupons[i].url === url) {
            coupon = coupons[i];
            break;
          }
        }
        if (url) {
          feedback({type: 'couponRedirect',
            url: url,
            originalRequest: data.originalQuery,
            coupon: coupon
          });
        }
      });


      /**
       * Handle Coupon links. Block link and pass event back to extension.
       * @param  {event} e click event
       */
      $('.avast-sas-coupon-link').click(function(e){
          var url = e.currentTarget.href,
              coupons = data.coupons,
              coupon = null;

          for (var i=0; i<coupons.length; i++) {
            if (coupons[i].url === url) {
              coupon = coupons[i];
              break;
            }
          }

          e.preventDefault();

          feedback({
            type : 'safeShopCouponClick',
            url  : url,
            coupon : coupon
          });
      });

      $('.avast-sas-code-button').click(function(e) {
        e.preventDefault();
        var text = e.currentTarget.textContent;
        AvastSP.ial.bs.messageHandler('copyToClipboard', {text: text});
        $('#avast-sas-copied-confirm').addClass('avast-sas-confirm-show');
        setTimeout(function() {
          $('#avast-sas-copied-confirm').removeClass('avast-sas-confirm-show');
        }, 2000);
      });


      bar.show();
      if (data.showHelp) {
        helpShow();
      }

      // set refresh interval
      if (!safeShopRefreshIID) {
        safeShopRefreshIID = setInterval(
          function () {
            AvastSP.ial.bs.messageHandler('safeShopOffersFound', data.safeShopData);
          },
          SAFESHOP_REFRESH_INTERVAL
        );
      }

    }//createSafeShopBar

  };

  /* Register SafePrice Event handlers */
  AvastSP.ial.registerEvents(function(ee) {
    ee.on('message.checkSafeShop',
      AvastSP.ial.sp.checkSafeShop.bind(AvastSP.ial.sp));
    ee.on('message.showSafeShopCoupon',
      AvastSP.ial.sp.createSafeShopBarCoupon.bind(AvastSP.ial.sp));
    ee.on('message.showSafeShop',
      AvastSP.ial.sp.createSafeShopBar.bind(AvastSP.ial.sp));
  });

}).call(this, $);
