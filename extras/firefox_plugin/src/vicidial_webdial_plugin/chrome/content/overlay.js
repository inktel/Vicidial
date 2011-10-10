var vicidial_webdial_plugin = {
  onLoad: function() {
    this.initialized = true;
    this.strings = document.getElementById("vicidial_webdial_plugin-strings");
  },

  onMenuItemCommand: function(e) {
    var promptService = Components.classes["@mozilla.org/embedcomp/prompt-service;1"]
                                  .getService(Components.interfaces.nsIPromptService);
    promptService.alert(window, this.strings.getString("helloMessageTitle"),
                                this.strings.getString("helloMessage"));
  },

  onToolbarButtonCommand: function(e) {
    vicidial_webdial_plugin.onMenuItemCommand(e);
  }
};
