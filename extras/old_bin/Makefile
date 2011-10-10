#
# Makefile
#
# Copyright (C) 2007  VICIDIAL-astGUIclient <vicidial@gmail.com>  LICENSE: GPLv2
#
# CHANGES
# 71121-1430 - Initial release.
# 71130-2137 - Remove install-VDconfig section and replace with install-common.
# 71201-0358 - Use sh to run VDconfig.
#
#
# methods:
#   make menuconfig
#      Runs graphical configuration.
#
#   make defaultconfig
#      Creates a default install config file.
#
#   make clean
#      Removes previous install config.
#
#   make install
#      Install Non-Web, Web and documents.
#
#   make install-nonweb
#      Only install executables and other Non-Web components.
#
#   make install-web
#      Only install Web components.
#
#   make install-docs
#      Installs documents to /usr/share/doc/astGUIclient-(version)
#
#   make install-asterisk-sample-config
#      Installs sample configs in /etc/asterisk, backing-up existing files.


.PHONY : defaultconfig menuconfig .install .install-nonweb install-web install install-nonweb install-web install-docs install-asterisk-sample-config clean .install-complete .install-common install-asterisk-sample-configs
VDconfig := VDconfig
version  := $(shell cat version)

menuconfig:
	@sh $(VDconfig)

clean:
	rm -f .agc.config

.agc.config:
	@echo "########################################################"
	@echo "###"
	@echo "### \".agc.config\" file not found."
	@echo "### Running configuration and just using defaults."
	@echo "###"
	@echo "########################################################"
	@sh $(VDconfig) --no-menu

defaultconfig: .agc_config
	@sh $(VDconfig) --no-menu

# The following, install, install-nonweb and install-web shell-out to
# run VDconfig which set the path variables and runs make with .install*
install: .agc.config
	@sh $(VDconfig) --env-make .$@

install-nonweb: .agc.config
	@sh $(VDconfig) --env-make .$@

install-web: .agc.config
	@sh $(VDconfig) --env-make .$@


# The following, .install, .install-nonweb and .install-web are the actual
# methods used to install
.install: .install-nonweb .install-web install-docs .install-common .install-complete

.install-common:
	@echo "Installing VDconfig script..."
	@install -d -m 755 $(DESTDIR)$(PATHhome)
	@install -p -m 755 $(VDconfig) $(DESTDIR)$(PATHhome)/VDconfig
	@install -d -m 755 $(DESTDIR)/usr/bin
	@ln -fs $(PATHhome)/VDconfig $(DESTDIR)/usr/bin/VDconfig
	@echo "Installing astGUIclient configuration to $(DESTDIR)/etc/astguiclient.conf..."
	@install -d -m 755 $(DESTDIR)/etc
	@install -p -m 644 ./.agc.config $(DESTDIR)/etc/astguiclient.conf
	@echo "Creating log directory $(DESTDIR)$(PATHlogs)..."
	@install -d -m 755 $(DESTDIR)$(PATHlogs)

.install-nonweb: .install-common
	@echo "Installing non-web components..."
	@install -d -m 755 $(DESTDIR)$(PATHhome)/libs/Asterisk
	@install -d -m 755 $(DESTDIR)$(PATHhome)/LEADS_IN/DONE
	@install -d -m 755 $(DESTDIR)$(PATHmonitor)
	@install -d -m 755 $(DESTDIR)$(PATHDONEmonitor)/ORIG
	@install -d -m 755 $(DESTDIR)$(PATHagi)
	@install -d -m 755 $(DESTDIR)$(PATHsounds)
	@install -p -m 755 ./bin/* $(DESTDIR)$(PATHhome)
	@install -p -m 755 ./extras/Asterisk.pm $(DESTDIR)$(PATHhome)/libs
	@install -p -m 755 ./extras/Asterisk/* $(DESTDIR)$(PATHhome)/libs/Asterisk
	@install -p -m 644 ./extras/GMT_USA_zip.txt $(DESTDIR)$(PATHhome)
	@install -p -m 644 ./extras/phone_codes_GMT.txt $(DESTDIR)$(PATHhome)
	@install -p -m 644 ./extras/MySQL_AST_CREATE_tables.sql $(DESTDIR)$(PATHhome)
	@install -p -m 755 ./agi/* $(DESTDIR)$(PATHagi)
	@install -p -m 644 ./sounds/* $(DESTDIR)$(PATHsounds)
	
.install-web: .install-common
	@echo "Installing web components in $(DESTDIR)$(PATHweb)..."
	@install -d -m 777 $(DESTDIR)$(PATHweb)/agc/images
	@install -d -m 777 $(DESTDIR)$(PATHweb)/vicidial/ploticus
	@install -d -m 777 $(DESTDIR)$(PATHweb)/vicidial/agent_reports
	@install -d -m 777 $(DESTDIR)$(PATHweb)/vicidial/server_reports
	@install -p -m 755 ./www/agc/*.php $(DESTDIR)$(PATHweb)/agc
	@install -p -m 644 ./www/agc/images/* $(DESTDIR)$(PATHweb)/agc/images
	@install -p -m 755 ./www/vicidial/*.php $(DESTDIR)$(PATHweb)/vicidial
	@install -p -m 755 ./www/vicidial/*.pl $(DESTDIR)$(PATHweb)/vicidial
	@install -p -m 644 ./www/vicidial/*.gif $(DESTDIR)$(PATHweb)/vicidial

install-asterisk-sample-configs: install-asterisk-sample-config

install-asterisk-sample-config:
	@echo "Installing sample configs in $(DESTDIR)/etc/asterisk..."
	@install -d -m 777 $(DESTDIR)/etc/asterisk
	@install -b -p -m 644 ./docs/conf_examples/extensions.conf.sample $(DESTDIR)/etc/asterisk/extensions.conf
	@install -b -p -m 644 ./docs/conf_examples/meetme.conf.sample $(DESTDIR)/etc/asterisk/meetme.conf
	@install -b -p -m 644 ./docs/conf_examples/manager.conf.sample $(DESTDIR)/etc/asterisk/manager.conf
	@install -b -p -m 644 ./docs/conf_examples/musiconhold.conf.sample $(DESTDIR)/etc/asterisk/musiconhold.conf
	@install -b -p -m 644 ./docs/conf_examples/voicemail.conf.sample $(DESTDIR)/etc/asterisk/voicemail.conf
	@install -b -p -m 644 ./docs/conf_examples/sip.conf.sample $(DESTDIR)/etc/asterisk/sip.conf
	@install -b -p -m 644 ./docs/conf_examples/logger.conf.sample $(DESTDIR)/etc/asterisk/logger.conf
	@install -b -p -m 644 ./docs/conf_examples/iax.conf.sample $(DESTDIR)/etc/asterisk/iax.conf
	@install -b -p -m 644 ./docs/conf_examples/dnsmgr.conf.sample $(DESTDIR)/etc/asterisk/dnsmgr.conf
	
install-docs:
	@echo "Installing documents in $(DESTDIR)/usr/share/doc/astGUIclient-$(version)..."
	@install -d -m 755 $(DESTDIR)/usr/share/doc/astGUIclient-$(version)/conf_examples
	@install -p -m 644 ./docs/*.txt $(DESTDIR)/usr/share/doc/astGUIclient-$(version)
	@install -p -m 644 ./docs/conf_examples/* $(DESTDIR)/usr/share/doc/astGUIclient-$(version)/conf_examples

.install-complete:
	@echo
	@echo
	@echo
	@echo "#################################################################"
	@echo "###                                                          ####"
	@echo "###                 VICIDIAL-astGUIclient                    ####"
	@echo "###                 INSTALLATION COMPLETE                    ####"
	@echo "###                                                          ####"
	@echo "###   Please continue with the steps in SCRATH_INSTALL.txt   ####"
	@echo "###                                                          ####"
	@echo "###   Run /usr/bin/VDconfig to modify the configuration.     ####"
	@echo "###                                                          ####"
	@echo "#################################################################"
