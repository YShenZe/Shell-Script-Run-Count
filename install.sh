#!/bin/bash

echo "\033[31m不是哥们?网上脚本你是真敢运行啊\033[0m"
sleep 1
echo "\033[31m不是哥们?网上脚本你是真敢运行啊\033[0m"
sleep 1
echo "\033[31m不是哥们?网上脚本你是真敢运行啊\033[0m"
sleep 1
echo "梦泽QQ群号：940994905"
sleep 1
system_info_file="$HOME/system_info.txt"
sleep 1
echo "系统信息文件已创建:\n $system_info_file"
{
    echo "===== 系统信息 ====="
    echo "设备型号: $(getprop ro.product.model)"
    echo "Android版本: $(getprop ro.build.version.release)"
    echo "Termux版本: $(termux-info | grep 'Termux version')"
    echo "内核版本: $(uname -r)"
    echo "CPU信息:"
    cat /proc/cpuinfo
    echo "内存信息:"
    cat /proc/meminfo
    echo "存储信息:"
    df -h
} > "$system_info_file"

echo "系统信息已写入:\n $system_info_file"
