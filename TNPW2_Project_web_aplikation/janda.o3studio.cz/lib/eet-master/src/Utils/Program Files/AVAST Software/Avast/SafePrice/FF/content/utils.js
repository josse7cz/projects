var BUTTON_RATING_ICONS = ["icn_ff_exttop.png", "icn_ff_exttop_green.png", "icn_ff_exttop_orange.png", "icn_ff_exttop_red.png"];
var DEFAULT_ICON_WIDTH = 24;
var DEFAULT_ICON_HEIGHT = 20;

var Utils = {
  button: {
    _button: null,
    _tabsDetails : {},

    setButton: function(button) {
      this._button = button
    },

    _getTabDetail: function(tabId) {
      if (!this._tabsDetails[tabId]) {
        this._tabsDetails[tabId] = {};
      }
      return this._tabsDetails[tabId];
    },

    _getIconFile: function(rating) {
      return './skin/img/' + BUTTON_RATING_ICONS[(rating > 0 && rating < BUTTON_RATING_ICONS.length) ? rating : 0];
    },

    setBadgeTextColor: function(tab, text, color) {
      var tabDetail = this._getTabDetail(tab.id);
      tabDetail.badgeText = text;
      tabDetail.badgeColor = color;

      this._button.state(tab, {
        icon: this._getIconFile(tabDetail.iconRating),
        badge: tabDetail.badgeText,
        badgeColor: tabDetail.badgeColor
      });
    },
    setButtonIconRating: function(tab, rating) {
      var tabDetail = this._getTabDetail(tab.id);
      tabDetail.iconRating = rating;

      this._button.state(tab, {
        icon: this._getIconFile(tabDetail.iconRating),
        badge: tabDetail.badgeText,
        badgeColor: tabDetail.badgeColor
      });
    },
    resetButtonBadge: function(tab) {
      this._tabsDetails[tab.id] = {};
      this._button.state(tab, {
        icon: this._getIconFile(0)
      });
    },
    resetTabDetails: function() {
      this._tabsDetails = {};
    }
  } // Utils.button
};

module.exports = Utils;
