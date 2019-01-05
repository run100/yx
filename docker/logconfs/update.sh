#!/bin/bash

ALIYUNLOG=`which aliyunlog`
PROJID=$1

if [[ "$ALIYUNLOG" == "" ]]; then
    echo "需要安装aliyun-log-cli， 参考: https://github.com/aliyun/aliyun-log-cli/blob/master/README_CN.md"
    exit 1
fi

if [[ "$PROJID" == "" ]]; then
    echo "参数1 需要提供 Project名称"
    exit 1
fi

$ALIYUNLOG log update_logtail_config --project_name=$PROJID --config_detail=file://./laravel.json
$ALIYUNLOG log update_logtail_config --project_name=$PROJID --config_detail=file://./phperr.json
$ALIYUNLOG log update_logtail_config --project_name=$PROJID --config_detail=file://./accesslog.json


$ALIYUNLOG log update_index --project_name=$PROJID --logstore_name=acslog-zhuanti-accesslog --index_detail=file://./accesslog_index.json
$ALIYUNLOG log update_index --project_name=$PROJID --logstore_name=acslog-zhuanti-phperr --index_detail=file://./phperr_index.json
$ALIYUNLOG log update_index --project_name=$PROJID --logstore_name=acslog-zhuanti-laravel --index_detail=file://./laravel_index.json