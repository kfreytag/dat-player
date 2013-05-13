CENTOS = $(shell grep -i centos /etc/issue)
UBUNTU = $(shell grep -i ubuntu /etc/issue)
GMTK_VERSION = 1.0.6

ifndef GMTK_VERSION
	SVN_PATH = trunk
	VERSION = trunk
else
	SVN_PATH = tags/v$(GMTK_VERSION)
	VERSION = $(GMTK_VERSION)
endif

ifneq ($(strip $(CENTOS)),)
	PACKAGE_MANAGER = yum
endif

ifneq ($(strip $(UBUNTU)),)
	PACKAGE_MANAGER = apt-get
endif

WORKING = $(shell pwd)
SRC = $(WORKING)/src
BUILD = $(WORKING)/build
BIN = /dat/bin

install: paths-install tools-install mplayer-install install-gmtk install-gnome-mplayer install-gecko-mediaplayer

paths-install:
	test -d $(BUILD) || mkdir -p $(BUILD)

tools-install:
	sudo $(PACKAGE_MANAGER) -y update
	sudo $(PACKAGE_MANAGER) -y install build-essential git subversion yasm gtk-doc-tools libglib2.0-dev intltool libgtk2.0-dev alsa-base libasound2 libasound2-dev libgconf2-dev libnspr4 libnspr4-dev firefox-dev dbus libdbus-1-dev libdbus-glib-1-2 libdbus-glib-1-dev
	sudo $(PACKAGE_MANAGER) -y install ubuntu-restricted-extras

mplayer-install: paths-install tools-install
	sudo $(PACKAGE_MANAGER) -y purge gnome-mplayer gecko-mediaplayer ffmpeg mencoder mplayer
	sudo $(PACKAGE_MANAGER) -y install ffmpeg mencoder mplayer
	sudo $(PACKAGE_MANAGER) -y install ubuntu-restricted-extras

install-gmtk: paths-install
	sudo $(PACKAGE_MANAGER) -y install libpulse-dev libpulse-mainloop-glib0 libpulse0
	cp -R $(SRC)/gmtk-$(VERSION) $(BUILD)/
	cd $(BUILD)/gmtk-$(VERSION) && chmod u+x *.sh configure && \
		./configure --prefix=/usr --with-alsa --disable-gtk3 && \
		make && \
		sudo make install
	touch $@

install-gnome-mplayer: paths-install
	cp -R $(SRC)/gnome-mplayer-$(VERSION) $(BUILD)/
	cd $(BUILD)/gnome-mplayer-$(VERSION) && chmod u+x *.sh configure mkinstalldirs && \
		./configure --prefix=/usr --with-dbus --with-alsa --disable-gtk3 && \
		make && \
		sudo make install
	touch $@

install-gecko-mediaplayer:
	sudo $(PACKAGE_MANAGER) -y install libcurl4-gnutls-dev
	test -L /usr/lib/pkgconfig/mozilla-xpcom.pc || sudo ln -s /usr/lib/pkgconfig/mozilla-plugin.pc /usr/lib/pkgconfig/mozilla-xpcom.pc
	cp -R $(SRC)/gecko-mediaplayer-$(VERSION) $(BUILD)/
	cd $(BUILD)/gecko-mediaplayer-$(VERSION) && chmod u+x *.sh configure mkinstalldirs && \
		./configure --prefix=/usr --with-plugin-dir=/usr/lib/mozilla/plugins && \
	    make && \
		sudo make install
	touch $@

clean: clean-gmtk clean-gnome-mplayer clean-gecko-mediaplayer
	rm -f *-install

clean-gmtk:
	rm -rf $(BUILD)/gmtk-$(VERSION)
	sudo rm -rf /usr/share/locale/*/LC_MESSAGES/gmtk.mo
	sudo rm -rf /usr/share/doc/gmtk
	sudo rm -rf /usr/include/gmtk
	sudo rm -rf /usr/lib/libgmtk.*
	sudo rm -rf /usr/lib/pkgconfig/gmtk.*
	sudo rm -rf /usr/lib/libgmlib.*
	sudo rm -rf /usr/lib/pkgconfig/gmlib.*

clean-gnome-mplayer:
	rm -rf $(BUILD)/gnome-mplayer-$(VERSION)
	rm -rf /home/dat/.cache/gnome-mplayer
	rm -rf /home/dat/.config/gnome-mplayer
	sudo rm -f /usr/share/gnome-control-center/default-apps/gnome-mplayer.xml
	sudo rm -f /usr/share/glib-2.0/schemas/apps.gnome-mplayer*
	sudo rm -f /usr/share/locale/*/LC_MESSAGES/gnome-mplayer.mo
	sudo rm -rf /usr/bin/gnome-mplayer
	sudo rm -rf /usr/share/doc/gnome-mplayer
	sudo rm -rf /usr/share/applications/gnome-mplayer.desktop

clean-gecko-mediaplayer:
	rm -rf $(BUILD)/gecko-mediaplayer-$(VERSION)
	sudo rm -f /usr/share/glib-2.0/schemas/apps.gecko-mediaplayer.preferences.gschema.xml
	sudo rm -f /usr/share/locale/*/LC_MESSAGES/gecko-mediaplayer.mo
	sudo rm -rf /usr/share/doc/gecko-mediaplayer
	sudo rm -rf /usr/lib/mozilla/plugins/gecko*
	sudo rm -rf /usr/lib/mozilla/plugins/gecko*
