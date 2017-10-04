/*******************************************************************************
 *
 *  avast! Online Security plugin
 *  (c) 2013 Avast Corp.
 *
 *  @author: Lucian Corlaciu
 *
 *  Injected specifics - Mozilla Firefox - non-privileged
 *
 ******************************************************************************/

if (typeof AvastSP == 'undefined') { AvastSP = {}; }

AvastSP.bs = {
  init: function() {
    var ial = AvastSP.ial.init(this); //AvastSP.ial instance - browser agnostic - non-privileged
    self.port.on('message', function(message) {
      ial.messageHub(message.message, message.data);
    });
  },
  messageHandler: function(functionName, data) {
    data = data || {};
    data.message = functionName;

    self.port.emit('message', data);
  },
  getLocalImageURL: function(file) {
    return 'chrome://sp/content/common/skin/img/'+ file;
  },
  getLocalResourceURL: function(file) {
    return 'chrome://sp/content/'+ file;
  },
  triggerSettingsPage: function() {
    AvastSP.bs.messageHandler('triggerSettingsPage', { page: AvastSP.bs.getLocalResourceURL('options.html')});
  }
};

AvastSP.bs.init();
