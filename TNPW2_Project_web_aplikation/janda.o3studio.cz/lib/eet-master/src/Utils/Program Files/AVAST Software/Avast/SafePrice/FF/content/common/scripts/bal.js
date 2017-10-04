/*******************************************************************************
 *
 *  avast! Online Security plugin
 *  (c) 2013 Avast Corp.
 *
 *  @author: Lucian Corlaciu
 *
 *  Background Core - cross browser
 *
 ******************************************************************************/

(function(_, EventEmitter) {

  // Extension editions
  var DEFAULT_EDITION = 0; // if no ed. determined start with AOS ed.

  var EDITION_FEATURES = [
    // 0 - AOS
    {
      applicationEvents : true, // ev. for gamification
      newUrlInfoVersion : false,
      safePrice : true
    },
    // 1 - ABOS
    {
      applicationEvents : false, // ev. for gamification
      newUrlInfoVersion : true,
      safePrice : false
    }
  ];

  var CORE_DEFAULT_SETTINGS = { // core defaults
    current : {
      callerId : 0,  // Not set by default
      userId : null,  // Persisted userId
    },
    features : {}
  };

  if (typeof AvastSP == 'undefined') { AvastSP = {}; } //AVAST Online Security - namespace

  var localStorage = null; // Browser specific local storage
  var sing; // AvastSP.bal instance - browser agnostic
  var back; // background script instance - browser specific

  var _forcedEdition = null;

  // Web Reputation
  var RATING_LEVEL = ['Undefined', 'Positive', 'Average', 'Bad'];
  var RATING_COLOR = ['#a5abb2','#47cc82','#fbb153','#f7636f'];

  // Regexp matching URLs that will be enabled with avast:// protocol actions
  var AOS_URLS_ENABLED_URLS = /^http[s]?\:\/\/aos.avast.com(\:\d+)?\/upgrade(\/)?/;

  // Actions assigned to avast protocol: avast://[action]
  //  - action is in form of message to be send to the background script: bal.js
  var AOS_URLS_ACTIONS = {
    'settings' : { message: 'openSettings', data : {} } // avast://settings -> open settings page
  };

  var SHOW_WELCOME_PAGE_RULE = function (prevMinor, currMinor) {
    // Stop showing the welcome/update screens until provided with better content
    return false;
  };

  AvastSP.PHISHING_REDIRECT = AvastSP.SAFE_ZONE_REDIRECT = AvastSP.SITE_CORRECT_MSG_REDIRECT = 'http://www.avast.com';

  AvastSP.AVAST_UPGRADE_PAGE_URL = 'http://aos.avast.com/upgrade/';


  AvastSP.CREATE_MESSAGE_BOX_TIMEOUT = 1500;

  /*
   * Definition of libraries to inject into content pages - default libs
   *  - modify using 'modifyInjectLibs'
   */
  var _injectLibs = {
    css: 'common/ui/css/style.modal.css',
    libs: [
      'common/libs/jquery-1.5.2.js',
      'common/libs/mustache.js',
      'common/libs/eventemitter2.js',
      'common/scripts/templates.js',
      'common/scripts/ial.js'
    ],
    script: 'scripts/extension.js'
  };


  /**
   * Alter the DNT defaults to force the DNT optin. - set false
   */
  function _alterDntFeaturesDefaults (settings) {
    _(['dntSocial','dntAdTracking','dntWebAnalytics','dntOthers']).each(
      function(key) {settings.features[key] = false;}
    );
    settings.features['dnt'] = true;
  }

  AvastSP.bal = {

    EXT_AOS_ID : 0,
    EXT_ABOS_ID : 1,

    RATING_COLOR : RATING_COLOR,

    reqServices: 0x0000, // services  of UrlInfo

    _bal_modules : [], // initialized modules
    _core_modules : [], // core modules
    _bootstrap_modules : [], // bootstrap modules based on edition config
    /**
     * Register BAL module.
     * @param {Object} module to register
     */
    registerModule: function(module) {
      if (typeof module.bootstrap === 'function') {
        this._bootstrap_modules.push(module);
      } else {
        this._core_modules.push(module);
      }
    },
    /**
     * EventEmitter instance to hangle background layer events.
     * @type {Object}
     */
    _ee: new EventEmitter({wildcard:true, delimiter: '.'}),
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
    // TODO mean to unregister the events
    /**
     * Emit background event
     * @param {String} event name
     * @param {Object} [arg1], [arg2], [...] event arguments
     */
    emitEvent: function() {
      // delegate to event emitter
      this._ee.emit.apply(this._ee, arguments);
    },
    /**
     * browser type
     * @type {String}
     */
    browser: '',

    /**
     * Get important info about the extension running.
     */
    trace: function (log) {
      _.each(this._bal_modules, function(module) {
        if (typeof module.trace === 'function') {
          module.trace(log);
        }
      });

      console.log('> all listeners ', this._ee.listeners('*').length);
    },

    /**
     * Initialization
     * @param  {String} browserType
     * @param  {Object} _back
     * @return {Object}
     */
    init: function(browserType, _back, locStorage, editionConfig, forceEdition) {
      if(sing){
        return sing;
      }

      _forcedEdition = forceEdition;

      EDITION_FEATURES = _.isArray(editionConfig) ?
        _.merge(EDITION_FEATURES, editionConfig) :
        _.map(EDITION_FEATURES, function (features) { return _.merge(features, editionConfig); });
        // same config for all editions applied to all features

      this.browser = browserType;
      back = _back;
      localStorage = locStorage;
      sing = this;

      this.initEdition( _forcedEdition == null ? DEFAULT_EDITION : _forcedEdition );

      this.settings = new AvastSP.bal.troughStorage('settings');
      this.mergeInSettings(CORE_DEFAULT_SETTINGS);

      Q.fcall(function() {
          return this._core_modules;
        }.bind(this))
        .then(this.initModuleSettings.bind(this))
        .then(this.initModules.bind(this))
        .then(function() {
          // Connect Avast if it listens on the machine
          return AvastSP.local.connect(this);
        }.bind(this))
        .get('avastEdition')
        .then(this.getCurrentEdition.bind(this))
        .then(this.initEdition.bind(this))
        .then(this.bootstrapInit.bind(this))
        .then(this.initModuleSettings.bind(this))
        .then(this.initModules.bind(this))
        .then(this.afterInit.bind(this))
        .fail(function (e) {
          console.error('Error in bal.init: ', e);
        });

      return this;
    },
    initEdition : function (edition) {
      var features = EDITION_FEATURES[edition];

      AvastSP.CONFIG.EDITION  = edition;
      AvastSP.CONFIG.FEATURES = features;
      this.reqUrlInfoServices  = features.reqUrlInfoServices;
      AvastSP.CONFIG.CALLERID = features.callerId;
      AvastSP.CONFIG.EXT_TYPE = features.extType;
      AvastSP.CONFIG.EXT_VER  = features.extVer;
      AvastSP.CONFIG.DATA_VER = features.dataVer;

      return Q.fcall(function() { return edition;});
    },
    bootstrapInit : function (edition) {
      var features = EDITION_FEATURES[edition];
      var bootstrapped = _.reduce(this._bootstrap_modules, function(bModules, moduleBootstrap) {
        var module = moduleBootstrap.bootstrap(features);
        if (module) bModules.push(module);
        return bModules;
      }, [], this);
      return Q.fcall(function () { return bootstrapped; });
    },
    initModules : function (modules) {
      _.each(modules, function(module) {
        if (module) {
          // register individual modules - init and register with event emitter
          if (typeof module.init === 'function') module.init(this);
          if (typeof module.registerModuleListeners === 'function') module.registerModuleListeners(this._ee);
          this._bal_modules.push(module);
        }
      }, this);
      return Q.fcall(function () { return modules; });
    },
    initModuleSettings : function (modules) {
      var defSettings = AvastSP.bal.getDefaultSettings(modules);
      AvastSP.bal.mergeInSettings(defSettings);
      AvastSP.bal.updateOldSettings();
      return Q.fcall(function () { return modules; });
    },
    afterInit : function () {
      _.each(this._bal_modules, function(module) {
        // after init - all modules initialized
        if (typeof module.afterInit === 'function') module.afterInit();
        this._bal_modules.push(module);
      }, this);
    },
    /**
     * Called once the local based service get initialized.
     */
    initLocalService: function(port) {
      _.each(this._bal_modules, function(module) {
        // after init - all modules initialized
        if (typeof module.initLocalService === 'function') module.initLocalService(port);
      }, this);
    },
    /**
     * Modify the inject libraries of the instance.
     * @param {Function} callback function to modify the libraries to inject.
     */
    modifyInjectLibs: function(modifyCallback) {
      if (typeof modifyCallback === 'function') {
        _injectLibs = modifyCallback(_injectLibs);
      }
    },
    /**
     * Get extension definition of libraries to inject into content page.
     */
    getInjectLibs: function() {
      return _injectLibs;
    },
    /**
     * creates the settings object or updates an already present one
     * @return {void}
     */
    mergeInSettings: function(settings) {
      var newSettings = this.settings.get(),
          big, small;

      for(big in settings) {
        if(newSettings[big] === undefined){
          newSettings[big] = settings[big];
        }
        else {
          for(small in settings[big]) {
            if (newSettings[big][small] === undefined) {
              newSettings[big][small] = settings[big][small];
            }
          }
        }
      }
      this.settings.set(newSettings);
    },
    /**
     * updates the stored settings from AvastSP
     * @return {void}
     *
     * TODO - save and use settings in a single place
     */
    updateOldSettings: function() {
      var settings = this.settings.get();
      AvastSP.CONFIG.COMMUNITY_IQ = settings.features.communityIQ;
      AvastSP.CONFIG.ENABLE_SERP = settings.features.serp;
      AvastSP.CONFIG.ENABLE_SERP_POPUP = settings.features.serpPopup;
      AvastSP.CONFIG.ENABLE_SAS = settings.features.safeShop;
      AvastSP.CONFIG.USERID = settings.current.userId;
    },

    getCurrentEdition : function(localAvastEdition) {
      var deferred = Q.defer();
      if (_forcedEdition == null) {
        var settings = this.settings.get();
        var storedEdition = settings.current.edition;
        if (localAvastEdition !== undefined && localAvastEdition !== null) {
          if (!storedEdition || storedEdition !== localAvastEdition) {
            settings.current.edition = localAvastEdition;
            this.settings.set(settings);
          }
          deferred.resolve( localAvastEdition );
        } else {
          deferred.resolve( storedEdition || DEFAULT_EDITION );
        }
      } else {
        deferred.resolve( _forcedEdition );
      }
      return deferred.promise;
    },

    /**
     * Notify the listeners about feature settings change.
     */
    featureSettingChanged: function(key, oldVal, newVal) {
      this._ee.emit('featureChanged.' + key, newVal, oldVal);
      this._ee.emit('feature.changed', key, newVal, oldVal);
    },
    /**
     * Hook in listener to register feature settings and enable/disable the functionality.
     * Linstener is triggered first time upon being registered to get current value.
     * @param {String} key of the feature in sttings
     * @param {Function} listener function called with (newVal, [oldVal]), or (currentVal) on registration
     */
    hookOnFeatureChange: function(key, changeListener) {
      this._ee.on("featureChanged." + key, changeListener);
      // run listener to register current settings
      var settings = this.settings.get();
      changeListener(settings.features[key]);
    },
    /**
     * Default settings with default values
     * @return {Object}
     */
    getDefaultSettings: function(modules) {
      return _.reduce (modules,
        function(defaults, module) {
          if (typeof module.getModuleDefaultSettings === 'function') {
            var moduleDefaults = module.getModuleDefaultSettings();
            if (moduleDefaults) {
              defaults = _.merge(defaults, moduleDefaults);
            }
          }
          return defaults;
        },
        CORE_DEFAULT_SETTINGS
      );
    },
    /**
     * Message hub - handles all the messages from the injected scripts
     * @param  {String} type
     * @param  {Object} message
     * @param  {Object} tab
     * @return {void}
     */
    commonMessageHub: function(type, data, tab) {
      var url = tab.url || (tab.contentDocument && tab.contentDocument.location
        ? tab.contentDocument.location.href : null);
      var host = AvastSP.bal.getHostFromUrl(url);
      switch (type) {
        case 'initMe':
          //if needed
        break;
        case 'unload':
          //if needed
        break;
        case 'DNTstate':
          sing.DNT.setWhiteList(data.type, data.ids, host, data.allow);
          break;
        case 'userVote':
          var v =
            {
              uri : data.url,
              vote: {
                rating: data.rating,
                flags : data.flags
              }
            };

          AvastSP.setVote(data.url, v);
          break;
        case 'messageBoxFeedback':
          var settings = AvastSP.bal.settings.get();
          switch(data.type) {
            case 'siteCorrect':
              //TODO - notify backend about the user selection
              settings.siteCorrect.declined[data.url_from] = data.ok;

              AvastSP.bal.aos.SC.reportTypoAccounting(data,
                data.ok ? AvastSP.bal.aos.SC.SC_MANUAL_REDIRECT :
                  AvastSP.bal.aos.SC.SC_MANUAL_REFUSED
              );

              if(data.ok){
                AvastSP.bs.tabRedirect(tab, data.url_to);

                if(data.auto) {
                  settings.features.siteCorrectAuto = true;
                }

                // make domain trackable
                this._ee.emit('message.setDomainTrackable', {host: data.brand});
              }
              sing.settings.set(settings);
            break;
            case 'phishing':
              if(data.ok){
                AvastSP.bs.tabRedirect(tab, AvastSP.PHISHING_REDIRECT);
              } else {
                settings.phishing.trusted[data.url_from] = true;
                sing.settings.set(settings);
              }
              break;
            default:
              this._ee.emit('message.messageBoxFeedback.' + data.type, data, tab);
          }
        break;
        case 'openSettings': // open settings page
          var optionsPage = AvastSP.bs.getLocalResourceURL('options.html');
          AvastSP.bs.openInNewTab(optionsPage);
          break;
        case 'resetSettings': // fallthrough !!!
          if (data.list === undefined) {
            var newSettings = this.getDefaultSettings();
            _alterDntFeaturesDefaults(newSettings);
            sing.settings.set(newSettings);
            sing.updateOldSettings();
            this.featureSettingChanged('dnt', false, true);
          }
          else {
            var def = this.getDefaultSettings(),
                current = sing.settings.get();

            for(var feature in data.list) {
              var key = data.list[feature];
              if (current.features[key] !== def.features[key]) {
                sing.featureSettingChanged(key, current.features[key], def.features[key]);
              }
              current.features[key] = def.features[key];
            }
            sing.settings.set(current);
          }
        case 'getSettingList':
          var ret = {
            message : 'settingList',
            data: {
              title: AvastSP.bs.getLocalizedString('title'),
              save: AvastSP.bs.getLocalizedString('settingsSave'),
              reset: AvastSP.bs.getLocalizedString('settingsReset'),
              list : []
            }
          };

          var settings = sing.settings.get();

          for(var feature in settings.features) {
            var val = settings.features[feature];
            var item = this.createSettingsItem(feature, val);

            // Hide an obsolete setting. Will be reenabled by the SP module only
            // in case the SP module is really registered and active.
            if (feature === 'safeShop') {
              item.show = false;
            }

            _.each(this._bal_modules, function(module) {
              if (typeof module.modifySettingsItem === 'function') {
                item = module.modifySettingsItem(feature, val, item);
              }
            }, this);

            if (item.show) { ret.data.list.push(item); }
          }

          back.messageTab(tab, ret);
        break;
        case 'saveSettings':
          var settings = sing.settings.get();

          for(var feature in data.list){
            var f = data.list[feature];
            var o = settings.features[f.key];
            settings.features[f.key] = f.active;
            if (o !== f.active) {
              sing.featureSettingChanged(f.key, o, f.active);
            }
          }
          sing.updateOldSettings();
          sing.settings.set(settings);
          break;
        case 'openInNewTab':
          AvastSP.bs.openInNewTab(data.url);
          break;
         case 'copyToClipboard':
          AvastSP.bs.copyToClipboard (data.text);
          break;
        default:
          // emit messages in specific namespace
          this._ee.emit('message.' + type, data, tab);
      }
    },

    createSettingsItem: function (feature, value) {
      var interm = false; // (value === -1); // ignoring intermed state
      var active = (interm) ? true : value; // treat intermed as feture is set
      return {
        name        : AvastSP.bs.getLocalizedString('settings' + feature),
        description : AvastSP.bs.getLocalizedString('settings' + feature + 'Desc'),
        key         : feature,
        active      : active,
        enabled     : true,
        show        : true,
        intermed    : interm
      };
    },

    /**
     * Detect pages where the extension will handle avast:// protocal URLs.
     * And it applies events to these links to trigger to extension specific functions.
     * Ie. avast://settings opens settings: .../options.html
     * @param {String} page URL
     * @param {Object} relevant tab to process the links
     */
    tabFixAosUrls: function(url, tab) {
      if (AOS_URLS_ENABLED_URLS.test(url)) {
        AvastSP.bs.accessContent(tab, {
          message : 'fixAosUrls',
          data: { actions : AOS_URLS_ACTIONS }
        });
      }
    },

    /**
     * Temporary storage
     * @type {Object}
     */
    cache: {
      map: {},
      add: function(itemKey, itemValue, key) {
        (key ? this.map[key] : this.map)[itemKey] = itemValue;
        return itemValue;
      },
      get: function(itemKey, key) {
        return (key ? this.map[key] : this.map)[itemKey];
      },
      contains: function(itemKey, key) {
        return (key ? this.map[key] : this.map).hasOwnProperty(itemKey);
      },
      delete: function(itemKey, key) {
        delete (key ? this.map[key] : this.map)[itemKey];
      },
      reset: function(key) {
        this.map[key] = {};
      }
    },
    /**
     * Persistent storage
     * @type {Object}
     */
    storage: {
      add: function(itemKey, itemValue) {
        localStorage.setItem(itemKey, JSON.stringify(itemValue));
        return itemValue;
      },
      get: function(itemKey, key) {
        var item = localStorage.getItem(itemKey);
        try {
          return JSON.parse(item);
        } catch (ex) {
          return {};
        }
      },
      contains: function(itemKey, key) {
        return localStorage.hasOwnProperty(itemKey);
      },
      delete: function(itemKey, key) {
        delete localStorage[itemKey];
      }
    },
    /**
     * Persistent Storage wrapper
     * @param  {String} key
     * @param  {Object} initializer - in case the key is not present in localStorage
     * @return {Object} - troughStorage instance with get and set
     */
    troughStorage: function(key, initializer) {
      var tmp = null;
      if(!sing.storage.contains(key))
        sing.storage.add(key,initializer || {});
      return {
        get: function() {
          return tmp || (tmp = sing.storage.get(key));
        },
        set: function(val) {
          tmp = val;
          sing.storage.add(key, val);
        }
      };
    },
    /**
     * Helper functions
     */
    isFirefox: function() {
      return sing.browser == 'Firefox';
    },
    getHostFromUrl: function(url) {
      if (!url) {
        return undefined;
      }

      var lcUrl = url.toLowerCase();

      if (lcUrl.toLowerCase().indexOf('http') != 0 ||
        lcUrl.toLowerCase().indexOf('chrome') == 0 ||
        lcUrl.toLowerCase().indexOf('data') == 0 ||
        lcUrl.toLowerCase() == 'about:newtab' ||
        lcUrl.toLowerCase() == 'about:blank')
      {
        return undefined;
      }

      var match = url.match(/:\/\/(www[0-9]?\.)?(.[^/:]+)/);
      return match.length > 2 ? match[2] : undefined;
    },
    getDomainFromHost: function(host){
      return host ? host.split('.').slice(-2).join('.') : undefined;
    },
    getDomainFromUrl: function(url) {
      return AvastSP.bal.getDomainFromHost(AvastSP.bal.getHostFromUrl(url));
    },
    jsonToString: function(obj) {
      var s = '';
      for(var key in obj) {
        if(typeof obj[key] == 'object') {
          s += key+'<br />';
          s += this.jsonToString(obj[key]);
        } else {
          s += key+': '+obj[key]+'<br />';
        }
      }

      return s;
    },
    tryParseJSON: function(obj) {
      try {
        return JSON.parse(obj);
      }
      catch(e) {
        return obj;
      }
    },
    tabClosed: function(tabId, host) {
      this.cache.delete('dnt_' + tabId);
    },
    defaultErrorHandler: function(obj) {
    },
    /**
     * WebRep Common Core
     * @type {Object}
     */
    WebRep: {
      /**
       * Creates default resources for building the UI
       * @return {Object}
       */
      getDefaults: function() {
        return {
            close  : AvastSP.bs.getLocalImageURL('icn_close.png'),
            btnLearn : AvastSP.bs.getLocalizedString('sideDetails').toUpperCase(),
            btnRate : AvastSP.bs.getLocalizedString('sideRate').toUpperCase(),
            btnDash : AvastSP.bs.getLocalizedString('sideDashboard').toUpperCase(),
            btnSettings : AvastSP.bs.getLocalizedString('sideSettings').toUpperCase(),
            rating : {
              headline: AvastSP.bs.getLocalizedString('ratingRate'),
              footer: AvastSP.bs.getLocalizedString('ratingNoRate'),
              close : AvastSP.bs.getLocalImageURL('icn_close_small.png'),
              elements: [
                {
                  text: AvastSP.bs.getLocalizedString('votePositive').toUpperCase(),
                  image: AvastSP.bs.getLocalImageURL('icnthumbsmall.png'),
                  color: '#32b576',
                  color_left: '#47cb83'
                },
                {
                  text: AvastSP.bs.getLocalizedString('voteNegative').toUpperCase(),
                  image: AvastSP.bs.getLocalImageURL('icnthumbdownsmall.png'),
                  color: '#d84753',
                  color_left: '#f7636f'
                }
              ],
              negative: {
                close : AvastSP.bs.getLocalImageURL('icn_close_small.png'),
                headline: AvastSP.bs.getLocalizedString('ratingNegative'),
                button: AvastSP.bs.getLocalizedString('ratingDone').toUpperCase(),
                elements: [
                  {
                    text: AvastSP.bs.getLocalizedString('pornography'),
                    data: 'pornography'
                  },
                  {
                    text: AvastSP.bs.getLocalizedString('violence'),
                    data: 'violence'
                  },
                  {
                    text: AvastSP.bs.getLocalizedString('gambling'),
                    data: 'gambling'
                  },
                  {
                    text: AvastSP.bs.getLocalizedString('drugs'),
                    data: 'drugs'
                  },
                  {
                    text: AvastSP.bs.getLocalizedString('illegal'),
                    data: 'illegal'
                  }
                ]
              }
            },
            thanks : {
              close : AvastSP.bs.getLocalImageURL('icn_close_small.png'),
              image: AvastSP.bs.getLocalImageURL('icn_checkbig.png'),
              headline: AvastSP.bs.getLocalizedString('thanksHeadline'),
              message: AvastSP.bs.getLocalizedString('thanksMessage'),
            },
          };
      },
      /**
       * Computes  WebRep data for a specific host
       * @param  {String} host
       * @return {Object} - data to be used with mustache.js templates for building the UI
       */
      compute: function(host) {
        var wrc = AvastSP.Cache.getNoTTL(host), color, ret = AvastSP.bal.WebRep.getDefaults();
        if(!wrc || !wrc.values){
          ret.icon  = AvastSP.bs.getLocalImageURL('icn_norating_big.png');
          ret.text  = AvastSP.bs.getLocalizedString('ratingTextUndefined');
          ret.color = '#a5abb2';
          ret.close = AvastSP.bs.getLocalImageURL('icn_close.png');
        }
        else {
          var val = wrc.values;
          switch(wrc.getRatingCategory()) {
            case AvastSP.RATING_GOOD:
              ret.icon = AvastSP.bs.getLocalImageURL('icn_thumbup_big.png');
              ret.text = AvastSP.bs.getLocalizedString('ratingTextPositive');
            break;
            case AvastSP.RATING_AVERAGE:
              ret.icon = AvastSP.bs.getLocalImageURL('icn_thumbright_big.png');
              ret.text = AvastSP.bs.getLocalizedString('ratingTextAverage');
            break;
            case AvastSP.RATING_BAD:
              ret.icon = AvastSP.bs.getLocalImageURL('icn_thumbdown_big.png');
              ret.text = AvastSP.bs.getLocalizedString('ratingTextBad');
            break;
          }

          ret.color = RATING_COLOR[wrc.getRatingCategory()];
          ret.details = {
            close : AvastSP.bs.getLocalImageURL('icn_close_small.png'),
            headline: AvastSP.bs.getLocalizedString('detailsHeadline'),
            elements: [
              {
                text: AvastSP.bs.getLocalizedString('detailsMalware' + ( val.block == 1 ? 'Yes' : 'No' )),
                image: AvastSP.bs.getLocalImageURL('icn_bug.png'),
                color: val.block == 1 ? '#f7636f' : '#47cc82',
                title: ''
              },
              {
                text: AvastSP.bs.getLocalizedString('detailsPhishing' + ( val.phishing ? 'Yes' : 'No' )),
                image: AvastSP.bs.getLocalImageURL('icn_eye.png'),
                color: val.phishing > 1 ? '#f7636f' : '#47cc82',
                title: ''
              },
              {
                text: AvastSP.bs.getLocalizedString('ratingCommunity',[AvastSP.bs.getLocalizedString('ratingLevel' + RATING_LEVEL[wrc.getRatingCategoryOld()])]),
                image: AvastSP.bs.getLocalImageURL('icn_thumblearn.png'),
                color: RATING_COLOR[wrc.getRatingCategoryOld()],
                title: val.rating
              }
            ]
          };
        }

        return ret;
      }
    },



    /* Wraps bal to register to submodule events */
    Core : {
      registerModuleListeners : function (ee) {
        // register for local Avast service
        ee.on('local.init', function(port) {
          sing.initLocalService(port);
        });
        ee.on('local.paired', function(guid, auid, hwid, uuid) {
          AvastSP.CONFIG.GUID = guid;
          AvastSP.CONFIG.AUID = auid;
          AvastSP.CONFIG.HWID = hwid;
          AvastSP.CONFIG.UUID = uuid;
        });
      }
    },

    /**
     * AvastSP.bal specific utilities.
     */
    utils : {
      /**
       * Retrieve localised strings into given data object
       * based on the string ids array.
       * @param {Object} data to load the strings to
       * @param {Array} identifiers of strings to load
       * @return {Object} updated data object
       */
      loadLocalizedStrings : function (data, stringIds) {
        return _.reduce (stringIds, function(res, stringId) {
          res[stringId] = AvastSP.bs.getLocalizedString(stringId);
          return res;
        }, data);
      },

      /**
       * Create local image url for given key/file map.
       * @param {Object} to add local URLs to
       * @param {Object} map key / image file to create the local URLs for
       * @return {Object} updated data object
       */
      getLocalImageURLs : function (data, imagesMap) {
        return _.reduce (imagesMap, function(res, image, key) {
          res[key] = AvastSP.bs.getLocalImageURL(image);
          return res;
        }, data);
      },

      /**
       * Generate random UID.
       */
      getRandomUID : function () {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,
          function(c) {
            var r = Math.random()*16|0, v = c == 'x' ? r : (r&0x3|0x8);
            return v.toString(16);
          }
        );
      }

    }, // utils

    /**
     * Set bal instance with local storage instance.
     * @param {Object} browser local storage instance
     */
    setLocalStorage : function (ls) {
      localStorage = ls;
    },

    /**
     * Check current version and compare with previous version stored.
     * If newer version detected open Avast Welcome page on avast web.
     * @param {Numeric} current extension caller Id
     */
    checkPreviousVersion : function (currentCallerId) {
      var settings = sing.settings.get();
      var previousCallerId = settings.current.callerId;
      if (previousCallerId !== currentCallerId) {
        var prevMinor = previousCallerId % 1000;
        var currMinor = currentCallerId % 1000;

        if ( SHOW_WELCOME_PAGE_RULE(prevMinor, currMinor) ) {
          AvastSP.bs.openInNewTab(AvastSP.AVAST_UPGRADE_PAGE_URL + currentCallerId);
        }

        // block DNT when updating to 7/8
        if (currMinor === 7 || currMinor === 8 || (currMinor > 8 && prevMinor < 7)) {
          _alterDntFeaturesDefaults(settings);
          this.featureSettingChanged('dnt', false, true);
        }

        // update saved callerId
        settings.current.callerId = currentCallerId;
        sing.settings.set(settings);
      }
    },

    /**
     * Stores user Id so it is available to subsequent requests and persisted in local storage.
     * @param {String} userid to store
     */
    storeUserId : function (userId) {
      var settings = sing.settings.get();
      settings.current.userId = userId;
      sing.settings.set(settings);
      sing.updateOldSettings(); // refresh settings accessible through AvastSP
    }

  }; // AvastSP.bal

  // Init the core module to register for event from sub-modules.
  AvastSP.bal.registerModule(AvastSP.bal.Core);

}).call(this, _, EventEmitter2);

/*******************************************************************************
 *  avast! Online Security plugin
 *  (c) 2014 Avast Corp.
 *
 *  Background Layer - SafePrice
 ******************************************************************************/

(function(AvastSP, _) {

  // Hide time for SafeShop settings - 24hs
  var SAFESHOP_HIDE_TIMEOUT = 24*60*60*1000;

  var SAS_STATUS = {
    NEW : 0,
    OPT_IN : 1,
    OPT_OUT : 2
  };

  var _safeShopInTab = {};

  /**
   * Parse SafeShop status featur into Avast Offers Proxy status values.
   */
  function parseSafeShopStatusFeature(safeShopVal) {
    return (safeShopVal === -1) ? SAS_STATUS.OPT_IN : //SAS_STATUS.NEW :
      (safeShopVal) ? SAS_STATUS.OPT_IN : SAS_STATUS.OPT_OUT;
  }

  (function (definition) {
    AvastSP.bal.registerModule({
      bootstrap : function (features) {
        return features.safePrice ? definition() : null;
      }
    });
  })(function () {

    AvastSP.bal.sp = _.extend( AvastSP.bal.SP || {}, {

      onUrlInfoResponse : function (url, response, tab, tabUpdate) {
        var safeShopData = response.values.safeShop;
        var tabId = AvastSP.bs.getTabId(tab);
        if (safeShopData && tabUpdate) {
          if (tabUpdate) { // wait until page complete
            _safeShopInTab[tabId] = safeShopData;
          } else {
            this.tabSafeShopCheck(tabId, tab, url, safeShopData);
            delete _safeShopInTab[tabId];
          }
        }
      },

      onPageComplete : function(tabId, tab, url) {
        var safeShopData = _safeShopInTab[tabId];
        if (safeShopData) {
          this.tabSafeShopCheck(tabId, tab, url, safeShopData);
          delete _safeShopInTab[tabId];
        }

        /* Check if request to retrieve domain made and retrive it from tab to make it trackable. */
        var requested = AvastSP.TabReqCache.get(tabId, 'dnt_retrieve_domain');
        if (requested) {
          var host = AvastSP.bal.getHostFromUrl(tab.url || tab.contentDocument.location.href);
          AvastSP.bal.emitEvent('message.setDomainTrackable', {host: host});
          AvastSP.TabReqCache.set(tabId, 'dnt_retrieve_domain', false);
        }
      },

      /**
       * Initiate page data check when safeShop selector received.
       * @param {String} page URL
       * @param {Object} urlInfo response
       * @param {Object} relevant tab to run the check
       */
      tabSafeShopCheck: function(tabId, tab, url, safeShopData) {
        var data = {
          message: 'checkSafeShop',
          data: {
            url: url,
            csl: JSON.parse(safeShopData.selector),
  //            coupons : (safeShopData.selector.indexOf("VoucherSearch") > -1),
            tabId: tabId,
          }
        };
        AvastSP.bs.accessContent(tab, data);
      },

      safeShopOffersFound: function(data, tab) {
        this.processSafeShopOffers(tab, data, function(tab, data) {
          AvastSP.bs.accessContent(tab, {
            message: 'showSafeShop',
            data: data
          });
        });
      },

      /**
       * Register a request to retrieve domain and enable to track it.
       */
      registerRetriveDomainRequest: function(tabId, tab) {
        AvastSP.TabReqCache.set(tabId, 'dnt_retrieve_domain', true);
      },

      safeShopFeedback: function (data,  tab) {
        var settings = AvastSP.bal.settings.get();
        switch(data.type) {
          case 'safeShopOptin':
            settings.features.safeShop = data.optin;
            AvastSP.bal.settings.set(settings);
            break;
          case 'safeShopSettings':
            var domain = data.domain;
            var timeout = (new Date()).getTime() + SAFESHOP_HIDE_TIMEOUT;
            switch(data.action) {
              case 'sas-block-coupons-domain':
                settings.safeShop.noCouponDomains[domain] = true;
                break;
              case 'sas-hide-domain':
                settings.safeShop.hideDomains[domain] = timeout;
                break;
              case 'sas-hide-all':
                settings.safeShop.hideAll = timeout;
                break;
            }
            AvastSP.bal.settings.set(settings);
            break;
          case 'offerRedirect':
            // open URL in new tab
            AvastSP.bs.openInNewTab (data.url, function (newTab) {
              AvastSP.bal.sp.registerRetriveDomainRequest(AvastSP.bs.getTabId(newTab), newTab);
              // report offer to backed
              new AvastSP.Query.SafeShopPickedOffer({
                originalRequest: data.originalRequest,
                offer: data.offer
              });
            });
            break;
          case 'couponRedirect':
            // report coupon to backed
            new AvastSP.Query.SafeShopPickedOffer({
              originalRequest: data.originalRequest,
              offer: data.coupon
            });
            break;

            /**
             * SafePrice Coupon was clicked
             * 1. Open the link in a new tab, wait for the redirects to finish (return to original domain)
             * 2. Insert new Bar with coupon_code and coupon_text
             */
          case 'safeShopCouponClick':
            AvastSP.bs.openInNewTab(data.url);


            if (AvastSP.Utils.getBrowserInfo().isFirefox()) {
              AvastSP.safeShopCouponActive = {
                id : AvastSP.bs.getTabId(tab),
                code : data.coupon.coupon_code,
                text : data.coupon.coupon_text,
                value : data.coupon.value,
                label : data.coupon.label,
                expire : data.coupon.coupon_expire_date,
                freeshipping : data.coupon.coupon_freeshipping
              };
            }else{
              AvastSP.bs.getActiveTab(function(tab){
                AvastSP.safeShopCouponActive = {
                  id : AvastSP.bs.getTabId(tab),
                  code : data.coupon.coupon_code,
                  text : data.coupon.coupon_text,
                  value : data.coupon.value,
                  label : data.coupon.label,
                  expire : data.coupon.coupon_expire_date,
                  freeshipping : data.coupon.coupon_freeshipping
                };
              });
            }
            break;
        }
      },

      /**
       * Display bar with the coupon instructions
       * @param  {[type]} tab    [description]
       * @param  {[type]} coupon [description]
       * @return {[type]}        [description]
       */
      tabSafeShopCoupon : function(tab, coupon){
          var urlDomain = AvastSP.bal.getDomainFromUrl(AvastSP.bs.getTabUrl(tab));

          var settingsLabelsProps = [urlDomain];

          AvastSP.bs.accessContent(tab, {
              message : "showSafeShopCoupon",
              data : {
                  coupon : coupon,
                  images : AvastSP.bal.utils.getLocalImageURLs({}, {
                      logo: 'sas_logo.png', drop: 'sas_drop.png', help: 'sas_help.png',
                      conf: 'sas_conf.png', close: 'icn_close.png', arrow: 'arrow.png'
                    }),
                  strings : AvastSP.bal.utils.loadLocalizedStrings( {}, [
                    'sasCouponCodeTitle', 'sasClickToCopy', 'sasCouponInstructionsTitle', 'sasNoCouponCode', 'sasCouponCodeCopied'
                  ]),
                  settings : _([
                      ['sas-hide-domain', 'sasOptionsDisableOnSite', settingsLabelsProps],
                      ['sas-hide-all', 'sasOptionsDisableAll', settingsLabelsProps]
                      ]).map(function(def) {
                        return {
                          actionId : def[0],
                          label : AvastSP.bs.getLocalizedString(def[1], def[2])
                        };
                      }).valueOf()
              }
          });
      },

      /**
       * Process safeShop offers returned for given tab.
       * @param {Object} tab to execute the SafeShop for
       * @param {Object} safeShop data retrieved
       * @param {Function} callback function to receive the prcessed data
       */
      processSafeShopOffers: function(tab, data, callback) {
        if(AvastSP.safeShopCouponActive){
            this.tabSafeShopCoupon(tab, AvastSP.safeShopCouponActive);
            delete AvastSP.safeShopCouponActive;
            return;
        }

        var settings = AvastSP.bal.settings.get();
        var features = settings.features;
        var urlDomain = AvastSP.bal.getDomainFromUrl(data.url);
        var status = parseSafeShopStatusFeature(features.safeShop);

        var VoucherSearch = _.any(data.csl.plugins, function(p) {return (p[0] === "VoucherSearch");});

        var queryOptions = {
          url: data.url,
          query: data.scan,
          status: status,
          VoucherSearch : VoucherSearch,
          country: 'de', // use country for testing only, otherwise GeoIP devised / 1st lvl domain name, 'us' for 'com'
          referrer: data.referrer,
          callback : function(offersResponse) {
            var timestamp = (new Date()).getTime();
            var domainTimeout = settings.safeShop.hideDomains[urlDomain] || 0;
            var allTimeout = settings.safeShop.hideAll || 0;

            if (!offersResponse.offers || offersResponse.offers.length === 0 ||
              domainTimeout > timestamp || allTimeout > timestamp)
            {
              return;
            }

            // group the offers by category: Product, Voucher,...
            var offers = _.groupBy(offersResponse.offers, function(item) {return item.category;});

            var safeShopBestTitle, safeShopBestButton, safeShopOffers, firstProduct, save;
            var products = _(offers['Product']).sortBy('price');

            if (products.size() > 0) {
              firstProduct = products.first().valueOf();
              if (firstProduct.saving) {
                // best product elsewhere
                safeShopBestTitle = AvastSP.bs.getLocalizedString('safeShopBestTitleCheaperFound');
                safeShopBestButton = AvastSP.bs.getLocalizedString('safeShopBestButtonSave',
                  ['', firstProduct.saving, firstProduct.affiliate]);
              } else {
                // best product on the page
                safeShopBestTitle = AvastSP.bs.getLocalizedString('safeShopBestTitleBestHere');
                safeShopBestButton = AvastSP.bs.getLocalizedString('safeShopBestButtonNextOffer',
                  [firstProduct.fprice, firstProduct.affiliate]);
              }
              safeShopOffers =  AvastSP.bs.getLocalizedString('safeShopOffers',
                    [products.size(), firstProduct.fprice]);
            }

            var dispProducts = products.valueOf();

            var coupons = offers['Voucher'] || [];

            var couponsSorted = _(coupons).sortBy(function(i) {return i.price || 0;}).reverse();
            var bestCoupon = coupons.length > 0 ? couponsSorted.first().valueOf() : null;


            var settingsLabelsProps = [urlDomain];
            var showHelp = (features.safeShop === -1) && (Math.floor(AvastSP.CONFIG.CALLERID/1000) !== 8);
            var showData = {
              domain : urlDomain,
              showHelp: showHelp,
              discreetSlider : !(AvastSP.Utils.getDomain(tab.url) ==
                  AvastSP.Utils.getDomain(AvastSP.TabReqCache.get(tab.id, 'referer'))) &&
                  !showHelp,
              discreet : !(data.scan && data.scan.title) && !showHelp,
              isNew : (features.safeShop === -1),
              products : dispProducts,
              firstProduct : firstProduct,
              coupon : [],
              coupons : couponsSorted.valueOf(),
              couponsTitle : (coupons.length > 0 && dispProducts.length <= 0),
              bestCouponUrl: ( bestCoupon ? bestCoupon.url : '' ),
              // hasOffers : dispOffers.length > 0,
              // hasCoupons : coupons.length > 0,
              images : AvastSP.bal.utils.getLocalImageURLs({}, {
                  logo: 'sas_logo.png', drop: 'sas_drop.png', help: 'sas_help.png',
                  conf: 'sas_conf.png', close: 'icn_close.png', arrow: 'arrow.png'
                }),
              strings : AvastSP.bal.utils.loadLocalizedStrings( {
                  safeShopBestTitle: safeShopBestTitle,
                  safeShopBestButton: safeShopBestButton,
                  safeShopOffers: safeShopOffers,
                  safeShopCoupons: AvastSP.bs.getLocalizedString('safeShopCoupons',
                    [coupons.length]),
                  safeShopCouponsTitle: AvastSP.bs.getLocalizedString('sasCouponsTitle',
                    [coupons.length]),
                  safeShopCouponsSaving: (bestCoupon && bestCoupon.value && bestCoupon.value.length>0) ?
                    AvastSP.bs.getLocalizedString('sasCouponsSaving', [bestCoupon.value]) : ''
                }, [
                  'sasHintBestOffer', 'sasHintBestOfferDesc',
                  'sasHintOtherOffers', 'sasHintOtherOffersDesc',
                  'sasHintCoupons', 'sasHintCouponsDesc',
                  'sasHintSettings', 'sasHintSettingsDesc',
                  'sasPromoNewBadge', 'sasProductName',
                  'sasPromoDescription', 'sasPromoAction',
                  'sasShippingLabel', 'sasCouponCodeTitle',
                  'sasClickToCopy', 'sasCouponInstructionsTitle',
                  'sideSettings', 'sasAddOnInfo'
                ]),
              // list settings actions
              settings : _([
                //['sas-block-coupons-domain', 'sasOptionsNeverShowOnSite', settingsLabelsProps],
                ['sas-hide-domain', 'sasOptionsDisableOnSite', settingsLabelsProps],
                ['sas-hide-all', 'sasOptionsDisableAll', settingsLabelsProps]
                ]).map(function(def) {
                  return {
                    actionId : def[0],
                    label : AvastSP.bs.getLocalizedString(def[1], def[2])
                  };
                }).valueOf(),
              safeShopData: data,
              safeShopOffers: dispProducts,
              safeShopCoupons: coupons,
              showSettings: (Math.floor(AvastSP.CONFIG.CALLERID/1000) !== 8),
              originalQuery: {
                url: data.url,
                query: data.scan,
                status: status
              }
            };
            callback(tab, showData);
          }
        };

        new AvastSP.Query.SafeShopOffer(queryOptions); // query Avast Offers Proxy

      }, // processSafeShopOffers

      /* Register SafePrice Event handlers */
      registerModuleListeners: function(ee) {
        ee.on('urlInfo.response', AvastSP.bal.sp.onUrlInfoResponse.bind(AvastSP.bal.sp));
        ee.on('page.complete', AvastSP.bal.sp.onPageComplete.bind(AvastSP.bal.sp));
        ee.on('message.safeShopFeedback', AvastSP.bal.sp.safeShopFeedback.bind(AvastSP.bal.sp));
        ee.on('message.safeShopOffersFound', AvastSP.bal.sp.safeShopOffersFound.bind(AvastSP.bal.sp));
      },

      modifySettingsItem: function(feature, value, item) {
        // Ensure that the setting is shown on options.html page
        if (feature === 'safeShop') {
          item.show = true;
        }
        return item;
      },

      /**
       * Return SafePrice related default settings.
       */
      getModuleDefaultSettings: function() {
        return {
          safeShop : {
            noCouponDomains : {}, // {"domain":true}
            hideDomains : {}, // {"domain":timeout}
            hideAll : 0 // hide until
          },
          features : {
            safeShop : -1 // NEW (-1), true = opt-in (default), false = opt-out
          }
        };
      }

    }); // SP

    return AvastSP.bal.sp;
  });

}).call(this, AvastSP, _);
