INC=-I/bin
PATH:=${PATH}:/bin
HOME=$(shell pwd)
USER=$(whoami)

all: install

install: remove-cruft database-install tools-install php-install caffeine-install

deploy: stop-monitors grub-config fsck-config apport-config lightdm-config logrotate-config user-paths user-config php-path link-release activate-bin install-crontab restart-services start-monitors

stop-monitors:
	touch /tmp/dat_deploying.lock

start-monitors:
	rm /tmp/dat_deploying.lock

grub-config:
	test -d /etc/grub || sudo mkdir /etc/grub
	sudo cp config/etc/grub/* /etc/grub/
	sudo chown root:root /etc/grub/00_header
	sudo chown root:root /etc/grub/10_linux
	sudo cp config/etc/default/grub /etc/default/
	sudo chown root:root /etc/default/grub
	sudo update-grub

fsck-config:
	sudo cp config/etc/default/rcS /etc/default/
	sudo chown root:root /etc/default/rcS

apport-config:
	test -d /etc/default || sudo mkdir /etc/default
	sudo cp config/etc/default/apport /etc/default/
	sudo chown root:root /etc/default/apport

lightdm-config:
	test -d /etc/lightdm || sudo mkdir /etc/lightdm
	sudo cp config/etc/lightdm/lightdm.conf /etc/lightdm/
	sudo chown root:root /etc/lightdm/lightdm.conf

logrotate-config:
	test -d /etc/logrotate.d || sudo mkdir /etc/logrotate.d
	sudo cp config/etc/logrotate.d/lighttpd /etc/logrotate.d/
	sudo chown root:root /etc/logrotate.d/lighttpd

user-paths:
	sudo mkdir -p ~/.config/autostart
	sudo chown -R dat:dat ~/.config

user-config: user-paths
	sudo rm -f ~/.config/autostart/caffeine.desktop
	cp $(HOME)/config/user/.config/autostart/caffeine.desktop ~/.config/autostart/caffeine.desktop
	sudo rm -rf ~/.config/caffeine
	cp -R $(HOME)/config/user/.config/caffeine ~/.config/caffeine
	sudo rm -rf ~/.config/gtk-3.0
	cp -R $(HOME)/config/user/.config/gtk-3.0 ~/.config/gtk-3.0
	sudo rm -f ~/.gtkrc-2.0
	cp $(HOME)/config/user/.gtkrc-2.0 ~/
	sudo rm -f ~/.bash_aliases
	cp $(HOME)/config/user/.bash_aliases ~/.bash_aliases
	sudo rm -f ~/.bash_profile
	cp $(HOME)/config/user/.bash_profile ~/.bash_profile

link-release:
	rm /dat/releases/player/current
	ln -s $(HOME) /dat/releases/player/current

activate-bin:
	chmod u+x bin/*

install-crontab:
	crontab $(HOME)/config/crontab.txt

restart-services:
	sudo $(HOME)/bin/web restart
	sudo su - dat $(HOME)/bin/player restart

remove-cruft:
	sudo apt-get update
	sudo apt-get -y purge totem totem-common totem-mozilla totem-plugins
	sudo apt-get -y purge thunderbird thunderbird-globalmenu thunderbird-gnome-support thunderbird-locale-en thunderbird-locale-en-gb thunderbird-locale-en-us
	sudo apt-get -y purge rhythmbox rhythmbox-data rhythmbox-mozilla rhythmbox-plugin-cdrecorder rhythmbox-plugin-magnatune rhythmbox-plugin-zeitgeist rhythmbox-plugins
	sudo apt-get -y purge mahjongg
	gconftool -s --type bool /apps/update-notifier/auto_launch false
	sudo apt-get autoremove
	sudo chown $(USER) /etc/default/apport && echo enabled=0 > /etc/default/apport && sudo chown root /etc/default/apport

ssh:
	sudo apt-get install openssh openssh-server -y

database-install:
	sudo mkdir -p /dat/local/db
	sudo chown dat /dat/local/db
	test -f /dat/local/db/player.db || (touch /dat/local/db/player.db && chmod 777 /dat/local/db/player.db)
	sudo apt-get install -y sqlite3

tools-install:
	sudo apt-get purge ubuntuone-client -y
	sudo apt-get install xdotool -y
	sudo apt-get install curl -y
	sudo apt-get install x11vnc -y
	sudo apt-get install vim -y
	sudo apt-get install autossh -y

php-install:
	sudo apt-get install lighttpd -y
	sudo apt-get install libcurl4-openssl-dev -y
	sudo apt-get install php5-cli php5-curl php5-cgi php5-sqlite php-pear -y

caffeine-install:
	sudo add-apt-repository ppa:caffeine-developers/ppa -y
	sudo apt-get update
	sudo apt-get install caffeine -y

php-path:
	sudo rm -f /etc/php5/cgi/php.ini
	sudo ln -s $(HOME)/../config/php/php.ini /etc/php5/cgi/php.ini
	sudo rm -f /etc/php5/cli/php.ini
	sudo ln -s $(HOME)/../config/php/php.ini /etc/php5/cli/php.ini

install-folders:
	sudo mkdir -p /dat/logs /dat/local/logs /dat/local/tmp /dat/local/assets /dat/local/db /dat/releases/player
	sudo chown -R $(USER) /dat
	sudo chmod -R 777 /dat
