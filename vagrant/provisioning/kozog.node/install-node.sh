#!/bin/sh
##############################################################
# Run as root installs latest Node.js
##############################################################
# check for node

$(which node > /dev/null 2>&1)
FOUND_NODE=$?

if [ "${FOUND_NODE}" -eq '0' ]; then
    echo 'Node installed moving forward'
    exit 0;
fi

# Update System
echo "System Update"
apt-get -y update
echo "Update completed"

LATEST_NODE=`wget -qO- http://nodejs.org | grep -o -E 'http\:\/\/nodejs\.org/dist/.+tar\.gz'`
LATEST_NODE_FILE=`echo $LATEST_NODE | grep -o -E 'node-v[0-9]+(\.[0-9]+)+'`
LATEST_NODE_VER=`echo $LATEST_NODE_FILE | grep -o -E '[0-9]+(\.[0-9]+)+'`

echo $LATEST_NODE_VER;

# Install help app
apt-get -y install libssl-dev git-core pkg-config build-essential curl gcc g++ checkinstall

echo "Download Node.js - $LATEST_NODE_VER"
mkdir /tmp/node-install
cd /tmp/node-install
wget $LATEST_NODE -O node.tar.gz
tar -zxf node.tar.gz 
echo "Node.js download & unpack completed"

# Install Node.js
echo Install Node.js
cd $LATEST_NODE_FILE

./configure && make && checkinstall --install=yes --pkgname=nodejs --pkgversion "$LATEST_NODE_VER" --default
echo "Node.js install completed"
