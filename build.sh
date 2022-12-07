#!/bin/bash

# shellcheck disable=SC2006
dirdate=`date +%Y%m%d`
base_dir=`pwd`
kunlun_server_dir=$base_dir/KunlunMonitor
kunlun_client_dir=$base_dir/KunlunXPanel
build_dir=$base_dir/buid/$dirdate/platform
#echo $base_dir
#echo $kunlun_client_dir
#echo $kunlun_server_dir
#echo $base_dir
#echo $build_dir

mkdir -p "$build_dir"
mkdir -p "$build_dir"/KunlunXPanel
mkdir -p "$build_dir"/KunlunMonitor

# build node js
# shellcheck disable=SC2164
cd "$kunlun_client_dir"
npm run build:prod

#copy file
# shellcheck disable=SC2164
cd "$base_dir"
cp -r -v  "$kunlun_client_dir"/dist/* "$build_dir"/KunlunXPanel
cp -r -v  "$kunlun_server_dir"/* "$build_dir"/KunlunMonitor

# zip file


