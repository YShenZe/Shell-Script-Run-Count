API_URL="api地址?action=increment" # 定义api链接，问好后面的千万别删

TOKEN=" " # 定义你获得的Token
AUTH_HEADER=" " # 定义你获得的auth_header

response=$(curl -s -H "Token: $TOKEN" -H "Authorization: $AUTH_HEADER" "$API_URL")
run_count=$(echo "$response" | grep -oP '"count":\K\d+')

echo "本脚本运行次数：$run_count"