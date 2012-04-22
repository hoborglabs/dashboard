#!/bin/bash
currentDir=`pwd`

function createFolders {
	out 'Creating folder structure'
	mkdir -p $currentDir/config
	mkdir -p $currentDir/data
	mkdir -p $currentDir/widgets
	mkdir -p $currentDir/templates
	mkdir -p $currentDir/htdocs
	mkdir -p $currentDir/vendors
}

function download {
	out 'Downloading vendors'
	cd $currentDir/vendors
	echo '  downloading dashboard.phar'

	if [ -e "$currentDir/vendors/dashboard.phar" ]
	then
		rm -f dashboard.phar && curl -sO http://get.hoborglabs.com/dashboard/dashboard.phar
	else
		curl -sO http://get.hoborglabs.com/dashboard/dashboard.phar
	fi

	echo '  downloading dashboard-assets.tgz'
	rm -f dashboard-assets.tgz && curl -sO http://get.hoborglabs.com/dashboard/dashboard-assets.tgz

	echo '  unpacking dashboard-assets.tgz'
	mkdir -p assets
	tar -xzf dashboard-assets.tgz -C $currentDir/htdocs/
}

function install {
	out 'Installing'
	cd $currentDir/htdocs
	rm -f dashboard.php && curl -sO https://raw.github.com/gist/1512137/dashboard.php
}

function out {
	echo "  $1"
}

createFolders;
download;
install;